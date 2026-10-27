<?php
if (!defined('ABSPATH')) {
    exit;
}

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'top';
$block_classes = array(
    'home-hero',
    !empty($block['className']) ? $block['className'] : '',
    !empty($block['align']) ? 'align' . $block['align'] : '',
);

$pagespeed = function_exists('get_field')
    ? get_field('theme_settings_pagespeed', 'theme-settings')
    : '';
$pagespeed = $pagespeed !== '' && $pagespeed !== null ? (string)$pagespeed : '[PAGESPEED]';
$lcp = '[LCP]';
?>

<section
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
    data-section
>
    <div class="home-hero__inner">
        <div class="home-hero__badge mono" aria-label="<?php echo esc_attr(sprintf('Live performance: PageSpeed %s, LCP %s', $pagespeed, $lcp)); ?>">
            <span class="dot" aria-hidden="true"></span>
            <span class="home-hero__live">LIVE</span>
            <span class="home-hero__badge-divider" aria-hidden="true"></span>
            <span>PageSpeed <strong><?php echo esc_html($pagespeed); ?></strong> · LCP <strong><?php echo esc_html($lcp); ?></strong></span>
        </div>

        <h1 class="home-hero__title">
            Fast websites<br class="home-hero__desktop-break"> that sell<span>.</span>
        </h1>

        <div class="home-hero__body">
            <div class="home-hero__copy">
                <p class="home-hero__lead">WordPress, WooCommerce &amp; Shopify developer. Custom code, real performance, clear communication.</p>
                <div class="home-hero__actions">
                    <a class="btn btn--primary home-hero__button" href="#contact">
                        Book a free call
                        <span class="arrow" aria-hidden="true"><?php echo theme_icon('arrow-right', 16); ?></span>
                    </a>
                    <a class="btn btn--secondary home-hero__button" href="#work">See my work</a>
                </div>
            </div>

            <aside class="home-hero__report" aria-label="Core Web Vitals performance report">
                <div class="home-hero__report-head">
                    <span class="lbl">perf.report</span>
                    <span class="lbl">Core Web Vitals</span>
                </div>
                <div class="home-hero__report-body">
                    <svg class="home-hero__gauge" viewBox="0 0 120 120" role="img" aria-label="<?php echo esc_attr(sprintf('PageSpeed score %s out of 100', $pagespeed)); ?>">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="6" class="home-hero__gauge-track"></circle>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-dasharray="320.2 326.7" transform="rotate(-90 60 60)" class="home-hero__gauge-value"></circle>
                        <text x="60" y="64" text-anchor="middle" class="home-hero__gauge-score"><?php echo esc_html($pagespeed); ?></text>
                        <text x="60" y="82" text-anchor="middle" class="home-hero__gauge-max">/ 100</text>
                    </svg>
                    <div class="home-hero__report-data">
                        <div class="home-hero__report-score">
                            <span class="lbl">PageSpeed</span>
                            <span class="mono"><?php echo esc_html($pagespeed); ?></span>
                        </div>
                        <div class="home-hero__report-lcp">
                            <div class="home-hero__report-score">
                                <span class="lbl">LCP</span>
                                <span class="mono"><?php echo esc_html($lcp); ?></span>
                            </div>
                            <div class="home-hero__lcp-track" aria-hidden="true"><span></span></div>
                            <div class="home-hero__lcp-scale mono"><span>0s</span><span>good ≤ 2.5s</span></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
