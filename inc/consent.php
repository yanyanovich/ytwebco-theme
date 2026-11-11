<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cookie consent with Google Consent Mode v2, by region.
 * - Outside the EEA, the UK and Switzerland, analytics (GA4 + Clarity) and Ads tags run from the first page view; the
 *   banner still lets a visitor refuse, and an explicit choice always wins.
 * - Inside those regions the default is "denied": gtag.js is loaded in Advanced Consent Mode (cookieless pings, no
 *   cookies), Clarity stays off, and nothing more happens until the visitor accepts.
 * - GA4 / Ads decide the region from the visitor's IP (Consent Mode `region`); Clarity has no such switch, so it uses the
 *   browser time zone (a Europe/* zone other than Kyiv, Moscow, Minsk, Istanbul, Chisinau counts as strict).
 * - The choice lives in the yt_consent cookie for 6 months: {"a": analytics, "m": marketing, "v": 1}.
 * - window.ytConsent.analytics() tells the rest of the JS whether analytics events may be sent.
 * IDs come from Theme settings → Tracking.
 */
function theme_get_tracking_ids()
{
    $get = function ($name) {
        return function_exists('get_field') ? trim((string)get_field('theme_settings_' . $name, 'theme-settings')) : '';
    };

    return array(
        'ga4' => preg_match('/^G-[A-Z0-9]+$/i', $get('ga4_id')) ? $get('ga4_id') : '',
        'ads' => preg_match('/^AW-\d+$/i', $get('ads_id')) ? $get('ads_id') : '',
        'label' => preg_replace('/[^A-Za-z0-9_-]/', '', $get('ads_label')),
        'clarity' => preg_replace('/[^a-z0-9]/i', '', $get('clarity_id')),
    );
}

// First thing in <head>, before any tag could read the consent state
add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="consent-mode">
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        // Specific region first, then the worldwide default (Google applies the most specific match)
        gtag('consent', 'default', {
            ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', analytics_storage: 'denied',
            functionality_storage: 'granted', security_storage: 'granted', wait_for_update: 500,
            region: ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'IS', 'LI', 'NO', 'GB', 'CH']
        });
        gtag('consent', 'default', {
            ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted', analytics_storage: 'granted',
            functionality_storage: 'granted', security_storage: 'granted'
        });
        gtag('set', 'ads_data_redaction', true);
        (function () {
            var ids = <?php echo wp_json_encode(theme_get_tracking_ids()); ?>, loaded = false;
            var state = function (on) { return on ? 'granted' : 'denied'; };
            function read() {
                var match = document.cookie.match(/(?:^|; )yt_consent=([^;]*)/);
                if (!match) return null;
                try {
                    var choice = JSON.parse(decodeURIComponent(match[1]));
                    return choice && choice.v === 1 ? choice : null;
                } catch (e) {
                    return null;
                }
            }
            // Strict (GDPR / ePrivacy) visitor guess from the time zone, for tags that cannot ask Google for the region
            function strictZone() {
                var zone = '';
                try { zone = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (e) {}
                if (/^(Europe\/(Kyiv|Kiev|Uzhgorod|Zaporozhye|Simferopol|Moscow|Minsk|Istanbul|Chisinau|Samara|Volgograd|Kirov|Astrakhan|Saratov|Ulyanovsk))$/.test(zone)) return false;
                return /^(Europe\/|Atlantic\/(Reykjavik|Madeira|Azores|Canary|Faroe)|Arctic\/Longyearbyen|Africa\/Ceuta)/.test(zone) || zone === '';
            }
            function clarityLoad(allowed) {
                if (!ids.clarity) return;
                if (!allowed) {
                    if (window.clarity) window.clarity('consent', false);
                    return;
                }
                if (!window.clarity) {
                    (function (c, l, a, r, i, t, y) {
                        c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
                        t = l.createElement(r); t.async = 1; t.src = 'https://www.clarity.ms/tag/' + i;
                        y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
                    })(window, document, 'clarity', 'script', ids.clarity);
                }
                window.clarity('consent', true);
            }
            // gtag.js is always loaded (Advanced Consent Mode): the consent state decides what it may store or send
            function loadGoogle() {
                var first = ids.ga4 || ids.ads;
                if (loaded || !first) return;
                loaded = true;
                var script = document.createElement('script');
                script.async = true;
                script.src = 'https://www.googletagmanager.com/gtag/js?id=' + first;
                document.head.appendChild(script);
                gtag('js', new Date());
                if (ids.ga4) gtag('config', ids.ga4);
                if (ids.ads) gtag('config', ids.ads);
            }
            // The heavy tag scripts wait for the theme's first-interaction hook (main.js → initOnUserInteraction → ytConsent.start) or 10 s so they never count against PageSpeed / LCP;
            // the consent state above is set at once, and gtag() calls made earlier are queued in dataLayer
            var ready = false, queue = [];
            function whenReady(fn) { if (ready) fn(); else queue.push(fn); }
            function release() {
                if (ready) return;
                ready = true;
                queue.splice(0).forEach(function (fn) { fn(); });
            }
            setTimeout(release, 10000);
            function apply(choice) {
                gtag('consent', 'update', {
                    analytics_storage: state(choice.a), ad_storage: state(choice.m),
                    ad_user_data: state(choice.m), ad_personalization: state(choice.m)
                });
                whenReady(function () { clarityLoad(!!choice.a); loadGoogle(); });
            }
            window.ytConsent = {
                ids: ids,
                start: release,
                get: read,
                // May analytics events be sent? An explicit choice wins; otherwise only outside the strict regions
                analytics: function () {
                    var choice = read();
                    return choice ? !!choice.a : !strictZone();
                },
                marketing: function () {
                    var choice = read();
                    return choice ? !!choice.m : !strictZone();
                },
                set: function (choice) {
                    choice = {a: choice.a ? 1 : 0, m: choice.m ? 1 : 0, v: 1};
                    document.cookie = 'yt_consent=' + encodeURIComponent(JSON.stringify(choice)) + '; max-age=15552000; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
                    document.documentElement.classList.remove('needs-consent');
                    apply(choice);
                }
            };
            var saved = read();
            if (saved) apply(saved);
            else {
                document.documentElement.classList.add('needs-consent');
                // No choice yet: Google follows the region defaults above; Clarity only outside the strict zones
                whenReady(function () { clarityLoad(!strictZone()); loadGoogle(); });
            }
        })();
    </script>
    <?php
}, 1);

add_action('wp_footer', function () {
    get_template_part('template-parts/partials/cookie-banner');
}, 5);
