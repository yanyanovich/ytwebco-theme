<?php
/**
 * ytweb_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ytweb_theme
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

require get_template_directory() . '/inc/theme_settings.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/polylang.php';
require get_template_directory() . '/inc/menus.php';
require get_template_directory() . '/inc/acf_settings.php';
require get_template_directory() . '/inc/post_types.php';
require get_template_directory() . '/inc/cf7_functions.php';
require get_template_directory() . '/inc/CustomArchiveContent.php';
require get_template_directory() . '/inc/enqueue_scripts_and_styles.php';
require get_template_directory() . '/inc/gutenberg_blocks.php';
require get_template_directory() . '/inc/ajax_functions.php';
