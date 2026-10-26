<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_redirect_to_lowercase()
{
    $request_uri = $_SERVER['REQUEST_URI'];
    $is_login_page = stripos($request_uri, 'wp-login.php') !== false;
    $is_signup_page = stripos($request_uri, 'wp-signup.php') !== false;
    $is_api_request = stripos($request_uri, '/wp-json/') !== false;

    if (is_admin() || $is_login_page || $is_signup_page || $is_api_request) {
        return;
    }

    $url_components = explode('?', $request_uri, 2);
    $path = $url_components[0];
    $query = isset($url_components[1]) ? '?' . $url_components[1] : '';

    if (preg_match('/[A-Z]/', $path)) {
        wp_redirect(site_url(strtolower($path) . $query), 301);
        exit;
    }
}
add_action('init', 'theme_redirect_to_lowercase');

add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (!empty($_GET['pageNumber'])) {
        return false;
    }

    return $redirect_url;
}, 10, 2);
