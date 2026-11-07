<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Every front-end link gets a title attribute (own title, aria-label, image alt or the visible link text, in that order).
 * Done once on the final HTML so menus, blocks, partials and editor content are all covered and always match the page language.
 */
function theme_add_link_titles($html)
{
    return preg_replace_callback(
        '#(<script\b.*?</script>|<style\b.*?</style>|<textarea\b.*?</textarea>)|<a\b([^>]*)>(.*?)</a>#is',
        function ($m) {
            if (!empty($m[1])) {
                return $m[0]; // leave scripts, styles and textareas untouched
            }

            $attrs = $m[2];
            if (preg_match('/\stitle\s*=/i', $attrs)) {
                return $m[0];
            }

            $title = '';
            if (preg_match('/\saria-label\s*=\s*"([^"]*)"/i', $attrs, $a)) {
                $title = html_entity_decode($a[1], ENT_QUOTES, 'UTF-8');
            }
            if ($title === '' && preg_match('/<(\w+)\b[^>]*class="[^"]*[-_]title[^"]*"[^>]*>(.*?)<\/\1>/is', $m[3], $t)) {
                $title = html_entity_decode(wp_strip_all_tags($t[2]), ENT_QUOTES, 'UTF-8'); // card / menu link: its own heading
            }
            if ($title === '') {
                $title = html_entity_decode(wp_strip_all_tags($m[3]), ENT_QUOTES, 'UTF-8');
            }
            if ($title === '' && preg_match('/<img\b[^>]*\salt\s*=\s*"([^"]*)"/i', $m[3], $i)) {
                $title = html_entity_decode($i[1], ENT_QUOTES, 'UTF-8');
            }
            $title = trim(preg_replace('/\s+/u', ' ', $title));
            if ($title === '') {
                return $m[0];
            }
            if (mb_strlen($title) > 110) {
                $title = rtrim(mb_substr($title, 0, 107)) . '…';
            }

            return '<a' . $attrs . ' title="' . esc_attr($title) . '">' . $m[3] . '</a>';
        },
        $html
    );
}

add_action('template_redirect', function () {
    if (is_admin() || is_feed() || is_embed() || is_robots() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    ob_start('theme_add_link_titles');
}, 0);
