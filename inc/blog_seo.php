<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Blog SEO (brief §6): BlogPosting schema, archive and category meta, hreflang only between existing translations,
 * sitemap entries, noindex for thin category archives, a reminder to link every post to a service page.
 * Titles and descriptions of posts come from Yoast (per post); author and date archives are off (inc/seo.php).
 */

/* ---------- schema ---------- */

// Posts are BlogPosting, not Article
add_filter('wpseo_schema_article_type', function ($type, $indexable = null) {
    if ($indexable && $indexable->object_type === 'post' && $indexable->object_sub_type === 'post') {
        return 'BlogPosting';
    }

    return $type;
}, 10, 2);

/**
 * dateModified = "updated_date" when set, otherwise the published date.
 */
function theme_schema_modified($post_id, $published)
{
    $updated = function_exists('get_field') ? trim((string)get_field('updated_date', $post_id)) : '';

    return $updated ? $updated . 'T00:00:00+00:00' : $published;
}

add_filter('wpseo_schema_article', function ($data) {
    if (!is_singular('post')) {
        return $data;
    }

    $post_id = get_queried_object_id();
    $data['dateModified'] = theme_schema_modified($post_id, $data['datePublished'] ?? '');

    if (isset($data['author']) && is_array($data['author'])) {
        $data['author']['name'] = 'Yan Tokaruk';
    }

    // publisher: the organisation (YT Web Co.), the same node the WebSite piece points to
    $data['publisher'] = array('@id' => home_url('/#organization'));

    // the generated OG image is the article image
    $og = get_post_meta($post_id, '_yt_og_image', true);
    if ($og) {
        $data['image'] = array('@type' => 'ImageObject', 'url' => $og, 'width' => 1200, 'height' => 630);
        $data['thumbnailUrl'] = $og;
    }

    return $data;
});

add_filter('wpseo_schema_webpage', function ($data) {
    if (is_singular('post')) {
        $data['dateModified'] = theme_schema_modified(get_queried_object_id(), $data['datePublished'] ?? '');
    }

    return $data;
});

// The author is a person with a name, not the login e-mail; no Gravatar image (it would expose the e-mail hash)
add_filter('wpseo_schema_person', function ($data) {
    if (is_array($data)) {
        $data['name'] = 'Yan Tokaruk';
        $data['jobTitle'] = 'WordPress developer';
        unset($data['image']);
    }

    return $data;
});

/* ---------- languages with posts ---------- */

/**
 * @return array<string, array{url: string, lastmod: string}> language slug => blog URL + last modified (GMT)
 */
function theme_blog_languages()
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $cache = array();
    if (!function_exists('pll_languages_list')) {
        return $cache;
    }

    $default = pll_default_language();
    foreach (pll_languages_list(array('fields' => 'slug')) as $slug) {
        $latest = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'modified',
            'order' => 'DESC',
            'lang' => $slug,
            'ignore_sticky_posts' => true,
        ));
        if (empty($latest)) {
            continue;
        }
        $cache[$slug] = array(
            'url' => home_url('/' . ($slug === $default ? '' : $slug . '/') . 'blog/'),
            'lastmod' => get_post_modified_time('c', true, $latest[0]),
        );
    }

    return $cache;
}

/* ---------- blog archive (a post type archive: Yoast does not print tags for it) ---------- */

function theme_is_blog_archive()
{
    return is_post_type_archive('post') && !is_category();
}

add_filter('pre_get_document_title', function ($title) {
    if (!theme_is_blog_archive()) {
        return $title;
    }

    $paged = max(1, (int)get_query_var('paged'));
    $text = theme_t('Blog: notes on fast WordPress sites | YT Web Co.');

    return $paged > 1 ? sprintf('%s — %s', $text, sprintf(theme_t('Page %1$s of %2$s'), $paged, theme_blog_max_pages($GLOBALS['wp_query']->found_posts))) : $text;
}, 99);

add_action('wp_head', function () {
    if (!theme_is_blog_archive()) {
        return;
    }

    $lang = function_exists('pll_current_language') ? (string)pll_current_language() : 'ua';
    $paged = max(1, (int)get_query_var('paged'));
    $url = $paged > 1 ? get_pagenum_link($paged) : theme_get_blog_url();
    $title = wp_get_document_title();
    $description = theme_t('Practical notes on speed, WooCommerce and integrations, with numbers from real projects.');
    $image = function_exists('theme_og_blog_image') ? theme_og_blog_image($lang) : '';
    $locale = $lang === 'en' ? 'en_US' : 'uk_UA';

    echo "\n<!-- Blog archive SEO -->\n";
    echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";

    echo '<meta property="og:locale" content="' . esc_attr($locale) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="YT Web Co." />' . "\n";
    if ($image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
        echo '<meta property="og:image:width" content="1200" />' . "\n" . '<meta property="og:image:height" content="630" />' . "\n";
        echo '<meta property="og:image:type" content="image/png" />' . "\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
    if ($image) {
        echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
    }

    $crumbs = array();
    foreach (theme_get_breadcrumbs() as $position => $crumb) {
        $crumbs[] = array('@type' => 'ListItem', 'position' => $position + 1, 'name' => $crumb['label'], 'item' => $crumb['url']);
    }
    $graph = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'Blog',
                '@id' => theme_get_blog_url() . '#blog',
                'url' => theme_get_blog_url(),
                'name' => theme_t('Notes on fast WordPress sites'),
                'description' => $description,
                'inLanguage' => $lang === 'en' ? 'en' : 'uk',
                'author' => array('@type' => 'Person', 'name' => 'Yan Tokaruk'),
                'publisher' => array('@type' => 'Organization', 'name' => 'YT Web Co.', 'url' => home_url('/')),
            ),
            array('@type' => 'BreadcrumbList', 'itemListElement' => $crumbs),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}, 2);

// hreflang of the archives (printed by Polylang): only languages that have posts; a single language gets none
add_filter('pll_rel_hreflang_attributes', function ($hreflangs) {
    if (!theme_is_blog_archive() || !is_array($hreflangs)) {
        return $hreflangs;
    }

    $with_posts = theme_blog_languages();
    $map = array('uk' => 'ua', 'ua' => 'ua', 'en' => 'en');
    $kept = array();
    foreach ($hreflangs as $code => $url) {
        if ($code === 'x-default' || isset($with_posts[$map[$code] ?? $code])) {
            $kept[$code] = $url;
        }
    }

    return count($with_posts) > 1 ? $kept : array();
});

/* ---------- category archives ---------- */

add_filter('wpseo_title', function ($title) {
    if (is_category() && ($term = get_queried_object())) {
        return sprintf(theme_t('%s: Blog | YT Web Co.'), $term->name);
    }

    return $title;
});

add_filter('wpseo_metadesc', function ($description) {
    if (is_category() && ($term = get_queried_object())) {
        return $term->description ?: sprintf(theme_t('Articles on “%s” from the YT Web Co. blog: practical notes with numbers from real projects.'), $term->name);
    }

    return $description;
});

// A category with fewer than 3 posts is thin: noindex until it grows, and out of the sitemap
add_filter('wpseo_robots', function ($robots) {
    if (is_category() && ($term = get_queried_object()) && (int)$term->count < 3) {
        return 'noindex, follow';
    }

    return $robots;
});

add_filter('wpseo_exclude_from_sitemap_by_term_ids', function ($ids) {
    $terms = get_terms(array('taxonomy' => 'category', 'hide_empty' => false, 'lang' => ''));

    if (is_wp_error($terms)) {
        return $ids;
    }

    foreach ($terms as $term) {
        if ((int)$term->count < 3) {
            $ids[] = (int)$term->term_id;
        }
    }

    return $ids;
});

/* ---------- sitemap: the archive pages (/blog/, /en/blog/) next to the posts ---------- */

add_filter('wpseo_sitemap_post_type_first_links', function ($links, $post_type) {
    if ($post_type !== 'post') {
        return $links;
    }

    $known = wp_list_pluck($links, 'loc');
    foreach (theme_blog_languages() as $info) {
        if (!in_array($info['url'], $known, true)) {
            $links[] = array('loc' => $info['url'], 'mod' => $info['lastmod'], 'chf' => 'daily', 'pri' => 1);
        }
    }

    return $links;
}, 10, 2);

/* ---------- every post links to a service page (brief §6) ---------- */

add_action('admin_notices', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'post') {
        return;
    }

    $post_id = get_the_ID();
    if (!$post_id || !get_post_field('post_content', $post_id)) {
        return;
    }

    $related = function_exists('get_field') ? (int)get_field('related_service', $post_id) : 0;
    $in_text = (bool)preg_match('~href="[^"]*/(posluhy|rozrobka-saitiv|rozrobka-internet-magazynu|pryskorennia-ta-seo|intehratsii-ta-telegram-boty|dyzain-saitu|services)[/"]~', get_post_field('post_content', $post_id));

    if (!$related || !$in_text) {
        echo '<div class="notice notice-info"><p><strong>Internal linking:</strong> every post should link to at least one service page, both in the text and in the “Related service” field.'
            . ($related ? '' : ' “Related service” is empty.') . ($in_text ? '' : ' No link to a service page in the text yet.') . '</p></div>';
    }
});
