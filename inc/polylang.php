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
        'Cookie policy' => 'Cookie policy',
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
        'Case: solution' => 'The solution',
        'Case: feedback' => 'Client feedback',
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
        'Policy: last updated' => 'Last updated:',
        'Policy: contents' => 'Contents',
        '404: title' => 'Page not found',
        '404: text' => 'This page took infinitely long to load — probably because it doesn’t exist.',
        '404: back home' => 'Back to home',
        '404: status' => 'Status',
        '404: note' => 'Everything else on this site loads fast. Promise.',
        'Cookie: text' => 'This site uses cookies. Necessary ones keep it working; analytics (Google Analytics 4, Microsoft Clarity) and marketing (Google Ads conversion tracking) only with your consent.',
        'Cookie: banner label' => 'Cookies',
        'Cookie: settings' => 'Settings',
        'Cookie: reject' => 'Reject',
        'Cookie: accept' => 'Accept',
        'Cookie: panel title' => 'Cookie settings',
        'Cookie: close' => 'Close',
        'Cookie: necessary' => 'Necessary',
        'Cookie: necessary text' => 'Keep the site and forms working.',
        'Cookie: always on' => 'Always on',
        'Cookie: analytics' => 'Analytics',
        'Cookie: analytics text' => 'Google Analytics 4 and Microsoft Clarity — help me see which pages are useful.',
        'Cookie: marketing' => 'Marketing',
        'Cookie: marketing text' => 'Google Ads (conversion tracking).',
        'Cookie: save' => 'Save choices',
        'Cookie: accept all' => 'Accept all',
        'Blog: title' => 'Notes on fast WordPress sites',
        'Blog: lead' => 'Practical notes on speed, WooCommerce and integrations — with numbers from real projects.',
        'Blog: name' => 'Blog',
        'Blog: latest' => 'Latest',
        'Blog: read post' => 'Read the post',
        'Blog: min read' => '%s min read',
        'Blog: posts count' => '%s posts',
        'Blog: post count (one)' => '%s post',
        'Blog: posts count (2–4)' => '%s posts (2–4)',
        'Blog: published' => 'Published',
        'Blog: updated' => 'Updated',
        'Blog: contents' => 'Contents',
        'Blog: contents sections' => '%s sections',
        'Blog: author' => 'Yan Tokaruk',
        'Blog: author initials' => 'YT',
        'Blog: author role' => 'WordPress developer · Lutsk',
        'Blog: author text' => 'Building WordPress sites since 2020. I write about speed, WooCommerce and integrations.',
        'Blog: author link process' => 'Process',
        'Blog: author link contact' => 'Contact',
        'Blog: need help' => 'Need help?',
        'Blog: need help label' => 'Need help',
        'Blog: cta title' => 'Site is slow and you can’t see why?',
        'Blog: cta text' => 'I’ll look at your site and give you a fixed price.',
        'Blog: fill brief' => 'Fill in the brief',
        'Blog: next title' => 'Next articles',
        'Blog: next text' => 'A new article every few weeks. Meanwhile, see what I have already built.',
        'Blog: next link' => 'See the work',
        'Blog: newer' => 'Newer posts',
        'Blog: older' => 'Older posts',
        'Blog: page of' => 'Page %1$s of %2$s',
        'Blog: pagination label' => 'Pages',
        'Blog: related' => 'Keep reading',
        'Blog: related title' => 'Related reading',
        'Blog: related service' => 'Service',
        'Blog: related service price' => 'from $20/hour',
        'Blog: questions' => 'Questions',
        'Blog: filter label' => 'Filter',
        'Blog: all' => 'All',
        'Blog: no posts' => 'No posts yet.',
        'Blog: sticky telegram' => 'Telegram',
        'Blog: sticky discuss' => 'Let’s talk',
        'Blog: sticky contents' => 'Contents',
        'Blog: close' => 'Close',
        'Blog: seo title' => 'Blog: notes on fast WordPress sites | YT Web Co.',
        'Blog: seo description' => 'Practical notes on speed, WooCommerce and integrations, with numbers from real projects.',
        'Blog: category title' => '%s: Blog | YT Web Co.',
        'Blog: category description' => 'Articles on “%s” from the YT Web Co. blog: practical notes with numbers from real projects.',
        'Blog: quick actions' => 'Quick actions',
        'Blog: close contents' => 'Close contents',
        'Blog: all posts' => 'All posts',
        'Blog: copy' => 'Copy',
        'Blog: copied' => 'Copied',
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
        'ua' => array(
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
            'Cookie policy' => 'Політика cookies',
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
            'Case: solution' => 'Рішення',
            'Case: feedback' => 'Відгук клієнта',
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
            'Policy: last updated' => 'Оновлено:',
            'Policy: contents' => 'Зміст',
            '404: title' => 'Сторінку не знайдено',
            '404: text' => 'Ця сторінка завантажувалася нескінченно довго — мабуть, тому що її не існує.',
            '404: back home' => 'На головну',
            '404: status' => 'Статус',
            '404: note' => 'Усе інше на цьому сайті завантажується швидко. Обіцяю.',
            'Cookie: text' => 'Сайт використовує cookies. Необхідні — для роботи сайту, аналітичні (Google Analytics 4, Microsoft Clarity) і маркетингові (відстеження конверсій Google Ads) — лише з вашої згоди.',
            'Cookie: banner label' => 'Cookies',
            'Cookie: settings' => 'Налаштування',
            'Cookie: reject' => 'Відхилити',
            'Cookie: accept' => 'Прийняти',
            'Cookie: panel title' => 'Налаштування cookies',
            'Cookie: close' => 'Закрити',
            'Cookie: necessary' => 'Необхідні',
            'Cookie: necessary text' => 'Для роботи сайту та форм.',
            'Cookie: always on' => 'Завжди увімкнені',
            'Cookie: analytics' => 'Аналітика',
            'Cookie: analytics text' => 'Google Analytics 4 і Microsoft Clarity — показують, які сторінки корисні.',
            'Cookie: marketing' => 'Маркетинг',
            'Cookie: marketing text' => 'Google Ads (відстеження конверсій).',
            'Cookie: save' => 'Зберегти вибір',
            'Cookie: accept all' => 'Прийняти всі',
            'Blog: title' => 'Блог про швидкі сайти на WordPress',
            'Blog: lead' => 'Практичні нотатки про швидкість, WooCommerce та інтеграції — з цифрами з реальних проєктів.',
            'Blog: name' => 'Блог',
            'Blog: latest' => 'Остання стаття',
            'Blog: read post' => 'Читати статтю',
            'Blog: min read' => '%s хв читання',
            'Blog: posts count' => '%s статей',
            'Blog: post count (one)' => '%s стаття',
            'Blog: posts count (2–4)' => '%s статті',
            'Blog: published' => 'Опубліковано',
            'Blog: updated' => 'Оновлено',
            'Blog: contents' => 'Зміст',
            'Blog: contents sections' => '%s розділів',
            'Blog: author' => 'Ян Токарук',
            'Blog: author initials' => 'ЯТ',
            'Blog: author role' => 'WordPress-розробник · Луцьк',
            'Blog: author text' => 'Роблю сайти на WordPress з 2020 року. Пишу про швидкість, WooCommerce та інтеграції.',
            'Blog: author link process' => 'Як працюю',
            'Blog: author link contact' => 'Контакти',
            'Blog: need help' => 'Потрібна допомога?',
            'Blog: need help label' => 'Потрібна допомога',
            'Blog: cta title' => 'Сайт гальмує, а причину не видно?',
            'Blog: cta text' => 'Я подивлюся ваш сайт і назву фіксовану ціну.',
            'Blog: fill brief' => 'Заповнити бриф',
            'Blog: next title' => 'Наступні статті',
            'Blog: next text' => 'Нова стаття — раз на кілька тижнів. А поки можна подивитися, що я вже зробив.',
            'Blog: next link' => 'Дивитися роботи',
            'Blog: newer' => 'Новіші записи',
            'Blog: older' => 'Старіші записи',
            'Blog: page of' => 'Сторінка %1$s з %2$s',
            'Blog: pagination label' => 'Сторінки',
            'Blog: related' => 'Читайте далі',
            'Blog: related title' => 'Схожі статті',
            'Blog: related service' => 'Послуга',
            'Blog: related service price' => 'від 5 000 грн',
            'Blog: questions' => 'Питання',
            'Blog: filter label' => 'Фільтр',
            'Blog: all' => 'Усі',
            'Blog: no posts' => 'Статей поки немає.',
            'Blog: sticky telegram' => 'Telegram',
            'Blog: sticky discuss' => 'Обговорити',
            'Blog: sticky contents' => 'Зміст',
            'Blog: close' => 'Закрити',
            'Blog: seo title' => 'Блог про швидкі сайти на WordPress | YT Web Co.',
            'Blog: seo description' => 'Практичні нотатки про швидкість, WooCommerce та інтеграції з цифрами з реальних проєктів.',
            'Blog: category title' => '%s: блог | YT Web Co.',
            'Blog: category description' => 'Статті на тему «%s» з блогу YT Web Co.: практичні нотатки з цифрами з реальних проєктів.',
            'Blog: quick actions' => 'Швидкі дії',
            'Blog: close contents' => 'Закрити зміст',
            'Blog: all posts' => 'Усі статті',
            'Blog: copy' => 'Копіювати',
            'Blog: copied' => 'Скопійовано',
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

// hreflang uk / en + x-default → always the English version (/en/…), even though UA is the default language
add_filter('pll_rel_hreflang_attributes', function ($hreflangs) {
    if (!empty($hreflangs['en'])) {
        $hreflangs['x-default'] = $hreflangs['en'];
    }

    return $hreflangs;
});

// UA used to live under /ua/; it is the default language now (no prefix). 301 old links: /ua/x → /x
add_action('init', function () {
    $home_path = (string)wp_parse_url(home_url('/'), PHP_URL_PATH);
    $uri = (string)($_SERVER['REQUEST_URI'] ?? '');

    if (is_admin() || strpos($uri, $home_path) !== 0) {
        return;
    }

    $path = substr($uri, strlen($home_path));
    if (preg_match('~^ua(?:/(.*))?$~', strtok($path, '?') ?: '', $match)) {
        $query = strpos($path, '?') !== false ? substr($path, strpos($path, '?')) : '';
        wp_redirect(home_url('/' . ($match[1] ?? '')) . $query, 301);
        exit;
    }
}, 0);

/**
 * Plural form of a count for the current language: "one", "few" (Ukrainian 2–4) or "many".
 * Decided by the locale, not the URL slug (the Ukrainian language uses the "ua" slug and the "uk" locale).
 */
function theme_plural_form($count)
{
    $locale = function_exists('pll_current_language') ? (string)pll_current_language('locale') : get_locale();
    $count = abs((int)$count);

    if (strpos($locale, 'uk') === 0) {
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
    $labels = array('en' => 'EN', 'ua' => 'UA');

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

    // Before Polylang is set up: UA is the default language at /, EN lives at /en/.
    return array(
        array('slug' => 'en', 'label' => 'EN', 'url' => home_url('/en/'), 'current' => false),
        array('slug' => 'ua', 'label' => 'UA', 'url' => home_url('/'), 'current' => true),
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
