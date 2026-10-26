<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('xmlrpc_enabled', '__return_false');

add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    $route = $request->get_route();
    if ($route === '/wp/v2/users' || strpos($route, '/wp/v2/users/') === 0) {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                __('Sorry, you are not allowed to do that.'),
                array('status' => 401)
            );
        }
    }
    return $result;
}, 10, 3);
