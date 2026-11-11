<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Spam guard shared by the contact forms and the brief: a honeypot field, a per-IP rate limit and a spam score.
 *
 * The score looks at what bots do and people do not: they post straight to the form endpoint without running the
 * site's JavaScript (no browser data in the lead), put the site's own name into the sender address, pitch "get your site
 * into the Google index" and drop bare links to throw-away domains. A lead that reaches SPAM_SCORE is still stored
 * (private, marked) but never sent to Telegram or e-mail; a lower score is delivered with a warning line.
 */
class Tokaruk_Leads_Guard
{
    const HONEYPOT = 'tl_website';
    const LIMIT = 5;          // submissions …
    const WINDOW = 600;       // … per 10 minutes per IP
    const SPAM_SCORE = 3;     // stored but not delivered
    const BRAND = 'ytwebco';

    const PHRASES = array(
        'search index', 'google search index', 'google\'s search', 'search results', 'indexregister', 'domainsubmit',
        'submit your site', 'seo ranking', 'first page of google', 'backlink', 'guest post', 'rank #1', 'link building',
        'casino', 'viagra', 'bitcoin', 'crypto', 'казино', 'ставки на спорт', 'крипто', 'позика',
    );

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

    /**
     * Scores a lead. $lead has the shape of Tokaruk_Leads::save().
     *
     * @return array{score: int, reasons: string[]}
     */
    public static function analyze(array $lead)
    {
        $score = 0;
        $reasons = array();
        $add = function ($points, $reason) use (&$score, &$reasons) {
            $score += $points;
            $reasons[] = $reason . ' (+' . $points . ')';
        };

        $attr = (array)($lead['attribution'] ?? array());
        $text = trim(implode(' ', array_map(function ($value) {
            return is_array($value) ? implode(' ', $value) : (string)$value;
        }, array_merge((array)($lead['fields'] ?? array()), array($lead['name'] ?? '', $lead['contact'] ?? '')))));
        $lower = mb_strtolower($text);

        // the site's JavaScript sends the screen size, time on site and pages with every real submission
        if (empty($attr['screen']) && empty($attr['trail']) && empty($attr['submit_page'])) {
            $add(3, 'no browser data: the form was posted without the site script');
        } elseif (isset($attr['seconds_on_site']) && (int)$attr['seconds_on_site'] < 3) {
            $add(2, 'sent within 3 seconds of arriving');
        }

        // the site's own name in the sender address: "domains@search-ytwebco.com"
        $email = (string)($lead['contact'] ?? '');
        if (strpos($email, '@') !== false) {
            $domain = strtolower(substr(strrchr($email, '@'), 1));
            if (strpos($domain, self::BRAND) !== false && $domain !== self::BRAND . '.com') {
                $add(3, 'sender address imitates the site (' . $domain . ')');
            }
        }

        // pitches
        $hit = 0;
        foreach (self::PHRASES as $phrase) {
            if (strpos($lower, $phrase) !== false) {
                $hit++;
            }
        }
        if ($hit) {
            $add(min(4, 2 + $hit), 'spam phrases (' . $hit . ')');
        }

        if (preg_match('/\b' . self::BRAND . '\.(com|ua|net)\b/i', $text)) {
            $add(2, 'names the site itself in the message');
        }

        if (preg_match('/\b[a-z0-9-]{2,}\.(live|site|top|xyz|click|icu|buzz|online|link|cfd|sbs|shop|store)\b/i', $text)) {
            $add(2, 'bare link to a throw-away domain');
        }

        if (preg_match_all('~https?://~i', $text) > 2) {
            $add(1, 'more than two links');
        }

        return array('score' => $score, 'reasons' => $reasons);
    }
}
