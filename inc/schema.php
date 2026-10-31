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
