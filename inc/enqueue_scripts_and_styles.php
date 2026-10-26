<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts and styles.
 */
function theme_styles()
{
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );

    $style_main_path = get_template_directory() . '/assets/css/style.min.css';
    $style_main_uri = get_template_directory_uri() . '/assets/css/style.min.css';

    if (file_exists($style_main_path)) {
        wp_enqueue_style('ytweb-theme-style-main', $style_main_uri, array(), filemtime($style_main_path));
    }
}
add_action('wp_enqueue_scripts', 'theme_styles');

/**
 * Preload the Latin subsets of the two variable fonts (used above the fold on every page).
 * Cyrillic subsets load on demand through unicode-range.
 */
function theme_preload_fonts()
{
    $fonts = array('inter-tight-latin-wght-normal', 'jetbrains-mono-latin-wght-normal');

    foreach ($fonts as $font) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url(get_template_directory_uri() . '/fonts/' . $font . '.woff2')
        );
    }
}
add_action('wp_head', 'theme_preload_fonts', 1);

/**
 * Enqueue scripts and styles.
 */
function theme_scripts() {
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery-3.7.1.min.js', array(), null, [ 'in_footer'=>false, 'strategy' => 'defer']);
    $scripts_main_path = get_template_directory() . '/assets/js/scripts.min.js';
    $scripts_main_uri = get_template_directory_uri() . '/assets/js/scripts.min.js';

    if (file_exists($scripts_main_path)) {
        $theme_settings_cookieyes_site_key = get_field('keys_cookieyes', 'scripts-metrics');

        $options = get_option('wpcf7');
        $site_key = null;
        if (isset($options['recaptcha']) && is_array($options['recaptcha'])) {
            foreach ($options['recaptcha'] as $key => $value) {
                $site_key = $key;
                break;
            }
        }

        wp_enqueue_script('ytweb-theme-scripts-main', $scripts_main_uri, array('jquery'), filemtime($scripts_main_path), ['in_footer' => false, 'strategy' => 'defer']);
        wp_localize_script('ytweb-theme-scripts-main', 'scriptData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'themeUrl' => get_template_directory_uri(),
            'cookieyesScriptUrl' => theme_get_cookieyes_script_url(),
            'cookieyesScriptUrlExtra' => theme_get_cookieyes_script_url_extra(),
            'cookieyesSiteKey' => $theme_settings_cookieyes_site_key,
            'cf7_captcha'=>$site_key
        ));
    }
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

function theme_custom_blocks_ui_enqueue() {
    $block_previews = array();
    $category_order = array();
    $custom_blocks_ui_script_path = get_template_directory() . '/inc/custom_blocks_ui/custom-block-ui.js';
    $custom_blocks_ui_script_uri = get_template_directory_uri() . '/inc/custom_blocks_ui/custom-block-ui.js';
    $custom_blocks_ui_style_path = get_template_directory() . '/inc/custom_blocks_ui/custom-block-ui.css';
    $custom_blocks_ui_style_uri = get_template_directory_uri() . '/inc/custom_blocks_ui/custom-block-ui.css';

    if (function_exists('theme_get_acf_block_json_files')) {
        foreach (theme_get_acf_block_json_files() as $block_json_file) {
            $block_json = json_decode(file_get_contents($block_json_file), true);

            if (empty($block_json['name'])) {
                continue;
            }

            $block_dir = dirname($block_json_file);
            $preview_file = $block_dir . '/preview.png';

            if (!file_exists($preview_file)) {
                continue;
            }

            $relative_path = str_replace(get_template_directory(), '', $preview_file);
            $block_previews[$block_json['name']] = get_template_directory_uri() . str_replace('\\', '/', $relative_path);
        }
    }

    if (function_exists('theme_get_block_categories')) {
        foreach (theme_get_block_categories() as $category) {
            if (!empty($category['slug'])) {
                $category_order[] = $category['slug'];
            }
        }
    }

    wp_enqueue_script(
        'custom-block-ui',
        $custom_blocks_ui_script_uri,
        array('wp-blocks', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins'),
        filemtime($custom_blocks_ui_script_path),
        [ 'in_footer'=>true ]
    );
    wp_localize_script('custom-block-ui', 'emptyThemeCustomBlocks', array(
        'previewUrls' => $block_previews,
        'categoryOrder' => $category_order,
    ));

    wp_enqueue_style(
        'custom-block-ui',
        $custom_blocks_ui_style_uri,
        array(),
        filemtime($custom_blocks_ui_style_path)
    );
}
add_action('enqueue_block_editor_assets', 'theme_custom_blocks_ui_enqueue');

function theme_get_cookieyes_script_url() {
    global $wp_scripts;
    if (isset($wp_scripts->registered['cookie-law-info'])) {
        return $wp_scripts->registered['cookie-law-info']->src;
    }

    return null;
}
function theme_get_cookieyes_script_url_extra() {
    global $wp_scripts;
    if (isset($wp_scripts->registered['cookie-law-info'])) {
        return isset($wp_scripts->registered['cookie-law-info']->extra['data'])
            ? $wp_scripts->registered['cookie-law-info']->extra['data']
            : null;
    }

    return null;
}

function theme_disable_cookieyes_script() {
    global $wp_scripts;

    if (isset($wp_scripts->registered['cookie-law-info'])) {
        wp_dequeue_script('cookie-law-info');
        wp_dequeue_style('cookie-law-info');
    }
}
add_action('wp_enqueue_scripts', 'theme_disable_cookieyes_script', 20);
