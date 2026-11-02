<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cookie consent with Google Consent Mode v2.
 * - Default: everything denied except necessary storage; GA4 / Google Ads are not loaded at all.
 * - The choice lives in the yt_consent cookie for 6 months: {"a": analytics, "m": marketing, "v": 1}.
 * - window.ytConsent.set() (cookie banner JS) saves a choice, sends the consent update and loads gtag.js
 *   with only the products that were accepted. IDs come from Theme settings → Tracking.
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
        gtag('consent', 'default', {
            ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', analytics_storage: 'denied',
            functionality_storage: 'granted', security_storage: 'granted', wait_for_update: 500
        });
        gtag('set', 'ads_data_redaction', true);
        (function () {
            var ids = <?php echo wp_json_encode(theme_get_tracking_ids()); ?>, loaded = false, configured = {};
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
            function apply(choice) {
                gtag('consent', 'update', {
                    analytics_storage: state(choice.a), ad_storage: state(choice.m),
                    ad_user_data: state(choice.m), ad_personalization: state(choice.m)
                });
                var products = [];
                if (choice.a && ids.ga4) products.push(['ga4', ids.ga4]);
                if (choice.m && ids.ads) products.push(['ads', ids.ads]);
                if (!products.length) return;
                if (!loaded) {
                    loaded = true;
                    var script = document.createElement('script');
                    script.async = true;
                    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + products[0][1];
                    document.head.appendChild(script);
                    gtag('js', new Date());
                }
                products.forEach(function (product) {
                    if (configured[product[0]]) return;
                    configured[product[0]] = true;
                    gtag('config', product[1]);
                });
            }
            window.ytConsent = {
                ids: ids,
                get: read,
                set: function (choice) {
                    choice = {a: choice.a ? 1 : 0, m: choice.m ? 1 : 0, v: 1};
                    document.cookie = 'yt_consent=' + encodeURIComponent(JSON.stringify(choice)) + '; max-age=15552000; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
                    document.documentElement.classList.remove('needs-consent');
                    apply(choice);
                }
            };
            var saved = read();
            if (saved) apply(saved);
            else document.documentElement.classList.add('needs-consent');
        })();
    </script>
    <?php
}, 1);

add_action('wp_footer', function () {
    get_template_part('template-parts/partials/cookie-banner');
}, 5);
