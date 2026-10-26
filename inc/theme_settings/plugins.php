<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';
require get_template_directory() . '/inc/tgm/plugins.php';

add_filter('ai1wm_exclude_themes_from_export', function ($exclude_filters) {
    $exclude_filters[] = 'ytweb_theme/_project-helpers';
    $exclude_filters[] = 'ytweb_theme/assembler';
    $exclude_filters[] = 'ytweb_theme/.git';
    $exclude_filters[] = 'ytweb_theme/.idea';

    return $exclude_filters;
});
