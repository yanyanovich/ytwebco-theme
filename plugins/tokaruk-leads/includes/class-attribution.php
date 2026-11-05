<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Where a lead came from: the browser sends UTM / click ids, referrer, landing page and the pages viewed
 * (assets/js setup-attribution.js); the server adds IP address and device. Everything is sanitised and capped —
 * it is visitor-supplied data and only ever shown to the site owner.
 *
 * The IP address and these tracking details are personal data: the Privacy policy must mention them.
 */
class Tokaruk_Leads_Attribution
{
    const TAGS = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_id', 'gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid', 'ref');

    /**
     * @param mixed $raw JSON string (contact form) or array (brief) from the browser
     * @return array sanitised client data + server data
     */
    public static function build($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode(wp_unslash($raw), true);
        }
        $raw = is_array($raw) ? $raw : array();

        $data = array(
            'first' => self::tags($raw['first'] ?? array()),
            'last' => self::tags($raw['last'] ?? array()),
            'landing' => self::text($raw['landing'] ?? '', 300),
            'referrer' => esc_url_raw(self::text($raw['referrer'] ?? '', 300)),
            'trail' => self::trail($raw['trail'] ?? array()),
            'submit_page' => self::text($raw['submit_page'] ?? '', 200),
            'seconds_on_site' => max(0, min(86400, (int)($raw['seconds_on_site'] ?? 0))),
            'screen' => self::text($raw['screen'] ?? '', 20),
            'viewport' => self::text($raw['viewport'] ?? '', 20),
            'timezone' => self::text($raw['timezone'] ?? '', 60),
            'browser_language' => self::text($raw['language'] ?? '', 20),
            'ga_client_id' => preg_replace('/[^0-9.]/', '', (string)($raw['ga_client_id'] ?? '')),
        );

        $user_agent = self::text($_SERVER['HTTP_USER_AGENT'] ?? '', 400);
        $data['ip'] = self::ip();
        $data['device'] = self::device($user_agent);
        $data['user_agent'] = $user_agent;

        return array_filter($data, function ($value) {
            return $value !== '' && $value !== array() && $value !== 0;
        });
    }

    /** Flat, labelled list for the admin screen and the Telegram message */
    public static function summary(array $attr)
    {
        $tags = array_merge((array)($attr['first'] ?? array()), (array)($attr['last'] ?? array()));
        $source = $tags['utm_source'] ?? ($tags['ref'] ?? '');
        if (!$source && !empty($tags['gclid'])) {
            $source = 'google (gclid)';
        }
        if (!$source && !empty($attr['referrer'])) {
            $source = (string)wp_parse_url($attr['referrer'], PHP_URL_HOST) . ' (referrer)';
        }

        $rows = array(
            'Source' => $source ?: 'direct',
            'Medium' => $tags['utm_medium'] ?? '',
            'Campaign' => $tags['utm_campaign'] ?? '',
            'Keyword' => $tags['utm_term'] ?? '',
            'Ad content' => $tags['utm_content'] ?? '',
            'Referrer' => $attr['referrer'] ?? '',
            'Landing page' => $attr['landing'] ?? '',
            'Path' => self::trail_text($attr['trail'] ?? array()),
            'Time on site' => !empty($attr['seconds_on_site']) ? self::duration((int)$attr['seconds_on_site']) : '',
            'Device' => $attr['device'] ?? '',
            'Screen' => trim(($attr['screen'] ?? '') . (isset($attr['viewport']) ? ' (viewport ' . $attr['viewport'] . ')' : '')),
            'Browser language' => $attr['browser_language'] ?? '',
            'Timezone' => $attr['timezone'] ?? '',
            'IP' => $attr['ip'] ?? '',
            'GA client ID' => $attr['ga_client_id'] ?? '',
        );
        foreach (array('gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid') as $id) {
            if (!empty($tags[$id])) {
                $rows[strtoupper($id)] = $tags[$id];
            }
        }

        return array_filter($rows, function ($value) {
            return $value !== '';
        });
    }

    private static function trail_text(array $trail)
    {
        $steps = array();
        foreach ($trail as $step) {
            $steps[] = $step['p'] . ' (' . self::duration((int)$step['t']) . ')';
        }
        return implode(' → ', $steps);
    }

    private static function duration($seconds)
    {
        return $seconds >= 60 ? floor($seconds / 60) . 'm ' . ($seconds % 60) . 's' : $seconds . 's';
    }

    private static function text($value, $max)
    {
        return mb_substr(sanitize_text_field(is_scalar($value) ? (string)$value : ''), 0, $max);
    }

    private static function tags($raw)
    {
        $tags = array();
        foreach (self::TAGS as $name) {
            $value = is_array($raw) ? self::text($raw[$name] ?? '', 120) : '';
            if ($value !== '') {
                $tags[$name] = $value;
            }
        }
        return $tags;
    }

    private static function trail($raw)
    {
        $trail = array();
        foreach (array_slice(is_array($raw) ? $raw : array(), 0, 25) as $step) {
            $path = self::text($step['p'] ?? '', 200);
            if ($path !== '') {
                $trail[] = array('p' => $path, 't' => max(0, min(86400, (int)($step['t'] ?? 0))));
            }
        }
        return $trail;
    }

    /** Visitor IP; behind Cloudflare / a proxy the forwarded headers are used (informational only, not trusted for security) */
    public static function ip()
    {
        foreach (array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key) {
            if (empty($_SERVER[$key])) {
                continue;
            }
            $candidate = trim(explode(',', (string)$_SERVER[$key])[0]);
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }
        return '';
    }

    /** "Chrome 130 · macOS · desktop" from the User-Agent */
    private static function device($ua)
    {
        $browser = 'Unknown browser';
        foreach (array('Edg' => 'Edge', 'OPR' => 'Opera', 'SamsungBrowser' => 'Samsung Internet', 'Firefox' => 'Firefox', 'CriOS' => 'Chrome', 'FxiOS' => 'Firefox', 'Chrome' => 'Chrome', 'Version' => 'Safari') as $token => $name) {
            if (preg_match('~' . $token . '/([0-9]+)~', $ua, $m)) {
                $browser = $name . ' ' . $m[1];
                break;
            }
        }
        $os = 'Unknown OS';
        foreach (array('Windows' => 'Windows', 'Android' => 'Android', 'iPhone' => 'iOS', 'iPad' => 'iPadOS', 'Mac OS X' => 'macOS', 'CrOS' => 'ChromeOS', 'Linux' => 'Linux') as $token => $name) {
            if (stripos($ua, $token) !== false) {
                $os = $name;
                break;
            }
        }
        $type = preg_match('~iPad|Tablet~i', $ua) ? 'tablet' : (preg_match('~Mobi|iPhone|Android.+Mobile~i', $ua) ? 'mobile' : 'desktop');

        return $browser . ' · ' . $os . ' · ' . $type;
    }
}
