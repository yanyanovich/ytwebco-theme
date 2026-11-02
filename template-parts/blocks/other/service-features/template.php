<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'service-features',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_features_head');
    $title = get_field('b_other_features_title');
    $text = get_field('b_other_features_text');
    $items = (get_field('b_other_features_items') ?: array());
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="service-features__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 service-features__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="service-features__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($items)) : ?>
                <ul class="service-features__list">
                    <?php foreach ($items as $index => $item) :
                        $tags = array_filter(array_map('trim', preg_split('/\R/u', (string)($item['tags'] ?? ''))));
                        ?>
                        <li class="service-features__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(($index % 2) * 100); ?>">
                            <span class="service-features__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            <div class="service-features__body">
                                <h3 class="service-features__name"><?php echo esc_html($item['title']); ?></h3>
                                <?php if (!empty($item['text'])) : ?>
                                    <p class="service-features__desc"><?php echo esc_html($item['text']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($tags)) : ?>
                                    <ul class="service-features__tags">
                                        <?php foreach ($tags as $tag) : ?>
                                            <li class="chip"><?php echo esc_html($tag); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
