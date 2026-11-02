<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'service-formats',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_formats_head');
    $items = (get_field('b_other_formats_items') ?: array());
    if (empty($items)) {
        return;
    }
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <ul class="service-formats__list">
                <?php foreach ($items as $index => $item) : ?>
                    <li class="service-formats__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr($index * 100); ?>">
                        <h2 class="service-formats__title"><?php echo esc_html($item['title']); ?></h2>
                        <?php if (!empty($item['text'])) : ?>
                            <p class="service-formats__text"><?php echo esc_html($item['text']); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php
endif;
