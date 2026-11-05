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
 * Service pages: Home / Services (hub) / {page}.
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

    // Inner pages (Contact, Privacy, Services hub): Home / {page}; service pages: Home / Services / {page}
    if (is_page() && !is_front_page()) {
        $page_id = get_queried_object_id();
        $crumbs = array($home);
        $hub_id = theme_get_services_hub_id();
        if ($hub_id && $hub_id !== $page_id && has_block('acf/other-blocks-service-hero', $page_id)) {
            $crumbs[] = array('label' => get_the_title($hub_id), 'url' => get_permalink($hub_id));
        }
        $crumbs[] = array('label' => get_the_title($page_id), 'url' => get_permalink($page_id));
        return $crumbs;
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

/**
 * Service schema for pages built with the service hero: name, description and price from the block,
 * provided by YT Web Co. and served in Ukraine.
 */
function theme_get_service_schema($post_id)
{
    $post = get_post($post_id);
    if (!$post || !has_block('acf/other-blocks-service-hero', $post)) {
        return array();
    }

    foreach (parse_blocks($post->post_content) as $block) {
        if ($block['blockName'] !== 'acf/other-blocks-service-hero') {
            continue;
        }
        $data = (array)($block['attrs']['data'] ?? array());
        $schema = array(
            '@type' => 'Service',
            '@id' => get_permalink($post_id) . '#service',
            'name' => trim(preg_replace('/\s+/u', ' ', preg_replace('/-\s*\R\s*/u', '-', wp_strip_all_tags(str_replace(array('<br>', '<br />'), "\n", (string)($data['b_other_service_hero_title'] ?? get_the_title($post_id))))))),
            'description' => trim(wp_strip_all_tags((string)($data['b_other_service_hero_text'] ?? ''))),
            'url' => get_permalink($post_id),
            'areaServed' => array('@type' => 'Country', 'name' => 'Ukraine'),
            // Inline until the Organization / ProfessionalService node is added in the SEO step
            'provider' => array('@type' => 'ProfessionalService', 'name' => 'YT Web Co.', 'url' => home_url('/')),
        );

        // First quote row = price ("from $1,500", "від 45 000 грн"); placeholders like "[$150]" are not real prices yet
        $price = (string)($data['b_other_service_hero_rows_0_value'] ?? '');
        if (strpos($price, '[') === false && preg_match('/(\$|грн|uah|usd)/iu', $price) && preg_match('/\d[\d\s,.]*/u', $price, $number)) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'priceSpecification' => array(
                    '@type' => 'PriceSpecification',
                    'minPrice' => (float)preg_replace('/[^\d.]/', '', str_replace(',', '', $number[0])),
                    'priceCurrency' => strpos($price, '$') !== false || stripos($price, 'usd') !== false ? 'USD' : 'UAH',
                ),
            );
        }

        return $schema;
    }

    return array();
}

add_filter('wpseo_schema_graph', function ($graph) {
    if (is_singular('page') && ($service = theme_get_service_schema(get_queried_object_id()))) {
        $graph[] = $service;
    }

    return $graph;
}, 20);

add_action('wp_head', function () {
    if (defined('WPSEO_VERSION') || !is_singular('page')) {
        return;
    }
    $service = theme_get_service_schema(get_queried_object_id());
    if ($service) {
        echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org') + $service, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
});

/**
 * Services hub page in the current language: UA /posluhy/, EN /en/services/ (its Polylang translation).
 */
function theme_get_services_hub_id()
{
    static $id = null;
    if ($id === null) {
        $page = get_page_by_path('services');
        $id = $page ? (int)$page->ID : 0;
        if ($id && function_exists('pll_get_post')) {
            $id = (int)pll_get_post($id) ?: 0;
        }
    }

    return $id;
}

