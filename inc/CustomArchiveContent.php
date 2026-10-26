<?php
if (!defined('ABSPATH')) {
    exit;
}

class CustomArchiveContent
{
    const POST_TYPE = 'archive_page';
    const OPTION_PREFIX = 'custom_archive_content_for_';

    private $archive_post_types = array();

    public function __construct()
    {
        add_action('init', array($this, 'register_archive_page_post_type'));
        add_action('admin_init', array($this, 'register_reading_settings'));
        add_filter('display_post_states', array($this, 'add_archive_page_post_states'), 10, 2);
        add_filter('pll_get_post_types', array($this, 'add_archive_page_to_polylang'), 10, 2);
    }

    public function register_archive_page_post_type()
    {
        register_post_type(self::POST_TYPE, array(
            'labels' => array(
                'name' => __('Archive pages', 'ytweb_theme'),
                'singular_name' => __('Archive page', 'ytweb_theme'),
                'add_new' => __('Add archive page', 'ytweb_theme'),
                'add_new_item' => __('Add archive page', 'ytweb_theme'),
                'edit_item' => __('Edit archive page', 'ytweb_theme'),
                'new_item' => __('New archive page', 'ytweb_theme'),
                'view_item' => __('View archive page', 'ytweb_theme'),
                'search_items' => __('Search archive pages', 'ytweb_theme'),
                'not_found' => __('Archive pages not found', 'ytweb_theme'),
                'not_found_in_trash' => __('No archive pages in trash', 'ytweb_theme'),
                'menu_name' => __('Archive pages', 'ytweb_theme'),
                'back_to_items' => __('Go to archive pages', 'ytweb_theme'),
            ),
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=page',
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'show_in_rest' => true,
            'query_var' => false,
            'rewrite' => false,
            'menu_icon' => 'dashicons-welcome-widgets-menus',
            'capability_type' => 'page',
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'revisions'),
        ));
    }

    public function register_reading_settings()
    {
        foreach ($this->get_archive_post_types() as $post_type => $label) {
            register_setting(
                'reading',
                self::OPTION_PREFIX . $post_type,
                array(
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'default' => 0,
                )
            );

            add_settings_field(
                self::OPTION_PREFIX . $post_type,
                $label,
                array($this, 'render_archive_page_select'),
                'reading',
                'default',
                array(
                    'label_for' => self::OPTION_PREFIX . $post_type,
                    'post_type' => $post_type,
                )
            );
        }
    }

    public function render_archive_page_select($args)
    {
        $post_type = $args['post_type'];
        $selected_archive_page_id = theme_get_archive_page_id($post_type, false);
        $archive_pages = get_posts(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => self::POST_TYPE,
            'post_status' => array('publish', 'draft', 'private'),
        ));

        echo '<select id="' . esc_attr(self::OPTION_PREFIX . $post_type) . '" name="' . esc_attr(self::OPTION_PREFIX . $post_type) . '">';
        echo '<option value="0">' . esc_html__('- Select -', 'ytweb_theme') . '</option>';

        foreach ($archive_pages as $archive_page) {
            echo '<option value="' . esc_attr($archive_page->ID) . '" ' . selected($selected_archive_page_id, $archive_page->ID, false) . '>' . esc_html($archive_page->post_title) . '</option>';
        }

        echo '</select>';
    }

    public function add_archive_page_post_states($states, $post)
    {
        if ($post->post_type !== self::POST_TYPE) {
            return $states;
        }

        foreach ($this->get_archive_post_types() as $post_type => $label) {
            $archive_page_id = theme_get_archive_page_id($post_type, false);

            if ((int) $post->ID === (int) $archive_page_id) {
                $states[] = sprintf(
                    esc_html__('Archive for: %s', 'ytweb_theme'),
                    esc_html($label)
                );
            }
        }

        return $states;
    }

    private function get_archive_post_types()
    {
        if (!empty($this->archive_post_types)) {
            return $this->archive_post_types;
        }

        $post_types = get_post_types(array(
            'public' => true,
            '_builtin' => false,
            'has_archive' => true,
        ), 'objects');

        $this->archive_post_types = array(
            'post' => __('Posts page', 'ytweb_theme'),
        );

        foreach ($post_types as $post_type => $post_type_object) {
            $this->archive_post_types[$post_type] = $post_type_object->labels->name;
        }

        return apply_filters('ytweb_theme_archive_page_post_types', $this->archive_post_types);
    }

    public function add_archive_page_to_polylang($post_types, $is_settings)
    {
        $post_types[self::POST_TYPE] = self::POST_TYPE;

        return $post_types;
    }
}

function theme_get_archive_page_id($post_type, $translate = true)
{
    $archive_page_id = (int) get_option(CustomArchiveContent::OPTION_PREFIX . $post_type);

    if (!$archive_page_id) {
        return 0;
    }

    if ($translate && function_exists('pll_get_post')) {
        $translated_archive_page_id = pll_get_post($archive_page_id);

        if ($translated_archive_page_id) {
            return (int) $translated_archive_page_id;
        }
    }

    return $archive_page_id;
}

function theme_get_archive_page_content($post_type = '')
{
    if (!$post_type) {
        $queried_object = get_queried_object();
        $post_type = !empty($queried_object->name) ? $queried_object->name : get_query_var('post_type');
    }

    if (is_array($post_type)) {
        $post_type = reset($post_type);
    }

    if (!$post_type) {
        return '';
    }

    $archive_page_id = theme_get_archive_page_id($post_type);

    if (!$archive_page_id) {
        return '';
    }

    $archive_page = get_post($archive_page_id);

    if (!$archive_page) {
        return '';
    }

    return apply_filters('the_content', $archive_page->post_content);
}

new CustomArchiveContent();
