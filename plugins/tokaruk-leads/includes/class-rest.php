<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST endpoint for the project brief: POST /wp-json/tokaruk-leads/v1/brief
 * Nonce (X-WP-Nonce, wp_rest), honeypot, rate limit and server-side validation.
 *
 * Body (JSON): {
 *   language: "en" | "uk", ref?: string, page?: string, tl_website?: "" (honeypot),
 *   attribution?: object (UTM, referrer, trail… see setup-attribution.js),
 *   design_path?: "express" | "designer" (Website design page),
 *   name: string, email?: string, contact?: string, project_type?: string,
 *   steps: [{ title: string, fields: { [label]: string | string[] } }]
 * }
 */
class Tokaruk_Leads_Rest
{
    const NS = 'tokaruk-leads/v1';

    public static function register()
    {
        register_rest_route(self::NS, '/brief', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array(__CLASS__, 'brief'),
            'permission_callback' => array(__CLASS__, 'check_nonce'),
        ));
    }

    public static function check_nonce(WP_REST_Request $request)
    {
        return (bool)wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
    }

    public static function brief(WP_REST_Request $request)
    {
        $body = (array)$request->get_json_params();

        if (Tokaruk_Leads_Guard::is_honeypot_filled($body) || Tokaruk_Leads_Guard::is_rate_limited()) {
            return new WP_Error('tl_rejected', 'Rejected', array('status' => 429));
        }

        $language = sanitize_key($body['language'] ?? 'en');
        $name = sanitize_text_field($body['name'] ?? '');
        $email = sanitize_email($body['email'] ?? '');
        $contact = sanitize_text_field($body['contact'] ?? '');

        // Required: EN — name + email; UA — name + phone or Telegram
        $errors = array();
        if ($name === '') {
            $errors['name'] = 'required';
        }
        if (in_array($language, array('uk', 'ua'), true)) {
            if ($contact === '') {
                $errors['contact'] = 'required';
            }
            if (!empty($body['email']) && !is_email($email)) {
                $errors['email'] = 'invalid';
            }
        } elseif (!is_email($email)) {
            $errors['email'] = empty($body['email']) ? 'required' : 'invalid';
        }
        if ($errors) {
            return new WP_Error('tl_invalid', 'Invalid fields', array('status' => 422, 'fields' => $errors));
        }

        $fields = array();
        $sections = array();
        foreach (array_slice((array)($body['steps'] ?? array()), 0, 8) as $step) {
            $title = sanitize_text_field($step['title'] ?? '');
            foreach (array_slice((array)($step['fields'] ?? array()), 0, 20, true) as $label => $value) {
                $label = sanitize_text_field($label);
                $value = is_array($value)
                    ? array_values(array_filter(array_map('sanitize_text_field', array_slice($value, 0, 20))))
                    : mb_substr(sanitize_textarea_field((string)$value), 0, 4000);
                if ($label === '' || $value === '' || $value === array()) {
                    continue;
                }
                $fields[$label] = $value;
                $sections[$title][] = $label;
            }
        }

        // Website design page: the chosen design path arrives with the lead
        $design_path = sanitize_key($body['design_path'] ?? '');
        if (in_array($design_path, array('express', 'designer'), true)) {
            $fields['Design path'] = $design_path;
            $sections['Design'][] = 'Design path';
        }

        $lead = array(
            'type' => 'brief',
            'language' => $language,
            'name' => $name,
            'contact' => $email ?: $contact,
            'fields' => $fields,
            'ref' => sanitize_key($body['ref'] ?? ''),
            'page' => esc_url_raw($body['page'] ?? ''),
            'attribution' => Tokaruk_Leads_Attribution::build($body['attribution'] ?? array()),
        );

        $post_id = Tokaruk_Leads::save($lead);
        if (!$post_id) {
            return new WP_Error('tl_failed', 'Could not save', array('status' => 500));
        }

        Tokaruk_Leads::set_delivery($post_id, Tokaruk_Leads_Telegram::deliver($post_id, $lead, $sections));

        return rest_ensure_response(array('ok' => true));
    }
}
