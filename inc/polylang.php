<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme UI strings. Registered in Polylang (Languages → Translations, group "Theme"),
 * printed through theme_t(). English is the source language.
 */
function theme_get_translatable_strings()
{
    return array(
        'Skip link' => 'Skip to content',
        'Logo role' => 'Web studio · Lutsk',
        'Logo label' => 'YT Web Co. — home',
        'CTA: start a project' => 'Start a project',
        'All services' => 'All services',
        'Menu label' => 'Menu',
        'Open menu' => 'Open menu',
        'Close menu' => 'Close menu',
        'Reply time' => 'I reply within one working day.',
        'Status: available' => 'Available for new projects',
        'Footer headline' => 'Let’s build something fast',
        'Footer: location' => 'Lutsk',
        'Footer: signature' => 'YT Web Co. · founded by Yan Tokaruk',
        'Footer: services column' => 'Services',
        'Footer: site column' => 'Site',
        'Footer: elsewhere column' => 'Elsewhere',
        'Footer: language column' => 'Language',
        'Main navigation' => 'Main',
        'Footer: built on' => 'Built on WordPress',
        'Privacy policy' => 'Privacy policy',
        'Cookie settings' => 'Cookie settings',
        'Back to top' => 'Back to top ↑',
        'Opens in a new tab' => 'opens in a new tab',
    );
}

add_action('init', function () {
    if (!function_exists('pll_register_string')) {
        return;
    }

    foreach (theme_get_translatable_strings() as $name => $string) {
        pll_register_string($name, $string, 'Theme');
    }
});

/**
 * Translated theme string (falls back to the English source without Polylang).
 */
function theme_t($string)
{
    return function_exists('pll__') ? pll__($string) : $string;
}

/**
 * Languages for the EN / UA switch. Plain links, no redirect by browser language.
 *
 * @return array<int, array{slug: string, label: string, url: string, current: bool}>
 */
function theme_get_languages()
{
    $labels = array('en' => 'EN', 'uk' => 'UA');

    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(array('raw' => 1, 'hide_if_empty' => 0));
        $result = array();

        foreach ((array)$languages as $language) {
            $slug = $language['slug'];
            $result[] = array(
                'slug' => $slug,
                'label' => $labels[$slug] ?? strtoupper($slug),
                'url' => $language['url'],
                'current' => !empty($language['current_lang']),
            );
        }

        if (!empty($result)) {
            return $result;
        }
    }

    // Before Polylang is set up: EN is the only real language, UA lives at /uk/.
    return array(
        array('slug' => 'en', 'label' => 'EN', 'url' => home_url('/'), 'current' => true),
        array('slug' => 'uk', 'label' => 'UA', 'url' => home_url('/uk/'), 'current' => false),
    );
}

/**
 * Translated permalink of a page by its English path, with a fallback URL.
 */
function theme_get_page_url($path)
{
    $page = get_page_by_path($path);

    if ($page) {
        $page_id = function_exists('pll_get_post') ? (pll_get_post($page->ID) ?: $page->ID) : $page->ID;
        return get_permalink($page_id);
    }

    $home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

    return trailingslashit($home) . trailingslashit($path);
}
