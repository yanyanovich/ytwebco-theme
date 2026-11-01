<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact details from Theme settings → Contacts. Telegram falls back to the handle
 * from content.md; email / phone stay visible as [placeholders] until they are filled in.
 *
 * @return array{telegram: string, telegram_url: string, email: string, email_url: string, phone: string, phone_url: string, whatsapp_url: string, booking_url: string}
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
    // wa.me needs digits only, without "+" or spaces
    $whatsapp = preg_replace('/\D/', '', $get('whatsapp') ?: $phone);

    $contacts = array(
        'telegram' => $telegram ?: '@yanyanovic',
        'telegram_url' => $telegram_url ?: 'https://t.me/yanyanovic',
        'email' => $email ?: '[email]',
        'email_url' => $email ? 'mailto:' . antispambot($email) : '#',
        'phone' => $phone ?: '[+380 XX XXX XX XX]',
        'phone_url' => $phone ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : '#',
        'whatsapp_url' => $whatsapp ? 'https://wa.me/' . $whatsapp : '#',
        'booking_url' => $get('booking_url') ?: '#',
    );

    return $contacts;
}

/**
 * Measured performance numbers from Theme settings → Site (hero badge, perf report, metrics, footer).
 * Unmeasured values stay visible as [PAGESPEED] / [LCP] placeholders.
 *
 * @return array{pagespeed: string, lcp: string}
 */
function theme_get_perf()
{
    static $perf = null;

    if ($perf === null) {
        $get = function ($name) {
            return function_exists('get_field') ? trim((string)get_field('theme_settings_' . $name, 'theme-settings')) : '';
        };

        $perf = array(
            'pagespeed' => $get('pagespeed') ?: '[PAGESPEED]',
            'lcp' => $get('lcp') ?: '[LCP]',
        );
    }

    return $perf;
}

/**
 * Escapes a heading and paints its closing full stop lime (the brand "." from the design).
 * Keeps <br> from ACF textareas with "new_lines: br".
 */
function theme_accent_period($text)
{
    $html = wp_kses((string)$text, array('br' => array()));

    return preg_replace('/\.(\s*)$/u', '<span class="accent">.</span>$1', trim($html));
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

/**
 * "Book a free call" target: the Cal.com link from Theme settings, else the Contact page.
 */
function theme_get_booking_url()
{
    $booking_url = theme_get_contacts()['booking_url'];

    return $booking_url !== '#' ? $booking_url : theme_get_page_url('contact');
}

/**
 * Splits policy content into numbered sections at each H2 and returns the TOC items.
 * "<h2>Who I am</h2><p>…</p>" → <section id="who-i-am"> with "01" before the heading.
 *
 * @return array{html: string, items: array<int, array{id: string, title: string}>}
 */
function theme_get_policy_sections($html)
{
    $parts = preg_split('#(<h2\b[^>]*>.*?</h2>)#is', (string)$html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $out = trim(array_shift($parts));
    $items = array();
    $used = array();

    for ($i = 0; $i < count($parts); $i += 2) {
        $title = trim(wp_strip_all_tags($parts[$i]));
        $id = sanitize_title($title) ?: 'section';
        $id = isset($used[$id]) ? $id . '-' . (count($items) + 1) : $id;
        $used[$id] = true;
        $items[] = array('id' => $id, 'title' => $title);

        $out .= sprintf(
            '<section class="policy__section" id="%1$s"><div class="policy__section-head"><span class="mono">%2$s</span><h2>%3$s</h2></div><div class="policy__prose">%4$s</div></section>',
            esc_attr($id),
            esc_html(sprintf('%02d', count($items))),
            esc_html($title),
            $parts[$i + 1] ?? ''
        );
    }

    return array('html' => $out, 'items' => $items);
}

