<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $case_id = !empty($post_id) ? (int)$post_id : get_the_ID();
    $block_classes = array(
        'case-hero',
        'bp',
        !empty($block['className']) ? $block['className'] : '',
    );

    $category = get_field('b_case_hero_category');
    $summary = get_field('b_case_hero_summary');
    $image_id = (int)get_field('b_case_hero_image');
    $meta = array(
        theme_t('Client') => get_field('b_case_hero_client'),
        theme_t('Role') => get_field('b_case_hero_role'),
        theme_t('Engagement') => get_field('b_case_hero_engagement'),
        theme_t('Year') => get_field('b_case_hero_year'),
    );

    // Domain and live URL belong to the case (they are on the card too)
    $domain = (string)get_field('case_domain', $case_id);
    $live_url = (string)get_field('case_live_url', $case_id);
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/breadcrumbs'); ?>

            <div class="case-hero__head grid-12">
                <div class="case-hero__intro">
                    <?php if (!empty($category)) : ?>
                        <span class="label case-hero__category" data-aos="reveal"><?php echo esc_html($category); ?></span>
                    <?php endif; ?>
                    <h1 class="h1 case-hero__title" data-aos="rise-mask" data-aos-delay="100"><?php echo esc_html(get_the_title($case_id)); ?></h1>
                    <?php if (!empty($summary)) : ?>
                        <p class="case-hero__summary" data-aos="drift-up" data-aos-delay="200"><?php echo esc_html($summary); ?></p>
                    <?php endif; ?>
                </div>

                <dl class="case-hero__meta" data-aos="drift-up" data-aos-delay="250">
                    <?php foreach ($meta as $label => $value) :
                        if (empty($value)) {
                            continue;
                        }
                        ?>
                        <div class="case-hero__meta-row">
                            <dt class="label"><?php echo esc_html($label); ?></dt>
                            <dd class="mono"><?php echo esc_html($value); ?></dd>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!empty($live_url)) : ?>
                        <div class="case-hero__meta-row">
                            <dt class="label"><?php echo esc_html(theme_t('Live site')); ?></dt>
                            <dd class="mono">
                                <a class="link-ul" href="<?php echo esc_url($live_url); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html($domain ?: wp_parse_url($live_url, PHP_URL_HOST)); ?>
                                    <span class="arrow arrow--diag"><?php echo theme_icon('arrow-up-right', 12); ?></span>
                                    <span class="screen-reader-text"><?php echo esc_html(theme_t('opens in a new tab')); ?></span>
                                </a>
                            </dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if (!empty($image_id)) : ?>
                <div class="case-frame case-hero__frame" data-aos="soft-scale" data-aos-delay="300">
                    <div class="case-frame__bar" aria-hidden="true">
                        <i></i><i></i><i></i>
                        <?php if (!empty($domain)) : ?>
                            <span class="mono"><?php echo esc_html($domain); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="case-hero__shot">
                        <?php echo wp_get_attachment_image($image_id, 'full', false, array(
                            'class' => 'case-frame__image',
                            'sizes' => '(min-width: 1440px) 1280px, calc(100vw - 40px)',
                            'loading' => 'eager',
                            'fetchpriority' => 'high',
                        )); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
