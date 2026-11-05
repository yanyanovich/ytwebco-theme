<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Private "lead" post type: one post per submission, the answers in post meta.
 * Not public, not queryable, never in the sitemap; visible to administrators only.
 */
class Tokaruk_Leads
{
    const POST_TYPE = 'lead';

    public static function register()
    {
        register_post_type(self::POST_TYPE, array(
            'labels' => array(
                'name' => __('Leads', 'tokaruk-leads'),
                'singular_name' => __('Lead', 'tokaruk-leads'),
                'menu_name' => __('Leads', 'tokaruk-leads'),
                'edit_item' => __('Lead', 'tokaruk-leads'),
                'search_items' => __('Search leads', 'tokaruk-leads'),
                'not_found' => __('No leads yet', 'tokaruk-leads'),
            ),
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'menu_icon' => 'dashicons-email-alt2',
            'menu_position' => 22,
            'supports' => array('title'),
            'capability_type' => 'post',
            'capabilities' => array('create_posts' => 'do_not_allow'),
            'map_meta_cap' => true,
        ));

        // Leads never reach the sitemap or Polylang
        add_filter('wp_sitemaps_post_types', function ($types) {
            unset($types[self::POST_TYPE]);
            return $types;
        });
        add_filter('wpseo_sitemap_exclude_post_type', function ($excluded, $post_type) {
            return $post_type === self::POST_TYPE ? true : $excluded;
        }, 10, 2);
        add_filter('pll_get_post_types', function ($types) {
            unset($types[self::POST_TYPE]);
            return $types;
        });

        if (is_admin()) {
            add_action('add_meta_boxes_' . self::POST_TYPE, array(__CLASS__, 'add_meta_box'));
            add_filter('manage_' . self::POST_TYPE . '_posts_columns', array(__CLASS__, 'columns'));
            add_action('manage_' . self::POST_TYPE . '_posts_custom_column', array(__CLASS__, 'column'), 10, 2);
        }
    }

    /**
     * Saves a lead.
     *
     * @param array $lead array{type: string, language: string, name: string, contact: string, fields: array<string, string|string[]>, ref?: string, page?: string}
     * @return int post ID, 0 on failure
     */
    public static function save(array $lead)
    {
        $type = $lead['type'] === 'brief' ? 'brief' : 'contact';
        $title = sprintf('%s · %s', $type === 'brief' ? 'Brief' : 'Contact', $lead['name'] ?: $lead['contact'] ?: '—');

        $post_id = wp_insert_post(array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'private',
            'post_title' => wp_strip_all_tags($title),
        ), true);

        if (is_wp_error($post_id)) {
            return 0;
        }

        update_post_meta($post_id, '_lead_type', $type);
        update_post_meta($post_id, '_lead_language', sanitize_key($lead['language']));
        update_post_meta($post_id, '_lead_fields', $lead['fields']);
        update_post_meta($post_id, '_lead_ref', sanitize_key($lead['ref'] ?? ''));
        update_post_meta($post_id, '_lead_page', esc_url_raw($lead['page'] ?? ''));
        update_post_meta($post_id, '_lead_attr', (array)($lead['attribution'] ?? array()));

        return (int)$post_id;
    }

    public static function set_delivery($post_id, $status)
    {
        update_post_meta($post_id, '_lead_delivery', sanitize_key($status));
    }

    public static function add_meta_box()
    {
        add_meta_box('tokaruk-lead', __('Answers', 'tokaruk-leads'), array(__CLASS__, 'render_meta_box'), self::POST_TYPE, 'normal', 'high');
    }

    public static function render_meta_box($post)
    {
        $fields = (array)get_post_meta($post->ID, '_lead_fields', true);
        $info = array(
            __('Type', 'tokaruk-leads') => get_post_meta($post->ID, '_lead_type', true),
            __('Language', 'tokaruk-leads') => get_post_meta($post->ID, '_lead_language', true),
            __('Source (ref)', 'tokaruk-leads') => get_post_meta($post->ID, '_lead_ref', true),
            __('Page', 'tokaruk-leads') => get_post_meta($post->ID, '_lead_page', true),
            __('Delivered', 'tokaruk-leads') => get_post_meta($post->ID, '_lead_delivery', true),
        );

        echo '<table class="widefat striped"><tbody>';
        foreach (array_merge($fields, $info) as $label => $value) {
            if ($value === '' || $value === array()) {
                continue;
            }
            printf(
                '<tr><th style="width:220px">%s</th><td>%s</td></tr>',
                esc_html($label),
                nl2br(esc_html(is_array($value) ? implode(', ', $value) : $value))
            );
        }
        echo '</tbody></table>';

        $attribution = Tokaruk_Leads_Attribution::summary((array)get_post_meta($post->ID, '_lead_attr', true));
        if ($attribution) {
            echo '<h3 style="margin:20px 0 8px">' . esc_html__('Source and visit', 'tokaruk-leads') . '</h3><table class="widefat striped"><tbody>';
            foreach ($attribution as $label => $value) {
                printf('<tr><th style="width:220px">%s</th><td>%s</td></tr>', esc_html($label), nl2br(esc_html((string)$value)));
            }
            echo '</tbody></table>';
        }
    }

    public static function columns($columns)
    {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Lead', 'tokaruk-leads'),
            'lead_type' => __('Type', 'tokaruk-leads'),
            'lead_language' => __('Language', 'tokaruk-leads'),
            'lead_source' => __('Source', 'tokaruk-leads'),
            'lead_delivery' => __('Telegram', 'tokaruk-leads'),
            'date' => $columns['date'],
        );
    }

    public static function column($column, $post_id)
    {
        $keys = array('lead_type' => '_lead_type', 'lead_language' => '_lead_language', 'lead_delivery' => '_lead_delivery');
        if ($column === 'lead_source') {
            $summary = Tokaruk_Leads_Attribution::summary((array)get_post_meta($post_id, '_lead_attr', true));
            echo esc_html(trim(($summary['Source'] ?? '—') . (!empty($summary['Campaign']) ? ' / ' . $summary['Campaign'] : '')));
            return;
        }
        if (isset($keys[$column])) {
            echo esc_html((string)get_post_meta($post_id, $keys[$column], true));
        }
    }
}
