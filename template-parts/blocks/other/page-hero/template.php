<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_classes = array(
        'page-hero',
        'bp',
        get_field('b_other_page_hero_layout') === 'split' ? 'page-hero--split' : '',
        !empty($block['className']) ? $block['className'] : '',
    );

    $label = get_field('b_other_page_hero_label');
    $status = get_field('b_other_page_hero_status');
    $title = get_field('b_other_page_hero_title');
    $text = get_field('b_other_page_hero_text');
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/breadcrumbs'); ?>

            <?php if (!empty($label)) : ?>
                <p class="label page-hero__label mono" data-aos="drift-up"><?php echo esc_html($label); ?></p>
            <?php endif; ?>

            <?php if (!empty($status)) : ?>
                <span class="status-pill page-hero__status" data-aos="drift-up">
                    <span class="dot dot--pulse" aria-hidden="true"></span>
                    <?php echo esc_html($status); ?>
                </span>
            <?php endif; ?>

            <div class="page-hero__body">
                <h1 class="page-hero__title" data-aos="rise-mask" data-aos-delay="100"><?php echo wp_kses($title ?: get_the_title(), array('br' => array())); ?></h1>

                <?php if (!empty($text)) : ?>
                    <p class="page-hero__text" data-aos="drift-up" data-aos-delay="200"><?php echo esc_html($text); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
endif;
