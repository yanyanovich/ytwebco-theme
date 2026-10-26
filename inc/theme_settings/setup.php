<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_setup()
{
    load_theme_textdomain('ytweb_theme', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus(
        array(
            'header_menu' => esc_html__('Header menu', 'ytweb_theme'),
            'footer_menu' => esc_html__('Footer menu', 'ytweb_theme'),
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    add_theme_support(
        'custom-background',
        apply_filters(
            'ytweb_theme_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    add_theme_support('customize-selective-refresh-widgets');

    add_theme_support(
        'custom-logo',
        array(
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        )
    );

    add_image_size('s-1920', 1920, 3840);
    add_image_size('s-1440', 1440, 2880);
    add_image_size('s-1280', 1280, 2560);
    add_image_size('s-767', 767, 1534);
    add_image_size('s-300', 300, 500);
}
add_action('after_setup_theme', 'theme_setup');

function theme_content_width()
{
    $GLOBALS['content_width'] = apply_filters('theme_content_width', 640);
}
add_action('after_setup_theme', 'theme_content_width', 0);

function theme_widgets_init()
{
    register_sidebar(
        array(
            'name' => esc_html__('Sidebar', 'ytweb_theme'),
            'id' => 'sidebar-1',
            'description' => esc_html__('Add widgets here.', 'ytweb_theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
}
add_action('widgets_init', 'theme_widgets_init');
