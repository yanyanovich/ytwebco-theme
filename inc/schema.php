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
            'areaServed' => theme_get_area_served(),
            'provider' => array('@id' => theme_get_organization_id()),
        );

        $service_type = theme_get_service_type($post_id);
        if ($service_type) {
            $schema['serviceType'] = $service_type;
        }

        $catalog = theme_get_table_offer_catalog($post_id);
        if ($catalog) {
            $schema['hasOfferCatalog'] = $catalog;
        }

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



/**
 * ---- Organization, offers, contact / collection / case pages (Yoast graph) ----
 * Spec from the UA texts: ProfessionalService (no street address; Lutsk / Volyn / Ukraine), OfferCatalog from the
 * visible prices, ContactPage, CollectionPage + ItemList on Work, CreativeWork on cases. No AggregateRating until
 * there are real, visible reviews.
 */

// The site has no search page: don't advertise a SearchAction
add_filter('disable_wpseo_json_ld_search', '__return_true');

function theme_is_ua()
{
    return function_exists('pll_current_language') ? pll_current_language() === 'ua' : get_locale() === 'uk';
}

/**
 * One organization node for the whole site (the default-language home URL is its identity in both languages).
 */
function theme_get_organization_id()
{
    $home = function_exists('pll_home_url') && function_exists('pll_default_language') ? pll_home_url(pll_default_language()) : home_url('/');

    return trailingslashit($home) . '#organization';
}

function theme_get_area_served()
{
    $ua = theme_is_ua();

    return array(
        array('@type' => 'City', 'name' => $ua ? 'Луцьк' : 'Lutsk'),
        array('@type' => 'AdministrativeArea', 'name' => $ua ? 'Волинська область' : 'Volyn Oblast'),
        array('@type' => 'Country', 'name' => $ua ? 'Україна' : 'Ukraine'),
    );
}

/**
 * "від 12 000 грн", "from $260", "від 2 500 грн/міс" → array(amount, currency). Placeholders ("[$150]") and
 * non-numeric values give null.
 */
function theme_parse_price($text)
{
    $text = trim(wp_strip_all_tags((string)$text));
    if ($text === '' || strpos($text, '[') !== false || !preg_match('/(\$|грн|uah|usd|€)/iu', $text) || !preg_match('/\d[\d\s\x{00A0},.]*/u', $text, $number)) {
        return null;
    }

    $amount = (float)preg_replace('/[^\d.]/', '', str_replace(',', '', preg_replace('/[\s\x{00A0}]/u', '', $number[0])));
    if ($amount <= 0) {
        return null;
    }

    return array(
        'amount' => $amount,
        'currency' => strpos($text, '$') !== false || stripos($text, 'usd') !== false ? 'USD' : (strpos($text, '€') !== false ? 'EUR' : 'UAH'),
    );
}

function theme_build_offer($name, $price_text, $service_url = '')
{
    $price = theme_parse_price($price_text);
    if (!$price || trim($name) === '') {
        return null;
    }

    $item = array('@type' => 'Service', 'name' => trim($name));
    if ($service_url) {
        $item['url'] = $service_url;
    }

    return array(
        '@type' => 'Offer',
        'itemOffered' => $item,
        'priceSpecification' => array(
            '@type' => 'PriceSpecification',
            'minPrice' => $price['amount'],
            'priceCurrency' => $price['currency'],
        ),
    );
}

function theme_get_block_data($post_id, $block_name)
{
    $post = get_post($post_id);
    if (!$post || !has_block($block_name, $post)) {
        return array();
    }

    foreach (parse_blocks($post->post_content) as $block) {
        if ($block['blockName'] === $block_name) {
            return (array)($block['attrs']['data'] ?? array());
        }
    }

    return array();
}

/**
 * OfferCatalog from the price list block (home, services hub): one Offer per row with a real price.
 */
function theme_get_price_list_catalog($post_id)
{
    $data = theme_get_block_data($post_id, 'acf/home-page-blocks-price-list');
    $count = (int)($data['b_hp_prices_items'] ?? 0);
    $offers = array();

    for ($i = 0; $i < $count; $i++) {
        $offer = theme_build_offer($data["b_hp_prices_items_{$i}_title"] ?? '', $data["b_hp_prices_items_{$i}_price"] ?? '');
        if ($offer) {
            $offers[] = $offer;
        }
    }

    if (!$offers) {
        return array();
    }

    return array(
        '@type' => 'OfferCatalog',
        'name' => theme_is_ua() ? 'Послуги та ціни' : 'Services and prices',
        'itemListElement' => $offers,
    );
}

/**
 * OfferCatalog from the comparison tables of a service page: a "Price" row whose cells are plain prices
 * ("від 12 000 грн"), one Offer per column. Free-text cells ("від 45 000 грн, зате без обмежень") are skipped.
 */
function theme_get_table_offer_catalog($post_id)
{
    $post = get_post($post_id);
    if (!$post || !has_block('acf/other-blocks-table', $post)) {
        return array();
    }

    $offers = array();
    foreach (parse_blocks($post->post_content) as $block) {
        if ($block['blockName'] !== 'acf/other-blocks-table') {
            continue;
        }
        $data = (array)($block['attrs']['data'] ?? array());
        // first line = header of the label column (often empty), the rest = one header per value column
        $columns = array_slice(array_map('trim', preg_split('/\R/u', (string)($data['b_other_table_columns'] ?? ''))), 1);
        $rows = (int)($data['b_other_table_rows'] ?? 0);

        for ($i = 0; $i < $rows; $i++) {
            if (!preg_match('/^(ціна|вартість|мій старт|price|cost|my start|starting)/iu', trim((string)($data["b_other_table_rows_{$i}_label"] ?? '')))) {
                continue;
            }
            $cells = array_values(array_map('trim', preg_split('/\R/u', (string)($data["b_other_table_rows_{$i}_cells"] ?? ''))));
            foreach ($columns as $index => $column) {
                $cell = $cells[$index] ?? '';
                // only a clean price: optional "від / from", number, currency
                if ($column === '' || !preg_match('/^(від|from)?\s*[\d\s\x{00A0},.]+\s*(грн|uah|usd|\$)?$|^(від|from)?\s*\$\s*[\d,.]+$/iu', $cell)) {
                    continue;
                }
                $offer = theme_build_offer($column, $cell, get_permalink($post_id));
                if ($offer) {
                    $offers[] = $offer;
                }
            }
        }
    }

    if (!$offers) {
        return array();
    }

    return array(
        '@type' => 'OfferCatalog',
        'name' => get_the_title($post_id),
        'itemListElement' => $offers,
    );
}

/**
 * serviceType by the English slug of the page (the same service in both languages).
 */
function theme_get_service_type($post_id)
{
    $english = function_exists('pll_get_post') ? (int)pll_get_post($post_id, 'en') : $post_id;
    $slug = $english ? get_post_field('post_name', $english) : '';
    $types = array(
        'websites' => 'Website development',
        'woocommerce-development' => 'Online store development',
        'speed-seo' => 'Website speed optimization and technical SEO',
        'integrations' => 'Integrations and Telegram bot development',
        'web-design' => 'Website design',
    );

    return $types[$slug] ?? '';
}

/**
 * The ProfessionalService node: name, founder, area, contacts and profiles; no street address.
 */
function theme_get_organization_schema()
{
    $contacts = function_exists('theme_get_contacts') ? theme_get_contacts() : array();
    $ua = theme_is_ua();
    $id = theme_get_organization_id();
    $home = trailingslashit(function_exists('pll_home_url') && function_exists('pll_default_language') ? pll_home_url(pll_default_language()) : home_url('/'));

    $node = array(
        '@type' => 'ProfessionalService',
        '@id' => $id,
        'name' => 'YT Web Co.',
        'url' => $home,
        'description' => $ua
            ? 'Веб-студія в Луцьку: сайти, інтернет-магазини на WordPress і WooCommerce, прискорення, SEO, Telegram-боти та інтеграції.'
            : 'Web studio in Lutsk, Ukraine: WordPress and WooCommerce websites and stores, speed and technical SEO, Telegram bots and integrations.',
        'founder' => array('@type' => 'Person', 'name' => $ua ? 'Ян Токарук' : 'Yan Tokaruk', 'jobTitle' => $ua ? 'Веб-розробник' : 'Web developer'),
        'areaServed' => theme_get_area_served(),
        // City level only: no streetAddress by design
        'address' => array('@type' => 'PostalAddress', 'addressLocality' => $ua ? 'Луцьк' : 'Lutsk', 'addressRegion' => $ua ? 'Волинська область' : 'Volyn Oblast', 'addressCountry' => 'UA'),
        'knowsLanguage' => array('uk', 'en'),
    );

    $icon = get_site_icon_url(512);
    if ($icon) {
        $node['logo'] = array('@type' => 'ImageObject', 'url' => $icon, 'width' => 512, 'height' => 512);
        $node['image'] = $icon;
    }

    $phone = $contacts['phone'] ?? '';
    if ($phone && strpos($phone, '[') === false) {
        $node['telephone'] = preg_replace('/[^\d+]/', '', $phone);
    }

    $email = $contacts['email'] ?? '';
    if ($email && strpos($email, '[') === false) {
        $node['email'] = $email;
    }

    $same_as = array();
    foreach ((array)(function_exists('get_field') ? get_field('theme_settings_socials', 'theme-settings') : array()) as $social) {
        if (!empty($social['url'])) {
            $same_as[] = $social['url'];
        }
    }
    if ($same_as) {
        $node['sameAs'] = array_values(array_unique($same_as));
    }

    return $node;
}

add_filter('wpseo_schema_graph', function ($graph) {
    if (is_admin() || is_feed() || is_404()) {
        return $graph;
    }

    $org = theme_get_organization_schema();
    $org_ref = array('@id' => $org['@id']);
    $post_id = is_singular() ? get_queried_object_id() : 0;

    // Home: the price list becomes the organization's OfferCatalog
    if (is_front_page() && $post_id && ($catalog = theme_get_price_list_catalog($post_id))) {
        $org['hasOfferCatalog'] = $catalog;
    }

    $page_done = false;
    foreach ($graph as &$piece) {
        $types = (array)($piece['@type'] ?? array());

        if (in_array('WebSite', $types, true)) {
            $piece['publisher'] = $org_ref;
            continue;
        }

        if ($page_done || (!in_array('WebPage', $types, true) && !array_intersect($types, array('CollectionPage', 'ContactPage', 'ProfilePage')))) {
            continue;
        }
        $page_done = true;

        // Contact page → ContactPage about the organization
        if ($post_id && has_block('acf/other-blocks-contact-page', $post_id)) {
            $piece['@type'] = array_values(array_unique(array_merge($types, array('ContactPage'))));
            $piece['mainEntity'] = $org_ref;
        }

        // Services hub → CollectionPage with the OfferCatalog
        if ($post_id && $post_id === theme_get_services_hub_id() && ($catalog = theme_get_price_list_catalog($post_id))) {
            $piece['@type'] = array_values(array_unique(array_merge($types, array('CollectionPage'))));
            $piece['mainEntity'] = $catalog;
        }

        // Work archive → ItemList of the cases
        if (is_post_type_archive('case')) {
            $items = array();
            foreach (get_posts(array('post_type' => 'case', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC')) as $position => $case) {
                $items[] = array('@type' => 'ListItem', 'position' => $position + 1, 'url' => get_permalink($case), 'name' => get_the_title($case));
            }
            if ($items) {
                $piece['mainEntity'] = array('@type' => 'ItemList', 'numberOfItems' => count($items), 'itemListElement' => $items);
            }
        }

        // Case → the WebPage presents a CreativeWork made by the founder
        if (is_singular('case')) {
            $url = get_permalink($post_id);
            $piece['mainEntity'] = array('@id' => $url . '#creativework');
            $work = array(
                '@type' => 'CreativeWork',
                '@id' => $url . '#creativework',
                'name' => get_the_title($post_id),
                'url' => $url,
                'inLanguage' => theme_is_ua() ? 'uk' : 'en',
                'creator' => array('@type' => 'Person', 'name' => theme_is_ua() ? 'Ян Токарук' : 'Yan Tokaruk', 'worksFor' => $org_ref),
                'publisher' => $org_ref,
            );
            $description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
            if ($description) {
                $work['description'] = $description;
            }
            $image = get_the_post_thumbnail_url($post_id, 'full') ?: get_post_meta($post_id, '_yoast_wpseo_opengraph-image', true);
            if ($image) {
                $work['image'] = $image;
            }
            $graph_extra = $work;
        }
    }
    unset($piece);

    $graph[] = $org;
    if (!empty($graph_extra)) {
        $graph[] = $graph_extra;
    }

    return $graph;
}, 15);
