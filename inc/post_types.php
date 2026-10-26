<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_types_dir = get_template_directory() . '/inc/post_types';
$post_type_files = glob($post_types_dir . '/*.php');

if (!$post_type_files) {
    return;
}

sort($post_type_files);

$helpers_file = $post_types_dir . '/helpers.php';

if (file_exists($helpers_file)) {
    require $helpers_file;
}

foreach ($post_type_files as $post_type_file) {
    if ($post_type_file === $helpers_file) {
        continue;
    }

    require $post_type_file;
}
