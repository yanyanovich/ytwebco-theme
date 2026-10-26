<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_add_title_to_attachment_image($attr, $attachment)
{
    $attr['title'] = get_the_title($attachment->ID);

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'theme_add_title_to_attachment_image', 10, 2);

function theme_add_alt_text_column($columns)
{
    $columns['alt_text'] = __('Alt Text', 'ytweb_theme');

    return $columns;
}
add_filter('manage_media_columns', 'theme_add_alt_text_column');

function theme_display_alt_text_column($column_name, $post_id)
{
    if ($column_name !== 'alt_text') {
        return;
    }

    $alt_text = get_post_meta($post_id, '_wp_attachment_image_alt', true);
    echo esc_html($alt_text ? $alt_text : '');
}
add_action('manage_media_custom_column', 'theme_display_alt_text_column', 10, 2);

function theme_alt_text_column_sortable($columns)
{
    $columns['alt_text'] = 'alt_text';

    return $columns;
}
add_filter('manage_upload_sortable_columns', 'theme_alt_text_column_sortable');

function theme_sort_by_alt_text($query)
{
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'attachment') {
        return;
    }

    if ('alt_text' !== $query->get('orderby')) {
        return;
    }

    $query->set('orderby', 'meta_value');

    if ($query->get('order') === 'asc' || $query->get('order') === 'desc') {
        $query->set(
            'meta_query',
            array(
                'relation' => 'OR',
                array(
                    'key' => '_wp_attachment_image_alt',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => '_wp_attachment_image_alt',
                    'compare' => '=',
                    'value' => '',
                ),
            )
        );
    }
}
add_action('pre_get_posts', 'theme_sort_by_alt_text');
