<?php
/**
 * Cookie banner + settings panel (yt/cookie-banner frames). Shown on the first visit (html.needs-consent)
 * and reopened by any [data-cookie-settings] button (footer "Cookie settings"). Logic: setup-cookie-consent.js.
 */
$privacy_id = (int)get_option('wp_page_for_privacy_policy');
if ($privacy_id && function_exists('pll_get_post')) {
    $privacy_id = (int)pll_get_post($privacy_id) ?: $privacy_id;
}
$categories = array(
    'necessary' => array(theme_t('Necessary'), theme_t('Keep the site and forms working.')),
    'a' => array(theme_t('Analytics'), theme_t('Google Analytics 4 — helps me see which pages are useful.')),
    'm' => array(theme_t('Marketing'), theme_t('Google Ads (conversion tracking).')),
);
?>
<div class="cookie-banner" data-cookie-banner role="region" aria-label="<?php echo esc_attr(theme_t('Cookies')); ?>">
    <span class="cookie-banner__icon mono" aria-hidden="true">{ }</span>
    <p class="cookie-banner__text">
        <?php echo esc_html(theme_t('This site uses cookies. Necessary ones keep it working; analytics (Google Analytics 4) and marketing (Google Ads conversion tracking) only with your consent.')); ?>
        <?php if ($privacy_id) : ?>
            <a class="link-inline" href="<?php echo esc_url(get_permalink($privacy_id)); ?>"><?php echo esc_html(theme_t('Privacy policy')); ?></a>
        <?php endif; ?>
        <a class="link-inline" href="<?php echo esc_url(theme_get_page_url('cookies')); ?>"><?php echo esc_html(theme_t('Cookie policy')); ?></a>
    </p>
    <div class="cookie-banner__actions">
        <button type="button" class="btn btn--ghost btn--sm cookie-banner__settings" data-cookie-settings><?php echo esc_html(theme_t('Settings')); ?></button>
        <button type="button" class="btn btn--secondary btn--sm" data-cookie-choice="reject"><?php echo esc_html(theme_t('Reject')); ?></button>
        <button type="button" class="btn btn--primary btn--sm" data-cookie-choice="accept"><?php echo esc_html(theme_t('Accept')); ?></button>
    </div>
</div>

<div class="cookie-panel" data-cookie-panel role="dialog" aria-modal="true" aria-labelledby="cookie-panel-title" hidden>
    <div class="cookie-panel__head">
        <h2 class="cookie-panel__title" id="cookie-panel-title" tabindex="-1"><?php echo esc_html(theme_t('Cookie settings')); ?></h2>
        <button type="button" class="cookie-panel__close" data-cookie-close aria-label="<?php echo esc_attr(theme_t('Close')); ?>"><?php echo theme_icon('close'); ?></button>
    </div>
    <ul class="cookie-panel__list">
        <?php foreach ($categories as $key => list($title, $text)) :
            $id = 'cookie-' . $key;
            ?>
            <li class="cookie-panel__item">
                <label class="cookie-panel__label" for="<?php echo esc_attr($id); ?>">
                    <span class="cookie-panel__name"><?php echo esc_html($title); ?></span>
                    <span class="cookie-panel__desc"><?php echo esc_html($text); ?></span>
                </label>
                <?php if ($key === 'necessary') : ?>
                    <span class="cookie-panel__always mono"><?php echo esc_html(theme_t('Always on')); ?></span>
                <?php endif; ?>
                <input class="switch" type="checkbox" role="switch" id="<?php echo esc_attr($id); ?>"
                    <?php echo $key === 'necessary' ? 'checked disabled' : 'data-cookie-category="' . esc_attr($key) . '"'; ?>
                    <?php echo $key === 'a' ? 'checked' : ''; ?>>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="cookie-panel__actions">
        <button type="button" class="btn btn--secondary" data-cookie-choice="save"><?php echo esc_html(theme_t('Save choices')); ?></button>
        <button type="button" class="btn btn--primary" data-cookie-choice="accept"><?php echo esc_html(theme_t('Accept all')); ?></button>
    </div>
</div>
