<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Blog: standard posts + categories, translated with Polylang.
 * URLs: /blog/{slug}/ and /en/blog/{slug}/ (permalink structure /blog/%postname%/), archives /blog/ and /en/blog/.
 * Page 1 of the archive = featured card + 9 cards (10 posts); further pages = 9 cards each, no featured card.
 */

const THEME_BLOG_FIRST_PAGE = 10;
const THEME_BLOG_PER_PAGE = 9;

/**
 * The blog archive has no WordPress page: Polylang cannot serve two pages with the same slug ("blog") in two languages,
 * so /blog/ and /en/blog/ are post type archive rules (flush permalinks once after deploy).
 */
add_filter('register_post_type_args', function ($args, $post_type) {
    if ($post_type === 'post') {
        $args['has_archive'] = true;
    }

    return $args;
}, 10, 2);

add_action('init', function () {
    add_rewrite_rule('^blog/page/([0-9]{1,})/?$', 'index.php?post_type=post&paged=$matches[1]', 'top');
    add_rewrite_rule('^blog/?$', 'index.php?post_type=post', 'top');

    // Polylang does not prefix custom rules: one pair per non-default language (/en/blog/)
    if (function_exists('pll_languages_list') && function_exists('pll_default_language')) {
        foreach (pll_languages_list(array('fields' => 'slug')) as $slug) {
            if ($slug === pll_default_language()) {
                continue;
            }
            add_rewrite_rule('^' . $slug . '/blog/page/([0-9]{1,})/?$', 'index.php?lang=' . $slug . '&post_type=post&paged=$matches[1]', 'top');
            add_rewrite_rule('^' . $slug . '/blog/?$', 'index.php?lang=' . $slug . '&post_type=post', 'top');
        }
    }
}, 20);

function theme_get_blog_url()
{
    $prefix = '';
    if (function_exists('pll_current_language') && function_exists('pll_default_language') && pll_current_language() !== pll_default_language()) {
        $prefix = pll_current_language() . '/';
    }

    return home_url('/' . $prefix . 'blog/');
}

/**
 * Number of published posts in the current language (0 hides the "Blog" links).
 */
function theme_blog_post_count()
{
    static $counts = array();

    $lang = function_exists('pll_current_language') ? (string)pll_current_language() : '';

    if (!isset($counts[$lang])) {
        $query = new WP_Query(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'no_found_rows' => false,
            'ignore_sticky_posts' => true,
        ));
        $counts[$lang] = (int)$query->found_posts;
    }

    return $counts[$lang];
}

// "Blog" in the header and footer (a custom menu link to /blog/) is hidden while the language has no posts
add_filter('wp_get_nav_menu_items', function ($items) {
    if (is_admin() || empty($items) || theme_blog_post_count() > 0) {
        return $items;
    }

    $blog = untrailingslashit(theme_get_blog_url());

    return array_values(array_filter($items, function ($item) use ($blog) {
        return untrailingslashit((string)$item->url) !== $blog;
    }));
}, 20);

/**
 * Archive pagination: page 1 holds 10 posts (featured + 9), the next pages 9 each.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !($query->is_post_type_archive('post') || $query->is_category())) {
        return;
    }

    $paged = max(1, (int)$query->get('paged'));

    if ($paged === 1) {
        $query->set('posts_per_page', THEME_BLOG_FIRST_PAGE);
        $query->set('offset', 0);
    } else {
        $query->set('posts_per_page', THEME_BLOG_PER_PAGE);
        $query->set('offset', THEME_BLOG_FIRST_PAGE + ($paged - 2) * THEME_BLOG_PER_PAGE);
    }

    $query->set('ignore_sticky_posts', true);
    $query->set('orderby', 'date');
    $query->set('order', 'DESC');
});

function theme_blog_max_pages($found)
{
    $found = (int)$found;

    if ($found <= THEME_BLOG_FIRST_PAGE) {
        return 1;
    }

    return 1 + (int)ceil(($found - THEME_BLOG_FIRST_PAGE) / THEME_BLOG_PER_PAGE);
}

/**
 * Reading time in minutes: the ACF value, otherwise 200 words per minute rounded up.
 */
function theme_get_reading_time($post_id)
{
    $value = function_exists('get_field') ? (int)get_field('reading_time', $post_id) : 0;
    if ($value > 0) {
        return $value;
    }

    $text = wp_strip_all_tags(strip_shortcodes((string)get_post_field('post_content', $post_id)));
    $words = preg_match_all('/[\p{L}\p{N}]+/u', $text);

    return max(1, (int)ceil($words / 200));
}

function theme_get_post_category($post_id)
{
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms) || is_wp_error($terms)) {
        return null;
    }

    return reset($terms);
}

/**
 * Date for cards and the meta row: "9 листопада 2026" (UA) / "Nov 9, 2026" (EN).
 */
function theme_blog_date($timestamp)
{
    $locale = function_exists('pll_current_language') ? (string)pll_current_language('locale') : get_locale();

    return strpos($locale, 'uk') === 0 ? mb_strtolower(wp_date('j F Y', $timestamp)) : wp_date('M j, Y', $timestamp);
}

/**
 * "Updated" date when ACF updated_date is set and differs from the published day, otherwise ''.
 */
function theme_get_updated_date($post_id)
{
    $value = function_exists('get_field') ? trim((string)get_field('updated_date', $post_id)) : '';
    $time = $value ? strtotime($value) : 0;

    if (!$time || wp_date('Y-m-d', $time) === get_the_date('Y-m-d', $post_id)) {
        return '';
    }

    return $value;
}

/**
 * Everything a blog card needs.
 */
function theme_get_post_card($post_id)
{
    $field = function ($name) use ($post_id) {
        return function_exists('get_field') ? trim((string)get_field($name, $post_id)) : '';
    };

    $category = theme_get_post_category($post_id);
    $cover_text = $field('cover_text');

    return array(
        'id' => $post_id,
        'title' => get_the_title($post_id),
        'url' => get_permalink($post_id),
        // The card text can differ from the article lead: the post excerpt wins when it is filled in
        'lead' => trim((string)get_post_field('post_excerpt', $post_id)) ?: $field('lead'),
        'date' => theme_blog_date(get_post_timestamp($post_id)),
        'iso' => get_the_date('c', $post_id),
        'minutes' => theme_get_reading_time($post_id),
        'category' => $category ? $category->name : '',
        'category_slug' => $category ? $category->slug : '',
        'cover_text' => $cover_text !== '' ? $cover_text : ($category ? $category->name : ''),
        'cover_caption' => $field('cover_caption'),
        'featured_number' => $field('featured_number'),
    );
}

/**
 * Short title for breadcrumbs: the part before the first colon ("How to speed up a WordPress site: 10 steps…").
 */
function theme_get_short_title($post_id)
{
    $title = get_the_title($post_id);
    $pos = strpos($title, ':');

    return $pos !== false && $pos > 12 ? trim(substr($title, 0, $pos)) : $title;
}

/**
 * "N min read" label with Ukrainian / English units.
 */
function theme_reading_time_label($minutes)
{
    return sprintf(theme_t('%s min read'), (int)$minutes);
}

/**
 * Adds ids to the H2 headings and returns them for the table of contents.
 * Anchors are "section-N" (headings are Cyrillic, so slugs would be percent-encoded).
 *
 * @return array{html: string, toc: array<int, array{id: string, title: string}>}
 */
function theme_prepare_article_content($html)
{
    $toc = array();
    $index = 0;

    $html = preg_replace_callback('#<h2([^>]*)>(.*?)</h2>#is', function ($match) use (&$toc, &$index) {
        $index++;
        $id = 'section-' . $index;
        $title = trim(wp_strip_all_tags($match[2]));
        $attrs = preg_replace('/\sid="[^"]*"/', '', $match[1]);
        $toc[] = array('id' => $id, 'title' => $title);

        return '<h2' . $attrs . ' id="' . esc_attr($id) . '">' . $match[2] . '</h2>';
    }, $html);

    // Code blocks: a header with the language (class language-xxx) and a copy button
    $html = preg_replace_callback('~<pre([^>]*class="[^"]*wp-block-code[^"]*"[^>]*)>(<code([^>]*)>)(.*?)</code></pre>~is', function ($match) {
        $language = preg_match('/language-([a-z0-9+-]+)/i', $match[3], $lang) ? strtoupper($lang[1]) : 'CODE';

        return '<div class="code-block"><div class="code-block__head mono"><span>' . esc_html($language) . '</span>'
            . '<button type="button" class="code-block__copy" data-copy-code data-copied="' . esc_attr(theme_t('Copied')) . '">' . esc_html(theme_t('Copy')) . '</button></div>'
            . '<pre' . $match[1] . ' tabindex="0">' . $match[2] . $match[4] . '</code></pre></div>';
    }, $html);

    return array('html' => $html, 'toc' => $toc);
}

/**
 * Related block: the service page from "related_service" plus 2 posts (same category first, then the latest).
 * Without a service, 3 posts.
 *
 * @return array{service: ?int, posts: int[]}
 */
function theme_get_related($post_id)
{
    $service_id = function_exists('get_field') ? (int)get_field('related_service', $post_id) : 0;
    $service_id = $service_id && get_post_status($service_id) === 'publish' ? $service_id : 0;
    $need = $service_id ? 2 : 3;

    $posts = array();
    $category = theme_get_post_category($post_id);

    if ($category) {
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => $need,
            'post__not_in' => array($post_id),
            'category__in' => array($category->term_id),
            'fields' => 'ids',
            'ignore_sticky_posts' => true,
        ));
    }

    if (count($posts) < $need) {
        $more = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => $need - count($posts),
            'post__not_in' => array_merge(array($post_id), $posts),
            'fields' => 'ids',
            'ignore_sticky_posts' => true,
        ));
        $posts = array_merge($posts, $more);
    }

    return array('service' => $service_id ?: null, 'posts' => array_map('intval', $posts));
}
