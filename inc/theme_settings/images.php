<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_custom_image_srcset($sources, $size_array, $image_src, $image_meta)
{
    $sources = array();
    $custom_sizes = array(
        's-1920' => 1920,
        's-1440' => 1440,
        's-1280' => 1280,
        's-767' => 767,
        's-300' => 300,
    );

    $attachment_id = attachment_url_to_postid($image_src);

    foreach ($custom_sizes as $size_name => $width) {
        if (empty($image_meta['sizes'][$size_name])) {
            continue;
        }

        $image_url = wp_get_attachment_image_url($attachment_id, $size_name);

        if ($image_url) {
            $sources[$width] = array(
                'url' => $image_url,
                'descriptor' => 'w',
                'value' => $width,
            );
        }
    }

    if (!empty($image_meta['width']) && $attachment_id) {
        $full_url = wp_get_attachment_url($attachment_id);

        if ($full_url) {
            $sources[$image_meta['width']] = array(
                'url' => $full_url,
                'descriptor' => 'w',
                'value' => $image_meta['width'],
            );
        }
    }

    return $sources;
}
add_filter('wp_calculate_image_srcset', 'theme_custom_image_srcset', 10, 4);

function theme_lazy_in_html($html)
{
    if (is_admin() || is_feed()) {
        return $html;
    }

    return preg_replace_callback('/<img\b[^>]*>/i', function ($matches) {
        $tag = $matches[0];

        if (strpos($tag, 'skip-lazy') !== false) {
            return $tag;
        }

        $had_srcset = preg_match('/\ssrcset=([\'"])(.*?)\1/i', $tag) === 1;
        $tag = preg_replace('/\s(src|srcset)=["\']([^"\']+)["\']/', ' data-$1="$2"', $tag);

        if ($had_srcset) {
            if (preg_match('/\ssizes=([\'"])(.*?)\1/i', $tag)) {
                $tag = preg_replace('/\ssizes=([\'"])(.*?)\1/i', ' data-sizes="$2"', $tag);
            } else {
                $tag = preg_replace('/<img\b/i', '<img data-sizes="auto"', $tag, 1);
            }
        } else {
            $tag = preg_replace('/\ssizes=([\'"])(.*?)\1/i', '', $tag);
        }

        if (strpos($tag, ' src=') === false) {
            $tag = preg_replace(
                '/<img\b/i',
                '<img src="' . get_template_directory_uri() . '/media/images/img-placeholder-min.png" ',
                $tag,
                1
            );
        }

        if (preg_match('/\sclass=([\'"])([^\'"]*)\1/i', $tag, $class_match)) {
            $new_class = trim($class_match[2] . ' lazyload');
            $tag = preg_replace('/\sclass=([\'"])[^\'"]*\1/i', ' class="' . $new_class . '"', $tag);
        } else {
            $tag = preg_replace('/<img\b/i', '<img class="lazyload"', $tag, 1);
        }

        if (strpos($tag, ' alt=') === false) {
            $tag = preg_replace('/<img\b/i', '<img alt=""', $tag, 1);
        }

        return $tag;
    }, $html);
}

function theme_lazy_in_full_page_start()
{
    if (wp_doing_ajax()) {
        return;
    }

    ob_start('theme_lazy_in_html');
}
add_action('template_redirect', 'theme_lazy_in_full_page_start');
