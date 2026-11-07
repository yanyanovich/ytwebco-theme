<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact Form 7 integration: CF7 keeps the form markup, its REST submit (nonce) and field validation;
 * this adds the honeypot + rate limit, saves the lead, sends it to Telegram and skips CF7's own email.
 * The form shows success only after the lead is saved.
 */
class Tokaruk_Leads_CF7
{
    /** Field name → label in the lead and the Telegram message */
    const LABELS = array(
        'your-name' => 'Name',
        'your-email' => 'Email',
        'your-contact' => 'Phone / Telegram',
        'project-type' => 'Project type',
        'budget' => 'Budget',
        'your-message' => 'Message',
    );

    /** @var int lead saved during the current submission */
    private static $lead_id = 0;

    public static function init()
    {
        if (!defined('WPCF7_VERSION')) {
            return;
        }

        add_filter('wpcf7_form_elements', array(__CLASS__, 'add_honeypot'));
        add_filter('wpcf7_spam', array(__CLASS__, 'spam'), 20);
        add_action('wpcf7_before_send_mail', array(__CLASS__, 'save'), 10, 3);
        add_filter('wpcf7_skip_mail', array(__CLASS__, 'skip_mail'));
    }

    public static function add_honeypot($html)
    {
        return $html . Tokaruk_Leads_Guard::honeypot_html();
    }

    public static function spam($spam)
    {
        if ($spam) {
            return true;
        }

        return Tokaruk_Leads_Guard::is_honeypot_filled($_POST) || Tokaruk_Leads_Guard::is_rate_limited();
    }

    /**
     * @param WPCF7_ContactForm $contact_form
     * @param bool              $abort
     * @param WPCF7_Submission  $submission
     */
    public static function save($contact_form, &$abort, $submission)
    {
        $posted = (array)$submission->get_posted_data();
        $fields = array();

        foreach ($posted as $name => $value) {
            if (strpos($name, '_') === 0 || $name === Tokaruk_Leads_Guard::HONEYPOT || $name === 'yt_attr') {
                continue;
            }
            $value = is_array($value) ? array_values(array_filter(array_map('sanitize_text_field', $value))) : sanitize_textarea_field((string)$value);
            $label = self::LABELS[$name] ?? $name;
            $fields[$label] = is_array($value) && count($value) === 1 ? $value[0] : $value;
        }

        // The page language when CF7 knows the page; forms rendered by a block (homepage) have no container
        // post, so the form's own locale decides (UA forms are uk). Stored as "en" / "uk", like the brief.
        $page_id = (int)$submission->get_meta('container_post_id');
        $language = $page_id && function_exists('pll_get_post_language') ? (string)pll_get_post_language($page_id, 'locale') : '';
        $language = strtolower(substr($language ?: (string)$contact_form->locale(), 0, 2));

        $lead = array(
            'type' => 'contact',
            'language' => $language === 'uk' ? 'uk' : 'en',
            'name' => (string)($fields['Name'] ?? ''),
            'contact' => (string)($fields['Email'] ?? $fields['Phone / Telegram'] ?? ''),
            'fields' => $fields,
            'page' => $page_id ? get_permalink($page_id) : (string)$submission->get_meta('url'),
            'attribution' => Tokaruk_Leads_Attribution::build($posted['yt_attr'] ?? ''),
        );

        self::$lead_id = Tokaruk_Leads::save($lead);

        if (!self::$lead_id) {
            $abort = true;
            $submission->set_response($contact_form->message('mail_sent_ng'));
            return;
        }

        Tokaruk_Leads::set_delivery(self::$lead_id, Tokaruk_Leads_Telegram::deliver(self::$lead_id, $lead));
    }

    /**
     * The lead is stored and delivered by the plugin, so CF7 does not send its own email.
     */
    public static function skip_mail($skip)
    {
        return self::$lead_id ? true : $skip;
    }
}
