<?php
/**
 * The template for displaying the footer
 *
 * CTA band, live status line (Lutsk time), link columns, signature, legal row.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ytweb_theme
 */

$menu = theme_get_menu_tree('header_menu');
$contacts = theme_get_contacts();
$socials = theme_get_socials();
$contact_url = theme_get_page_url('contact');
$privacy_url = get_privacy_policy_url() ?: theme_get_page_url('privacy');
$pagespeed = function_exists('get_field') ? get_field('theme_settings_pagespeed', 'theme-settings') : '';

// Services column = children of the menu item that has them; Site column = the other top-level items
$services = array();
$site_links = array();
foreach ($menu as $item) {
    if (!empty($item['children'])) {
        $services = $item['children'];
    } else {
        $site_links[] = $item;
    }
}

// UTC offset is computed for Europe/Kyiv (UTC+2 winter / UTC+3 summer), never hardcoded.
// The time itself is filled in by setup-footer.js; the server renders a same-width placeholder.
$kyiv_offset = (new DateTimeImmutable('now', new DateTimeZone('Europe/Kyiv')))->getOffset() / HOUR_IN_SECONDS;
$utc_label = 'UTC' . ($kyiv_offset >= 0 ? '+' : '−') . abs($kyiv_offset);
?>
<footer class="footer bp" data-footer>
    <div class="container">
        <div class="footer__cta grid-12">
            <div class="footer__cta-main">
                <h2 class="h2 footer__headline"><?php echo esc_html(theme_t('Let’s build something fast')); ?><span class="accent">.</span></h2>
                <a class="btn btn--primary footer__cta-btn" href="<?php echo esc_url($contact_url); ?>">
                    <?php echo esc_html(theme_t('Start a project')); ?>
                    <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                </a>
            </div>
            <div class="footer__channels">
                <div class="footer__channel">
                    <span class="label">Telegram</span>
                    <a class="link-ul mono footer__channel-link" href="<?php echo esc_url($contacts['telegram_url']); ?>" data-track="telegram"><?php echo esc_html($contacts['telegram']); ?></a>
                </div>
                <div class="footer__channel">
                    <span class="label">Email</span>
                    <a class="link-ul mono footer__channel-link" href="<?php echo esc_url($contacts['email_url']); ?>" data-track="email"><?php echo esc_html($contacts['email']); ?></a>
                </div>
            </div>
        </div>

        <div class="footer__status mono">
            <span class="footer__status-live"><span class="dot dot--pulse" aria-hidden="true"></span><?php echo esc_html(theme_t('Available for new projects')); ?></span>
            <span class="footer__status-sep" aria-hidden="true">·</span>
            <span>
                <?php echo esc_html(theme_t('Lutsk')); ?> ·
                <time class="footer__time" data-live-time data-timezone="Europe/Kyiv">--:--</time> ·
                <span data-utc-offset><?php echo esc_html($utc_label); ?></span>
            </span>
        </div>

        <div class="footer__columns">
            <div class="footer__signature">
                <?php get_template_part('template-parts/partials/logo', null, array('variant' => 'footer')); ?>
            </div>

            <?php if (!empty($services)) : ?>
                <nav class="footer__col footer__col--services" aria-label="<?php echo esc_attr(theme_t('Services')); ?>">
                    <span class="label footer__col-title"><?php echo esc_html(theme_t('Services')); ?></span>
                    <ul class="footer__links dim">
                        <?php foreach ($services as $item) : ?>
                            <li><a class="link-muted"<?php echo theme_menu_link_attrs($item); ?>><?php echo esc_html($item['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php if (!empty($site_links)) : ?>
                <nav class="footer__col footer__col--site" aria-label="<?php echo esc_attr(theme_t('Site')); ?>">
                    <span class="label footer__col-title"><?php echo esc_html(theme_t('Site')); ?></span>
                    <ul class="footer__links dim">
                        <?php foreach ($site_links as $item) : ?>
                            <li><a class="link-muted"<?php echo theme_menu_link_attrs($item); ?>><?php echo esc_html($item['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php if (!empty($socials)) : ?>
                <nav class="footer__col footer__col--elsewhere" aria-label="<?php echo esc_attr(theme_t('Elsewhere')); ?>">
                    <span class="label footer__col-title"><?php echo esc_html(theme_t('Elsewhere')); ?></span>
                    <ul class="footer__links dim">
                        <?php foreach ($socials as $social) : ?>
                            <li>
                                <a class="link-muted" href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener me">
                                    <?php echo esc_html($social['label']); ?> <span aria-hidden="true">↗</span>
                                    <span class="screen-reader-text">(<?php echo esc_html(theme_t('opens in a new tab')); ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="footer__col footer__col--lang">
                <span class="label footer__col-title"><?php echo esc_html(theme_t('Language')); ?></span>
                <?php get_template_part('template-parts/partials/lang-switch'); ?>
            </div>
        </div>

        <div class="footer__legal mono">
            <span class="footer__copy">© <?php echo esc_html(wp_date('Y')); ?> YT Web Co.</span>
            <span class="footer__legal-links">
                <a class="link-muted" href="<?php echo esc_url($privacy_url); ?>"><?php echo esc_html(theme_t('Privacy policy')); ?></a>
                <button type="button" class="link-muted" data-cookie-settings><?php echo esc_html(theme_t('Cookie settings')); ?></button>
            </span>
            <span class="footer__built">
                <?php echo esc_html(theme_t('Built on WordPress')); ?> · PageSpeed
                <span class="accent"><?php echo esc_html($pagespeed ?: '[PAGESPEED]'); ?></span>
            </span>
            <a class="link-muted footer__top" href="#top"><?php echo esc_html(theme_t('Back to top')); ?></a>
        </div>
    </div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

<?php theme_print_additional_code('end_of_body'); ?>
</body>
</html>
