<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'seo-text',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_seo_head');
    $head['class'] = 'section-head--muted';
    $title = get_field('b_other_seo_title');
    $content = get_field('b_other_seo_content');

    if (empty($content)) {
        return;
    }
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             <?php if (!empty($title)) : ?>aria-labelledby="<?php echo esc_attr($block_id); ?>-title"<?php endif; ?>
             data-seo-text
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <?php if (!empty($title)) : ?>
                <h2 id="<?php echo esc_attr($block_id); ?>-title" class="seo-text__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php // Full text is always in the HTML; JS (html.js) only collapses it visually ?>
            <div class="seo-text__clip" id="<?php echo esc_attr($block_id); ?>-text">
                <div class="seo-text__content">
                    <?php echo wp_kses_post($content); ?>
                </div>
            </div>

            <div class="seo-text__ctl">
                <button type="button" class="btn btn--secondary btn--sm seo-text__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr($block_id); ?>-text">
                    <span class="seo-text__more"><?php echo esc_html(theme_t('Read more')); ?> <span class="arrow arrow--down"><?php echo theme_icon('arrow-down', 14); ?></span></span>
                    <span class="seo-text__less"><?php echo esc_html(theme_t('Show less')); ?> <span class="arrow arrow--up"><?php echo theme_icon('arrow-up', 14); ?></span></span>
                </button>
            </div>
        </div>
    </section>
<?php
endif;
