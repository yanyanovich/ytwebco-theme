<?php
/**
 * 404 (NotFound · Desktop / Mobile): big "404", the joke perf report (status 404, LCP ∞) and links back.
 *
 * @package ytweb_theme
 */

get_header();

// The requested path in the report: "GET /old-page" (trimmed, no query string)
$request_path = '/' . ltrim((string)wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH), '/');
$home_path = (string)wp_parse_url(home_url('/'), PHP_URL_PATH);
if ($home_path !== '/' && strpos($request_path, $home_path) === 0) {
    $request_path = '/' . substr($request_path, strlen($home_path));
}
if (mb_strlen($request_path) > 28) {
    $request_path = mb_substr($request_path, 0, 27) . '…';
}

$home_url = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
?>

    <main id="content" tabindex="-1" class="site-main not-found bp">
        <div class="container not-found__inner grid-12">
            <div class="not-found__main">
                <span class="not-found__code mono" aria-hidden="true">4<span class="accent">0</span>4</span>
                <p class="not-found__meta mono">404 · <?php echo esc_html(theme_t('Page not found')); ?> · LCP: <span class="accent">∞</span></p>
                <h1 class="not-found__title"><?php echo esc_html(theme_t('Page not found')); ?></h1>
                <p class="not-found__text"><?php echo esc_html(theme_t('This page took infinitely long to load — probably because it doesn’t exist.')); ?></p>
                <div class="not-found__actions">
                    <a class="btn btn--primary" href="<?php echo esc_url($home_url); ?>">
                        <?php echo esc_html(theme_t('Back to home')); ?>
                        <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                    </a>
                    <a class="btn btn--secondary" href="<?php echo esc_url(trailingslashit($home_url) . '#services'); ?>"><?php echo esc_html(theme_t('Services')); ?></a>
                    <a class="btn btn--secondary" href="<?php echo esc_url(get_post_type_archive_link('case')); ?>"><?php echo esc_html(theme_t('Work')); ?></a>
                </div>
            </div>

            <div class="not-found__report">
                <div class="not-found__card" role="group" aria-label="<?php echo esc_attr(theme_t('Performance report of this site')); ?>">
                    <div class="not-found__card-head">
                        <span class="label">perf.report</span>
                        <span class="label">GET <?php echo esc_html($request_path); ?></span>
                    </div>
                    <dl class="not-found__rows">
                        <div class="not-found__row">
                            <dt class="label"><?php echo esc_html(theme_t('Status')); ?></dt>
                            <dd class="mono not-found__status">404</dd>
                        </div>
                        <div class="not-found__row">
                            <dt class="label">PageSpeed</dt>
                            <dd class="mono">—</dd>
                        </div>
                        <div class="not-found__row not-found__row--lcp">
                            <dt class="label">LCP</dt>
                            <dd class="mono accent">∞</dd>
                            <dd class="not-found__lcp" aria-hidden="true">
                                <span class="not-found__bar"></span>
                                <span class="not-found__scale mono"><span>0s</span><span><?php echo esc_html(theme_t('good ≤ 2.5s')); ?></span><span>∞ →</span></span>
                            </dd>
                        </div>
                    </dl>
                </div>
                <p class="not-found__note mono"><?php echo esc_html(theme_t('Everything else on this site loads fast. Promise.')); ?></p>
            </div>
        </div>
    </main>

<?php
get_footer();
