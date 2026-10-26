<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_add_page_supports()
{
    add_post_type_support('page', 'excerpt');
    add_post_type_support('page', 'revisions');
}
add_action('init', 'theme_add_page_supports');
