<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('tgmpa_register', 'theme_register_required_plugins');

function theme_register_required_plugins()
{
    $plugins = array(
        array(
            'name' => 'Advanced Custom Fields PRO',
            'slug' => 'advanced-custom-fields-pro',
            'source' => get_template_directory() . '/_project-helpers/plugins/advanced-custom-fields-pro.zip',
            'required' => true,
        ),
        array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
            'required' => true,
        ),
        array(
            'name' => 'Flamingo',
            'slug' => 'flamingo',
            'required' => true,
        ),
        array(
            'name' => 'Converter for Media',
            'slug' => 'webp-converter-for-media',
            'required' => true,
        ),
        array(
            'name' => 'Safe SVG',
            'slug' => 'safe-svg',
            'required' => true,
        ),
        array(
            'name' => 'All-in-One WP Migration and Backup',
            'slug' => 'all-in-one-wp-migration',
            'required' => true,
        ),
        array(
            'name' => 'WP Mail SMTP',
            'slug' => 'wp-mail-smtp',
            'required' => true,
        ),
        array(
            'name' => 'Yoast SEO',
            'slug' => 'wordpress-seo',
            'required' => true,
        ),
        array(
            'name' => 'WPS Hide Login',
            'slug' => 'wps-hide-login',
            'required' => true,
        ),
        array(
            'name' => 'Limit Login Attempts Reloaded',
            'slug' => 'limit-login-attempts-reloaded',
            'required' => true,
        ),
        array(
            'name' => 'Redirection',
            'slug' => 'redirection',
            'required' => true,
        ),
        array(
            'name' => 'CookieYes | GDPR Cookie Consent',
            'slug' => 'cookie-law-info',
            'required' => true,
        ),
        array(
            'name' => 'Advanced Editor Tools',
            'slug' => 'tinymce-advanced',
            'required' => true,
        ),
        array(
            'name' => 'Breadcrumb NavXT',
            'slug' => 'breadcrumb-navxt',
            'required' => false,
        ),
    );

    $config = array(
        'id' => 'ytweb_theme',
        'default_path' => '',
        'menu' => 'ytweb-theme-install-plugins',
        'parent_slug' => 'themes.php',
        'capability' => 'edit_theme_options',
        'has_notices' => true,
        'dismissable' => true,
        'dismiss_msg' => '',
        'is_automatic' => false,
        'message' => '',
    );

    tgmpa($plugins, $config);
}
