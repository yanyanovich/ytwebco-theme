<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Spam guard shared by the contact forms and the brief: a honeypot field and a per-IP rate limit.
 */
class Tokaruk_Leads_Guard
{
    const HONEYPOT = 'tl_website';
    const LIMIT = 5;          // submissions …
    const WINDOW = 600;       // … per 10 minutes per IP

    /**
     * Visually hidden text field that people never see or fill; bots do.
     */
    public static function honeypot_html()
    {
        return sprintf(
            '<span class="tl-hp" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"><label>Website<input type="text" name="%s" value="" tabindex="-1" autocomplete="off"></label></span>',
            esc_attr(self::HONEYPOT)
        );
    }

    public static function is_honeypot_filled(array $data)
    {
        return !empty($data[self::HONEYPOT]);
    }

    /**
     * Counts a submission for this IP; true when the limit is exceeded.
     */
    public static function is_rate_limited()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $key = 'tl_rate_' . md5($ip . wp_salt('nonce'));
        $count = (int)get_transient($key);

        if ($count >= self::LIMIT) {
            return true;
        }

        set_transient($key, $count + 1, self::WINDOW);

        return false;
    }
}
