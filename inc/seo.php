<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO glue around Yoast (titles, descriptions and share cards come from the page's own Yoast fields).
 */

/**
 * The Work archive (/work/, /en/work/) is a post type archive, so Yoast would use its generic "Cases Archive"
 * settings. Its real SEO fields live on the archive page that holds its content: use those.
 */
function theme_get_archive_seo_value($meta_key)
{
    if (!is_post_type_archive('case') || !function_exists('theme_get_archive_page_id')) {
        return '';
    }

    $page_id = theme_get_archive_page_id('case');

    return $page_id ? (string)get_post_meta($page_id, $meta_key, true) : '';
}

foreach (array(
    'wpseo_title' => '_yoast_wpseo_title',
    'wpseo_metadesc' => '_yoast_wpseo_metadesc',
    'wpseo_opengraph_desc' => '_yoast_wpseo_metadesc',
    'wpseo_twitter_description' => '_yoast_wpseo_metadesc',
    'wpseo_opengraph_image' => '_yoast_wpseo_opengraph-image',
    'wpseo_twitter_image' => '_yoast_wpseo_opengraph-image',
) as $filter => $meta_key) {
    add_filter($filter, function ($value) use ($meta_key) {
        return theme_get_archive_seo_value($meta_key) ?: $value;
    });
}

/**
 * Share cards: the page title without the brand gets "| YT Web Co." (SEO titles stay short for the 60-character limit).
 */
foreach (array('wpseo_opengraph_title', 'wpseo_twitter_title') as $filter) {
    add_filter($filter, function ($title) {
        $title = theme_get_archive_seo_value('_yoast_wpseo_title') ?: $title;

        if (!is_string($title) || $title === '' || stripos($title, 'YT Web Co') !== false) {
            return $title;
        }

        return $title . ' | YT Web Co.';
    });
}

/**
 * Archives nobody needs: date archives and post formats. The author page is a designed profile of the single author
 * (author.php); its URL carries the slug "yan-tokaruk" (inc/search_author.php), not the login e-mail.
 */
add_filter('option_wpseo_titles', function ($titles) {
    if (is_array($titles)) {
        $titles['disable-author'] = false;
        $titles['disable-date'] = true;
        $titles['disable-post_format'] = true;
    }

    return $titles;
});
