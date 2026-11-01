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
        'Back to top' => 'Back to top',
        'Opens in a new tab' => 'opens in a new tab',
        'Hero: live badge' => 'Live',
        'Hero: perf report label' => 'Performance report of this site',
        'Hero: LCP good threshold' => 'good ≤ 2.5s',
        'Hero: gauge label' => 'PageSpeed score %s out of 100',
        'SEO text: read more' => 'Read more',
        'SEO text: show less' => 'Show less',
        'Breadcrumb label' => 'Breadcrumb',
        'Breadcrumb: home' => 'Home',
        'Breadcrumb: work' => 'Work',
        'Case: client' => 'Client',
        'Case: role' => 'Role',
        'Case: engagement' => 'Engagement',
        'Case: year' => 'Year',
        'Case: live site' => 'Live site',
        'Case: challenge' => 'The challenge',
        'Case: what I did' => 'What I did',
        'Case: results' => 'Results',
        'Case: before' => 'Before',
        'Case: after' => 'After',
        'Case: stack' => 'Stack',
        'Case: screens' => 'Screens',
        'Case: screens meta' => 'Desktop + mobile',
        'Case: screens swipe' => 'Swipe →',
        'Case: translated note' => 'Screenshots are shown in English translation; the live site is in Ukrainian.',
        'CTA band: title' => 'Want results like this?',
        'CTA: book a call' => 'Book a free call',
        'CTA: telegram' => 'Message on Telegram',
        'Case nav label' => 'More projects',
        'Case nav: previous' => 'Previous project',
        'Case nav: next' => 'Next project',
        'Work: filter' => 'Filter',
        'Work: all' => 'All',
        'Work: projects count' => '%s projects',
        'Work: project count (one)' => '%s project',
        // Only used by languages with a separate 2–4 form (Ukrainian); English uses "%s projects"
        'Work: projects count (2–4)' => '%s projects (2–4)',
    );
}

/**
 * Translations shipped with the theme, keyed by the string name above. Written into Polylang
 * (Languages → Translations) where a translation is still empty, so they can be edited there afterwards.
 *
 * @return array<string, array<string, string>>
 */
function theme_get_string_translations()
{
    return array(
        'uk' => array(
            'Skip link' => 'Перейти до вмісту',
            'Logo role' => 'Веб-студія · Луцьк',
            'Logo label' => 'YT Web Co. — головна',
            'CTA: start a project' => 'Обговорити проєкт',
            'All services' => 'Усі послуги',
            'Menu label' => 'Меню',
            'Open menu' => 'Відкрити меню',
            'Close menu' => 'Закрити меню',
            'Reply time' => 'Відповідаю протягом одного робочого дня.',
            'Status: available' => 'Приймаю нові проєкти',
            'Footer headline' => 'Зробімо швидкий сайт',
            'Footer: location' => 'Луцьк',
            'Footer: signature' => 'YT Web Co. · засновник Ян Токарук',
            'Footer: services column' => 'Послуги',
            'Footer: site column' => 'Сайт',
            'Footer: elsewhere column' => 'Соцмережі',
            'Footer: language column' => 'Мова',
            'Main navigation' => 'Головне меню',
            'Footer: built on' => 'Працює на WordPress',
            'Privacy policy' => 'Політика конфіденційності',
            'Cookie settings' => 'Налаштування cookies',
            'Back to top' => 'Нагору',
            'Opens in a new tab' => 'відкривається в новій вкладці',
            'Hero: live badge' => 'Live',
            'Hero: perf report label' => 'Звіт про швидкість цього сайту',
            'Hero: LCP good threshold' => 'добре ≤ 2,5 с',
            'Hero: gauge label' => 'Оцінка PageSpeed %s зі 100',
            'SEO text: read more' => 'Читати далі',
            'SEO text: show less' => 'Згорнути',
            'Breadcrumb label' => 'Навігаційний ланцюжок',
            'Breadcrumb: home' => 'Головна',
            'Breadcrumb: work' => 'Роботи',
            'Case: client' => 'Клієнт',
            'Case: role' => 'Роль',
            'Case: engagement' => 'Формат',
            'Case: year' => 'Рік',
            'Case: live site' => 'Сайт',
            'Case: challenge' => 'Задача',
            'Case: what I did' => 'Що зроблено',
            'Case: results' => 'Результати',
            'Case: before' => 'До',
            'Case: after' => 'Після',
            'Case: stack' => 'Стек',
            'Case: screens' => 'Екрани',
            'Case: screens meta' => 'Десктоп + мобільна',
            'Case: screens swipe' => 'Гортайте →',
            'Case: translated note' => 'Скриншоти показано в англійському перекладі; сайт працює українською.',
            'CTA band: title' => 'Хочете такий результат?',
            'CTA: book a call' => 'Отримати консультацію',
            'CTA: telegram' => 'Написати в Telegram',
            'Case nav label' => 'Інші проєкти',
            'Case nav: previous' => 'Попередній проєкт',
            'Case nav: next' => 'Наступний проєкт',
            'Work: filter' => 'Фільтр',
            'Work: all' => 'Усі',
            'Work: projects count' => '%s проєктів',
            'Work: project count (one)' => '%s проєкт',
            'Work: projects count (2–4)' => '%s проєкти',
        ),
    );
}

/**
 * Fills empty Polylang string translations from theme_get_string_translations().
 * Runs in the admin when the shipped set changes; never overwrites a translation edited in Polylang.
 */
add_action('admin_init', function () {
    if (!class_exists('PLL_MO') || !function_exists('PLL') || empty(PLL()->model)) {
        return;
    }

    $translations = theme_get_string_translations();
    $version = md5(wp_json_encode($translations));
    if (get_option('theme_string_translations_version') === $version) {
        return;
    }

    $strings = theme_get_translatable_strings();
    foreach ($translations as $slug => $list) {
        $language = PLL()->model->get_language($slug);
        if (!$language) {
            continue;
        }

        $mo = new PLL_MO();
        $mo->import_from_db($language);
        foreach ($list as $name => $translation) {
            if (isset($strings[$name]) && $mo->translate($strings[$name]) === $strings[$name]) {
                $mo->add_entry($mo->make_entry($strings[$name], $translation));
            }
        }
        $mo->export_to_db($language);
    }

    update_option('theme_string_translations_version', $version, false);
});

/**
 * Plural form of a count for the current language: "one", "few" (Ukrainian 2–4) or "many".
 */
function theme_plural_form($count)
{
    $language = function_exists('pll_current_language') ? pll_current_language() : 'en';
    $count = abs((int)$count);

    if ($language === 'uk') {
        if ($count % 10 === 1 && $count % 100 !== 11) {
            return 'one';
        }
        if ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 12 || $count % 100 > 14)) {
            return 'few';
        }
        return 'many';
    }

    return $count === 1 ? 'one' : 'many';
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

    // Language root, not pll_home_url(): that returns the translated front page permalink
    $prefix = '';
    if (function_exists('pll_current_language') && pll_current_language() !== pll_default_language()) {
        $prefix = pll_current_language() . '/';
    }

    return home_url('/' . $prefix . trailingslashit($path));
}
