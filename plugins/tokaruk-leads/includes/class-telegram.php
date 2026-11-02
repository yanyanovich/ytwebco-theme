<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sends a lead to Telegram (Bot API sendMessage). Token and chat ID come from wp-config.php constants.
 * Without them, or when Telegram fails, a plain email to the site admin is the fallback.
 */
class Tokaruk_Leads_Telegram
{
    public static function is_configured()
    {
        return defined('TOKARUK_TG_TOKEN') && TOKARUK_TG_TOKEN && defined('TOKARUK_TG_CHAT_ID') && TOKARUK_TG_CHAT_ID;
    }

    /**
     * @param int   $post_id
     * @param array $lead same shape as Tokaruk_Leads::save()
     * @param array $sections optional grouping for the message: array<string heading, string[] field labels>
     * @return string "telegram" | "email" | "failed"
     */
    public static function deliver($post_id, array $lead, array $sections = array())
    {
        $text = self::format($post_id, $lead, $sections);

        if (self::is_configured()) {
            $response = wp_remote_post('https://api.telegram.org/bot' . TOKARUK_TG_TOKEN . '/sendMessage', array(
                'timeout' => 8,
                'body' => array(
                    'chat_id' => TOKARUK_TG_CHAT_ID,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => 'true',
                ),
            ));

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                return 'telegram';
            }
        }

        $sent = wp_mail(
            get_option('admin_email'),
            wp_strip_all_tags(get_the_title($post_id)),
            wp_strip_all_tags(str_replace(array('<b>', '</b>'), '', $text)) . "\n\n" . admin_url('post.php?post=' . $post_id . '&action=edit')
        );

        return $sent ? 'email' : 'failed';
    }

    private static function format($post_id, array $lead, array $sections)
    {
        $escape = function ($value) {
            return htmlspecialchars(is_array($value) ? implode(', ', $value) : (string)$value, ENT_NOQUOTES, 'UTF-8');
        };
        $fields = array_filter($lead['fields'], function ($value) {
            return $value !== '' && $value !== array();
        });

        $lines = array(sprintf(
            '<b>%s</b> · %s%s',
            $lead['type'] === 'brief' ? '📝 New brief' : '✉️ New request',
            in_array($lead['language'], array('uk', 'ua'), true) ? 'UA' : 'EN',
            !empty($lead['ref']) ? ' · ref: ' . $escape($lead['ref']) : ''
        ));

        $groups = $sections ?: array('' => array_keys($fields));
        foreach ($groups as $heading => $labels) {
            $group = array_intersect_key($fields, array_flip($labels));
            if (!$group) {
                continue;
            }
            $lines[] = '';
            if ($heading !== '') {
                $lines[] = '<b>' . $escape($heading) . '</b>';
            }
            foreach ($group as $label => $value) {
                $lines[] = $escape($label) . ': ' . $escape($value);
            }
        }

        if (!empty($lead['page'])) {
            $lines[] = '';
            $lines[] = $escape($lead['page']);
        }

        return implode("\n", $lines);
    }
}
