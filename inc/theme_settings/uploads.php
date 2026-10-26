<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_extra_upload_mime_types()
{
    return array(
        'gltf' => 'model/gltf+json',
        'glb' => 'model/gltf-binary',
        'json' => 'application/json',
        'ico' => 'image/x-icon',
        'mov' => 'video/mp4',
    );
}

function theme_custom_mime_types($mimes)
{
    return array_merge(
        $mimes,
        theme_extra_upload_mime_types()
    );
}
add_filter('upload_mimes', 'theme_custom_mime_types');

function theme_check_filetype_and_ext($data, $file, $filename, $mimes)
{
    if (!empty($data['ext']) && !empty($data['type'])) {
        return $data;
    }

    $wp_filetype = wp_check_filetype($filename, $mimes);
    $extra_mimes = theme_extra_upload_mime_types();

    if (
        empty($wp_filetype['ext']) ||
        empty($wp_filetype['type']) ||
        !isset($extra_mimes[$wp_filetype['ext']])
    ) {
        return $data;
    }

    return array(
        'ext' => $wp_filetype['ext'],
        'type' => $wp_filetype['type'],
        'proper_filename' => isset($data['proper_filename']) ? $data['proper_filename'] : false,
    );
}
add_filter('wp_check_filetype_and_ext', 'theme_check_filetype_and_ext', 10, 4);
