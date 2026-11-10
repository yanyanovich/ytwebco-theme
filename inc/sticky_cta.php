<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sticky mobile CTA bar (≤767px, whole site). Markup is printed in footer.php by
 * template-parts/partials/sticky-cta.php; behaviour lives in setup-sticky-cta.js.
 * Not rendered at all on the brief and contact pages (UA + EN), desktop never shows it (CSS).
 */
function theme_sticky_cta_enabled()
{
    static $enabled = null;

    if ($enabled !== null) {
        return $enabled;
    }

    if (is_404() || is_search() || !is_page()) {
        return $enabled = !is_404() && !is_search();
    }

    // Brief + contact pages with all their translations
    $excluded = array();
    foreach (array('bryf', 'brief', 'kontakty', 'contact') as $path) {
        $page = get_page_by_path($path);
        if (!$page) {
            continue;
        }
        $excluded[] = (int)$page->ID;
        if (function_exists('pll_get_post_translations')) {
            $excluded = array_merge($excluded, array_map('intval', array_values(pll_get_post_translations($page->ID))));
        }
    }

    return $enabled = !in_array((int)get_queried_object_id(), $excluded, true);
}

add_filter('body_class', function ($classes) {
    if (theme_sticky_cta_enabled()) {
        $classes[] = 'has-sticky-cta';
    }

    return $classes;
});
