<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact details from Theme settings → Contacts. Empty values stay visible
 * as [placeholders] until they are filled in.
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
        'telegram' => $telegram ?: '[@username]',
        'telegram_url' => $telegram_url ?: '#',
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
 * Inline SVG icons used across the theme (stroke icons, inherit currentColor).
 */
function theme_icon($name, $size = 16)
{
    $paths = array(
        'arrow-right' => '<path d="M3 8h10M9 4l4 4-4 4"/>',
        'arrow-diag' => '<path d="M4 12 12 4M5 4h7v7"/>',
        'chevron-down' => '<path d="m4 6 4 4 4-4"/>',
        'plus' => '<path d="M8 3v10M3 8h10"/>',
        'close' => '<path d="M3 3l10 10M13 3 3 13"/>',
        'burger' => '<path d="M2 4.5h12M2 8h12M2 11.5h8"/>',
    );

    if (!isset($paths[$name])) {
        return '';
    }

    return sprintf(
        '<svg width="%1$d" height="%1$d" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true" focusable="false">%2$s</svg>',
        (int)$size,
        $paths[$name]
    );
}
