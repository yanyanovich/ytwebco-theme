<?php
/**
 * Plugin Name: YT Web Co. Leads
 * Description: Saves contact form and project brief submissions as private leads and sends them to Telegram. Contact forms run on Contact Form 7; the brief posts to its own REST endpoint.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Author: Yan Tokaruk
 * Text Domain: tokaruk-leads
 *
 * Telegram: define TOKARUK_TG_TOKEN and TOKARUK_TG_CHAT_ID in wp-config.php (never in code).
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TOKARUK_LEADS_DIR', __DIR__);

require_once __DIR__ . '/includes/class-leads.php';
require_once __DIR__ . '/includes/class-guard.php';
require_once __DIR__ . '/includes/class-attribution.php';
require_once __DIR__ . '/includes/class-telegram.php';
require_once __DIR__ . '/includes/class-cf7.php';
require_once __DIR__ . '/includes/class-rest.php';

add_action('init', array('Tokaruk_Leads', 'register'));
add_action('plugins_loaded', array('Tokaruk_Leads_CF7', 'init'));
add_action('rest_api_init', array('Tokaruk_Leads_Rest', 'register'));
