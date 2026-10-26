<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_get_additional_code_group()
{
    $project_mode = get_field('additional_code_proj_mode', 'scripts-metrics');
    $project_mode = in_array($project_mode, array('dev', 'prod'), true) ? $project_mode : 'dev';

    if ($project_mode === 'prod') {
        return get_field('additional_code_production_scripts', 'scripts-metrics');
    }

    return get_field('additional_code_dev_scripts', 'scripts-metrics');
}

function theme_get_additional_code_items($position)
{
    $additional_code = theme_get_additional_code_group();

    if (empty($additional_code[$position]) || !is_array($additional_code[$position])) {
        return array();
    }

    $code_items = array();

    foreach ($additional_code[$position] as $item) {
        if (!empty($item['code'])) {
            $code_items[] = $item['code'];
        }
    }

    return $code_items;
}

function theme_print_additional_code($position)
{
    $code_items = theme_get_additional_code_items($position);

    if (empty($code_items)) {
        return;
    }

    echo implode("\r\n", $code_items);
}