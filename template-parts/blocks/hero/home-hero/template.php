<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'home-hero',
        'bp',
        !empty($block['className']) ? $block['className'] : '',
    );

    $title = get_field('b_hero_home_title');
    $text = get_field('b_hero_home_text');
    $link_primary = get_field('b_hero_home_link_primary');
    $link_secondary = get_field('b_hero_home_link_secondary');
    $metrics = get_field('b_hero_home_metrics');
    $location = get_field('b_hero_home_location');
    $region = get_field('b_hero_home_region');

    $perf = theme_get_perf();
    $is_placeholder = function ($value) {
        return strpos($value, '[') === 0;
    };

    // Gauge ring (r = 52) and LCP bar (scale 0 → 2.5s) follow the real numbers; placeholders keep the mockup state.
    $circumference = 2 * M_PI * 52;
    $score = is_numeric($perf['pagespeed']) ? max(0, min(100, (float)$perf['pagespeed'])) : 98;
    $lcp_seconds = (float)preg_replace('/[^\d.]/', '', $perf['lcp']);
    $lcp_width = $lcp_seconds > 0 ? min(100, $lcp_seconds / 2.5 * 100) : 48;
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <p class="home-hero__badge mono">
                <span class="dot dot--pulse" aria-hidden="true"></span>
                <span class="home-hero__live"><?php echo esc_html(theme_t('Live')); ?></span>
                <span>PageSpeed <span class="accent"><?php echo esc_html($perf['pagespeed']); ?></span> · LCP <span class="accent"><?php echo esc_html($perf['lcp']); ?></span></span>
            </p>

            <?php if (!empty($title)) : ?>
                <h1 class="display home-hero__title"><?php echo theme_accent_period($title); ?></h1>
            <?php endif; ?>

            <div class="home-hero__body grid-12">
                <div class="home-hero__content">
                    <?php if (!empty($text)) : ?>
                        <p class="lead home-hero__text"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($link_primary['url']) || !empty($link_secondary['url'])) : ?>
                        <div class="home-hero__actions">
                            <?php if (!empty($link_primary['url'])) : ?>
                                <a<?php echo get_acf_link_attrs($link_primary, 'btn btn--primary home-hero__btn'); ?>>
                                    <?php echo esc_html($link_primary['title'] ?: $link_primary['url']); ?>
                                    <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php render_acf_link($link_secondary, 'btn btn--secondary home-hero__btn'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="home-hero__report" role="group" aria-label="<?php echo esc_attr(theme_t('Performance report of this site')); ?>">
                    <div class="home-hero__report-head">
                        <span class="label">perf.report</span>
                        <span class="label">Core Web Vitals</span>
                    </div>
                    <div class="home-hero__report-body">
                        <svg class="home-hero__gauge" viewBox="0 0 120 120" role="img" aria-label="<?php echo esc_attr(sprintf(theme_t('PageSpeed score %s out of 100'), $perf['pagespeed'])); ?>">
                            <circle class="home-hero__gauge-track" cx="60" cy="60" r="52"></circle>
                            <circle class="home-hero__gauge-value" cx="60" cy="60" r="52" transform="rotate(-90 60 60)"
                                    stroke-dasharray="<?php echo esc_attr(round($circumference * $score / 100, 1) . ' ' . round($circumference, 1)); ?>"></circle>
                            <text class="home-hero__gauge-score<?php echo $is_placeholder($perf['pagespeed']) ? ' is-placeholder' : ''; ?>" x="60" y="<?php echo $is_placeholder($perf['pagespeed']) ? 64 : 68; ?>" text-anchor="middle"><?php echo esc_html($perf['pagespeed']); ?></text>
                            <text class="home-hero__gauge-max" x="60" y="<?php echo $is_placeholder($perf['pagespeed']) ? 82 : 88; ?>" text-anchor="middle">/ 100</text>
                        </svg>
                        <dl class="home-hero__report-data">
                            <div class="home-hero__report-row">
                                <dt class="label">PageSpeed</dt>
                                <dd class="mono home-hero__report-value"><?php echo esc_html($perf['pagespeed']); ?></dd>
                            </div>
                            <div class="home-hero__report-row home-hero__report-row--lcp">
                                <dt class="label">LCP</dt>
                                <dd class="mono home-hero__report-value"><?php echo esc_html($perf['lcp']); ?></dd>
                                <dd class="home-hero__lcp" aria-hidden="true">
                                    <span class="home-hero__lcp-bar"><span style="width: <?php echo esc_attr(round($lcp_width, 1)); ?>%"></span></span>
                                    <span class="home-hero__lcp-scale mono"><span>0s</span><span><?php echo esc_html(theme_t('good ≤ 2.5s')); ?></span></span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <?php if (!empty($metrics)) : ?>
                <ul class="home-hero__metrics">
                    <?php foreach ($metrics as $metric) :
                        $source = $metric['source'] ?? 'custom';
                        $value = $source === 'custom' ? $metric['value'] : $perf[$source];
                        $unit = $source === 'custom' ? $metric['unit'] : '';
                        $label_mobile = !empty($metric['label_mobile']) ? $metric['label_mobile'] : $metric['label'];
                        $value_classes = array(
                            'metric',
                            $is_placeholder((string)$value) ? 'metric--sm' : 'metric--md',
                            $source === 'pagespeed' ? 'metric--accent' : '',
                        );
                        ?>
                        <li class="home-hero__metric">
                            <span class="<?php echo esc_attr(trim(implode(' ', array_filter($value_classes)))); ?>">
                                <?php echo esc_html($value); ?><?php if (!empty($unit)) : ?><span class="home-hero__metric-unit"> <?php echo esc_html($unit); ?></span><?php endif; ?>
                            </span>
                            <span class="label">
                                <span class="home-hero__metric-label"><?php echo esc_html($metric['label']); ?></span>
                                <span class="home-hero__metric-label home-hero__metric-label--mob"><?php echo esc_html($label_mobile); ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($location) || !empty($region)) : ?>
                <div class="home-hero__location mono">
                    <span><?php echo esc_html($location); ?></span>
                    <?php if (!empty($region)) : ?>
                        <span class="home-hero__region"><?php echo esc_html($region); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
