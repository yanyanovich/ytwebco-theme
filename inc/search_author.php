<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Site search (/?s=, /en/?s=) and the author page (/blog/author/yan-tokaruk/).
 * Search looks through articles, cases and pages of the current language; the results page is designed in
 * search.php. The author page is a profile for the single author, listing the articles.
 */

/* ---------- search ---------- */

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $query->set('post_type', array('post', 'case', 'page'));
    $query->set('posts_per_page', 10);
    $query->set('ignore_sticky_posts', true);
});

/**
 * Wraps the matches of the query in <mark> (text is escaped first).
 */
function theme_search_highlight($text, $query)
{
    $text = esc_html($text);
    $terms = array_filter(array_unique(preg_split('/\s+/u', trim((string)$query))), function ($term) {
        return mb_strlen($term) >= 2;
    });

    if (empty($terms)) {
        return $text;
    }

    $pattern = '/(' . implode('|', array_map(function ($term) {
        return preg_quote(esc_html($term), '/');
    }, $terms)) . ')/iu';

    return preg_replace($pattern, '<mark>$1</mark>', $text);
}

/**
 * One search result: type label, title, short text, meta line.
 */
function theme_get_search_result($post)
{
    $post = get_post($post);
    $type = $post->post_type;
    $excerpt = '';
    $meta = '';

    if ($type === 'post') {
        $card = theme_get_post_card($post->ID);
        $label = theme_t('Article');
        $excerpt = $card['lead'];
        $meta = $card['date'] . ' · ' . theme_reading_time_label($card['minutes']);
    } elseif ($type === 'case') {
        $card = theme_get_case_card($post->ID);
        $label = theme_t('Case');
        $excerpt = $card['excerpt'];
        $meta = $card['metric'];
    } else {
        $label = theme_t('Page');
        $excerpt = (string)get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
    }

    if ($excerpt === '') {
        // blocks keep their text in comments, so take whatever plain text is left
        $plain = wp_strip_all_tags(preg_replace('/<!--.*?-->/s', '', (string)$post->post_content));
        $excerpt = wp_trim_words($plain, 28, '…');
    }

    return array(
        'type' => $type,
        'label' => $label,
        'title' => get_the_title($post),
        'url' => get_permalink($post),
        'excerpt' => wp_trim_words($excerpt, 32, '…'),
        'meta' => $meta,
    );
}

add_filter('wpseo_title', function ($title) {
    if (is_search()) {
        return trim(get_search_query(false)) === ''
            ? theme_t('Search the site') . ' | YT Web Co.'
            : sprintf(theme_t('Search: %s | YT Web Co.'), get_search_query());
    }

    return $title;
});

// Search results are never indexed
add_filter('wpseo_robots', function ($robots) {
    return is_search() ? 'noindex, follow' : $robots;
});

/* ---------- author ---------- */

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_author()) {
        return;
    }

    $query->set('post_type', 'post');
    $query->set('posts_per_page', 9);
    $query->set('ignore_sticky_posts', true);
});

/**
 * The owner's account has the e-mail as login, display name and URL slug. One time (on the first admin page view):
 * display name "Yan Tokaruk", URL slug "yan-tokaruk" for the first administrator, so the author page URL does not
 * show an e-mail and the REST users list does not either.
 */
add_action('admin_init', function () {
    if (get_option('theme_author_profile_v1')) {
        return;
    }

    $admins = get_users(array('role' => 'administrator', 'orderby' => 'ID', 'order' => 'ASC', 'number' => 1));
    if (empty($admins)) {
        return;
    }

    $user = $admins[0];
    wp_update_user(array(
        'ID' => $user->ID,
        'user_nicename' => 'yan-tokaruk',
        'display_name' => 'Yan Tokaruk',
        'first_name' => 'Yan',
        'last_name' => 'Tokaruk',
    ));
    update_option('theme_author_profile_v1', 1, false);
});

add_filter('wpseo_title', function ($title) {
    return is_author() ? theme_t('Yan Tokaruk, WordPress developer | YT Web Co.') : $title;
});

add_filter('wpseo_metadesc', function ($description) {
    return is_author() ? theme_t('Yan Tokaruk, WordPress developer from Lutsk: articles on speed, WooCommerce and integrations, with numbers from real projects.') : $description;
});

// The author is a Person with the public profiles (Upwork, LinkedIn, GitHub, Telegram) and a profile page
add_filter('wpseo_schema_person', function ($data) {
    if (!is_array($data)) {
        return $data;
    }

    $same = array();
    foreach (theme_get_socials() as $social) {
        if ($social['url'] && $social['url'] !== '#' && preg_match('#^https?://#', $social['url'])) {
            $same[] = $social['url'];
        }
    }
    if ($same) {
        $data['sameAs'] = $same;
    }
    $data['url'] = home_url('/');

    return $data;
}, 20);
