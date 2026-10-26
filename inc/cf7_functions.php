<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Delete <p> from cf7 forms on front
 */
add_filter('wpcf7_autop_or_not', '__return_false');

function render_cf7_form($form_id){
    if(!empty($form_id)){
        echo do_shortcode(sprintf('[contact-form-7 id="%d"]', $form_id));
    }
}

/**
 * Change Submit input to Submit button if contains "btn" class
 */
add_filter('wpcf7_form_elements', 'theme_replace_cf7_submit_input_with_button');
function theme_replace_cf7_submit_input_with_button($content)
{
    return preg_replace_callback(
        '/<input([^>]*type=["\']submit["\'][^>]*)>/',
        function ($matches) {
            $attributes = preg_replace('/\s*\/$/', '', $matches[1]);

            if (!preg_match('/class=["\'][^"\']*btn[^"\']*["\']/', $attributes)) {
                return '<input' . $matches[1] . '>';
            }

            preg_match('/value=["\']([^"\']*)["\']/', $attributes, $value_match);
            $button_text = isset($value_match[1]) ? esc_html($value_match[1]) : '';

            return '<button' . $attributes . '>' . $button_text . '</button>';
        },
        $content
    );
}

/**
 * Delay recaptcha v3
 */
function theme_get_wpcf7_recaptcha_keys()
{
    $options = get_option('wpcf7');
    $site_key = '';
    $secret_key = '';

    if (isset($options['recaptcha']) && is_array($options['recaptcha'])) {
        foreach ($options['recaptcha'] as $key => $value) {
            $site_key = $key;
            $secret_key = $value;
            break;
        }
    }

    return array(
        'site_key' => $site_key,
        'secret_key' => $secret_key,
    );
}

function theme_disable_wpcf7_recaptcha() {
    wp_dequeue_script( 'google-recaptcha' );
    wp_dequeue_script( 'wpcf7-recaptcha' );
    wp_dequeue_style( 'wpcf7-recaptcha' );

    $keys = theme_get_wpcf7_recaptcha_keys();
    if ( isset( $keys['site_key'] ) && $keys['site_key'] != '' ) {
        wp_register_script(
            'delay-wpcf7-recaptcha',
            get_template_directory_uri() . '/js/cf7_delay_captcha.js',
            array(
                'wp-polyfill',
            ),
            null,
            array( 'in_footer' => true )
        );

        wp_localize_script( 'delay-wpcf7-recaptcha',
            'wpcf7_recaptcha',
            array(
                'sitekey'  => $keys['site_key'],
                'actions'  => apply_filters( 'wpcf7_recaptcha_actions', array(
                    'homepage'    => 'homepage',
                    'contactform' => 'contactform',
                ) ),
                'home_url' => get_home_url(),
                'version'  => defined('WPCF7_VERSION') ? WPCF7_VERSION : ''
            )
        );

        wp_enqueue_script( 'delay-wpcf7-recaptcha' );
    }
}

add_action( 'wp_enqueue_scripts', 'theme_disable_wpcf7_recaptcha', 20 );
