<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('allowed_block_types_all', 'theme_allowed_block_types', 10, 2);
function theme_allowed_block_types($allowed_blocks, $editor_context)
{
    if (!$editor_context || empty($editor_context->post)) {
        return $allowed_blocks;
    }

    $groups = theme_get_block_groups();
    $post = $editor_context->post;
    $page_template = $post->ID ? basename(get_post_meta($post->ID, '_wp_page_template', true)) : '';

    if ($page_template === 'policy.php') {
        $theme_blocks = theme_collect_block_groups($groups, 'core_min', 'acf_other');
    } else {
        switch ($post->post_type) {
            case 'post':
                $theme_blocks = theme_collect_block_groups($groups, 'core_full', 'acf_blog');
                break;
            case 'page':
                $theme_blocks = theme_collect_block_groups($groups, 'core_full', 'acf_hero', 'acf_other');
                break;
            default:
                $theme_blocks = theme_collect_block_groups($groups, 'core_min', 'acf_other');
                break;
        }
    }

    $theme_blocks = theme_filter_registered_blocks($theme_blocks);

    return apply_filters('theme_allowed_block_types', $theme_blocks, $editor_context, $allowed_blocks);
}

function theme_get_block_groups()
{
    return array(
        'core_min' => array(
            'core/block',
            'core/paragraph',
        ),
        'core_full' => array(
            'core/block',
            'core/paragraph',
            'core/image',
            'core/heading',
            'core/gallery',
            'core/list',
            'core/list-item',
            'core/quote',
            'core/audio',
            'core/video',
            'core/cover',
            'core/file',
            'core/shortcode',
            'core/spacer',
            'core/separator',
            'core/button',
            'core/buttons',
            'core/columns',
            'core/group',
            'core/table',
            'core/verse',
            'core/code',
            'core/preformatted',
            'core/pullquote',
            'core/embed',
            'core-embed/twitter',
            'core-embed/youtube',
            'core-embed/facebook',
            'core-embed/instagram',
            'core-embed/wordpress',
            'core-embed/soundcloud',
            'core-embed/spotify',
            'core-embed/vimeo',
            'core-embed/flickr',
            'core-embed/animoto',
            'core-embed/cloudup',
            'core-embed/collegehumor',
            'core-embed/dailymotion',
            'core-embed/hulu',
            'core-embed/imgur',
            'core-embed/issuu',
            'core-embed/kickstarter',
            'core-embed/meetup-com',
            'core-embed/mixcloud',
            'core-embed/photobucket',
            'core-embed/polldaddy',
            'core-embed/reddit',
            'core-embed/reverbnation',
            'core-embed/screencast',
            'core-embed/scribd',
            'core-embed/slideshare',
            'core-embed/smugmug',
            'core-embed/speaker',
            'core-embed/ted',
            'core-embed/tumblr',
            'core-embed/videopress',
            'core-embed/wordpress-tv',
        ),
        'acf_hero' => array(
            // 'acf/hero-blocks-type1',
        ),
        'acf_blog' => array(
            // 'acf/blog-blocks-faq',
        ),
        'acf_other' => array(
            'acf/other-blocks-example1',
        ),
    );
}

function theme_get_block_categories()
{
    return array(
        array(
            'slug' => 'hero-blocks',
            'title' => 'Hero blocks',
        ),
        array(
            'slug' => 'blog-blocks',
            'title' => 'Blog blocks',
        ),
        array(
            'slug' => 'other-blocks',
            'title' => 'Other blocks',
        ),
    );
}

function theme_get_acf_block_json_files()
{
    $blocks_dir = get_template_directory() . '/template-parts/blocks';

    if (!is_dir($blocks_dir)) {
        return array();
    }

    $files = glob($blocks_dir . '/*/*/block.json');

    if (!$files) {
        return array();
    }

    sort($files);

    return $files;
}

function theme_get_acf_block_dirs()
{
    return array_map('dirname', theme_get_acf_block_json_files());
}

function theme_get_acf_block_names()
{
    $block_names = array();

    foreach (theme_get_acf_block_json_files() as $block_json_file) {
        $block_json = json_decode(file_get_contents($block_json_file), true);

        if (!empty($block_json['name']) && strpos($block_json['name'], 'acf/') === 0) {
            $block_names[] = $block_json['name'];
        }
    }

    return array_values(array_unique($block_names));
}

function theme_collect_block_groups(array $groups, ...$parts)
{
    $blocks = array();

    foreach ($parts as $part) {
        if (is_string($part) && isset($groups[$part])) {
            $blocks = array_merge($blocks, $groups[$part]);
        } elseif (is_array($part)) {
            $blocks = array_merge($blocks, $part);
        } elseif (is_string($part)) {
            $blocks[] = $part;
        }
    }

    return array_values(array_unique($blocks));
}

function theme_filter_registered_blocks(array $blocks)
{
    $registered_acf_blocks = theme_get_acf_block_names();

    return array_values(array_filter($blocks, function ($block_name) use ($registered_acf_blocks) {
        return strpos($block_name, 'acf/') !== 0 || in_array($block_name, $registered_acf_blocks, true);
    }));
}

add_filter('block_categories_all', 'theme_block_categories', 10, 2);
function theme_block_categories($categories, $editor_context)
{
    foreach (array_reverse(theme_get_block_categories()) as $new_category) {
        $category_exists = false;

        foreach ($categories as $category) {
            if (!empty($category['slug']) && $category['slug'] === $new_category['slug']) {
                $category_exists = true;
                break;
            }
        }

        if (!$category_exists) {
            array_unshift($categories, $new_category);
        }
    }

    return $categories;
}

add_action('init', 'theme_register_acf_blocks', 5);
function theme_register_acf_blocks()
{
    foreach (theme_get_acf_block_dirs() as $block_dir) {
        register_block_type($block_dir);
    }
}

function get_custom_block_styles($block, $styles = array())
{
    if (!empty($block['backgroundColor'])) {
        $styles[] = 'background-color: var(--wp--preset--color--' . esc_attr($block['backgroundColor']) . ')';
    } elseif (!empty($block['style']['color']['background'])) {
        $styles[] = 'background-color: ' . esc_attr($block['style']['color']['background']);
    }

    if (!empty($block['textColor'])) {
        $styles[] = 'color: var(--wp--preset--color--' . esc_attr($block['textColor']) . ')';
    } elseif (!empty($block['style']['color']['text'])) {
        $styles[] = 'color: ' . esc_attr($block['style']['color']['text']);
    }

    return !empty($styles) ? ' style="' . esc_attr(implode('; ', $styles)) . ';"' : '';
}

add_action('enqueue_block_editor_assets', function () {
    wp_add_inline_script('wp-blocks', "
    (function(wp){
        const { addFilter } = wp.hooks;
        const { createElement: el, useMemo } = wp.element;
        const { select } = wp.data;

        function isInsideReusable(clientId){
            const parents = select('core/block-editor').getBlockParents(clientId) || [];
            for (let i=0;i<parents.length;i++){
                const b = select('core/block-editor').getBlock(parents[i]);
                if (b && b.name === 'core/block' && b.attributes && b.attributes.ref) {
                    return b.attributes.ref;
                }
            }
            return null;
        }

        addFilter('editor.BlockEdit', 'ytweb_theme/reusable-mask', (BlockEdit)=> (props)=>{
            const { clientId, name } = props;
            const patternId = useMemo(()=> isInsideReusable(clientId), [clientId]);
            if (patternId && name !== 'core/block') {
                const label = name.replace(/^acf\\//,'').replace(/^core\\//,'').replace(/[-_]/g,' ');
                const title = '\"'+label.charAt(0) + label.slice(1)+'\"';

                return el('div', {
                    className: 'ytweb_theme-pattern-mask-editor',
                    style:{
                        padding:'12px', border:'1px dashed #bbb', borderRadius:'6px',
                        background:'#fafafa', textAlign:'center'
                    }
                },
                title + ' is pattern. Click edit original to change info'
                );
            }
            return el(BlockEdit, props);
        });
    })(window.wp);
    ");
});
