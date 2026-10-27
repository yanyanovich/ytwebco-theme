<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact details from Theme settings → Contacts. Telegram falls back to the handle
 * from content.md; email / phone stay visible as [placeholders] until they are filled in.
 *
 * @return array{telegram: string, telegram_url: string, email: string, email_url: string, phone: string, phone_url: string, booking_url: string}
 */
function theme_get_contacts()
{
    static $contacts = null;

    if ($contacts !== null) {
        return $contacts;
    }

    $get = function ($name) {
        return function_exists('get_field') ? trim((string)get_field('theme_settings_' . $name, 'theme-settings')) : '';
    };

    $telegram = $get('telegram');
    $telegram_url = $get('telegram_url');
    if ($telegram && !$telegram_url) {
        $telegram_url = 'https://t.me/' . ltrim($telegram, '@');
    }

    $email = $get('email');
    $phone = $get('phone');

    $contacts = array(
        'telegram' => $telegram ?: '@yanyanovic',
        'telegram_url' => $telegram_url ?: 'https://t.me/yanyanovic',
        'email' => $email ?: '[email]',
        'email_url' => $email ? 'mailto:' . antispambot($email) : '#',
        'phone' => $phone ?: '[+380 XX XXX XX XX]',
        'phone_url' => $phone ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : '#',
        'booking_url' => $get('booking_url') ?: '#',
    );

    return $contacts;
}

/**
 * Social links for the footer "Elsewhere" column (Theme settings → Contacts → Socials).
 *
 * @return array<int, array{label: string, url: string}>
 */
function theme_get_socials()
{
    $rows = function_exists('get_field') ? get_field('theme_settings_socials', 'theme-settings') : array();
    $socials = array();

    foreach ((array)$rows as $row) {
        if (!empty($row['label'])) {
            $socials[] = array(
                'label' => $row['label'],
                'url' => !empty($row['url']) ? $row['url'] : '#',
            );
        }
    }

    return $socials;
}

/**
 * Inline an SVG from the theme folder (logo lockups, icons from design/assets).
 * Decorative by default: aria-hidden + focusable="false"; extra attributes go on <svg>.
 */
function theme_svg($relative_path, array $attrs = array())
{
    static $cache = array();

    if (!isset($cache[$relative_path])) {
        $file = get_template_directory() . '/' . ltrim($relative_path, '/');
        $cache[$relative_path] = is_readable($file) ? trim(file_get_contents($file)) : '';
    }

    $svg = $cache[$relative_path];

    if ($svg === '') {
        return '';
    }

    $attrs = array_merge(array('aria-hidden' => 'true', 'focusable' => 'false'), $attrs);
    $attr_string = '';
    foreach ($attrs as $name => $value) {
        $attr_string .= ' ' . $name . '="' . esc_attr($value) . '"';
    }

    return preg_replace('/^<svg\b/', '<svg' . $attr_string, $svg, 1);
}

/**
 * UI icon from media/icons (stroke, currentColor): 16px UI icons, 20px contact icons.
 *
 * @param string $name arrow-right | arrow-up-right | arrow-up | arrow-down | chevron-down | check |
 *                     burger | close | plus | telegram | email | phone | calendar | location | alert
 */
function theme_icon($name, $size = 16)
{
    return theme_svg('media/icons/icon-' . $name . '.svg', array(
        'width' => (int)$size,
        'height' => (int)$size,
    ));
}
