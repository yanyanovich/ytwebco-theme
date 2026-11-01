<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Questions from the FAQ block(s) of a post, read straight from the block attributes
 * (schema is printed in <head>, before the blocks render).
 *
 * @return array<int, array{question: string, answer: string}>
 */
function theme_get_faq_items($post_id)
{
    $post = get_post($post_id);
    if (!$post || !has_block('acf/home-page-blocks-faq', $post)) {
        return array();
    }

    $items = array();
    foreach (parse_blocks($post->post_content) as $block) {
        if ($block['blockName'] !== 'acf/home-page-blocks-faq' || empty($block['attrs']['data'])) {
            continue;
        }
        $data = $block['attrs']['data'];
        $count = (int)($data['b_hp_faq_items'] ?? 0);
        for ($i = 0; $i < $count; $i++) {
            $question = trim(wp_strip_all_tags((string)($data["b_hp_faq_items_{$i}_question"] ?? '')));
            $answer = trim(wp_strip_all_tags((string)($data["b_hp_faq_items_{$i}_answer"] ?? '')));
            if ($question !== '' && $answer !== '') {
                $items[] = array('question' => $question, 'answer' => $answer);
            }
        }
    }

    return $items;
}

/**
 * schema.org Question entities for FAQPage.mainEntity.
 */
function theme_get_faq_schema_entities($post_id)
{
    return array_map(function ($item) {
        return array(
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => $item['answer'],
            ),
        );
    }, theme_get_faq_items($post_id));
}

// Yoast: the page's WebPage node also becomes a FAQPage (same pattern as Yoast's own FAQ block)
add_filter('wpseo_schema_graph', function ($graph) {
    if (!is_singular()) {
        return $graph;
    }

    $entities = theme_get_faq_schema_entities(get_queried_object_id());
    if (empty($entities)) {
        return $graph;
    }

    foreach ($graph as &$piece) {
        $types = (array)($piece['@type'] ?? array());
        if (in_array('WebPage', $types, true)) {
            $piece['@type'] = array_values(array_unique(array_merge($types, array('FAQPage'))));
            $piece['mainEntity'] = $entities;
            break;
        }
    }
    unset($piece);

    return $graph;
});

// Without Yoast: a standalone FAQPage
add_action('wp_head', function () {
    if (defined('WPSEO_VERSION') || !is_singular()) {
        return;
    }

    $entities = theme_get_faq_schema_entities(get_queried_object_id());
    if (empty($entities)) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $entities,
    );
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});

/**
 * Breadcrumb trail for inner pages; one source for the visible breadcrumbs and BreadcrumbList.
 * Work archive: Home / Work. Case page: Home / Work / {case}. Inner pages: Home / {page}.
 *
 * @return array<int, array{label: string, url: string}>
 */
function theme_get_breadcrumbs()
{
    $home = array('label' => theme_t('Home'), 'url' => function_exists('pll_home_url') ? pll_home_url() : home_url('/'));

    $work_page_id = theme_get_archive_page_id('case');
    $work = array(
        'label' => $work_page_id ? get_the_title($work_page_id) : theme_t('Work'),
        'url' => (string)get_post_type_archive_link('case'),
    );

    if (is_singular('case')) {
        return array($home, $work, array('label' => get_the_title(get_queried_object_id()), 'url' => get_permalink(get_queried_object_id())));
    }

    if (is_post_type_archive('case')) {
        return array($home, $work);
    }

    // Inner pages (Contact, Privacy, services): Home / {page}
    if (is_page() && !is_front_page()) {
        $page_id = get_queried_object_id();
        return array($home, array('label' => get_the_title($page_id), 'url' => get_permalink($page_id)));
    }

    return array();
}

// Yoast builds its BreadcrumbList from these links, so the schema matches what the page shows
add_filter('wpseo_breadcrumb_links', function ($links) {
    $crumbs = theme_get_breadcrumbs();

    if (empty($crumbs)) {
        return $links;
    }

    return array_map(function ($crumb) {
        return array('url' => $crumb['url'], 'text' => $crumb['label']);
    }, $crumbs);
});

// Without Yoast: a standalone BreadcrumbList
add_action('wp_head', function () {
    if (defined('WPSEO_VERSION')) {
        return;
    }

    $crumbs = theme_get_breadcrumbs();
    if (empty($crumbs)) {
        return;
    }

    $items = array();
    foreach ($crumbs as $position => $crumb) {
        $item = array('@type' => 'ListItem', 'position' => $position + 1, 'name' => $crumb['label']);
        if ($position < count($crumbs) - 1) {
            $item['item'] = $crumb['url'];
        }
        $items[] = $item;
    }

    $schema = array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items);
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});

