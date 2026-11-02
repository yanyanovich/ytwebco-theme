<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-services',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_services_head');
    $title = get_field('b_hp_services_title');
    $text = get_field('b_hp_services_text');
    $items = get_field('b_hp_services_items');
    $items = !empty($items) ? $items : array();
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="hp-services__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 hp-services__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="hp-services__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($items)) : ?>
                <div class="hp-services__list">
                    <?php foreach ($items as $index => $item) :
                        $tags = array_filter(array_map('trim', preg_split('/\R/u', (string)$item['tags'])));
                        ?>
                        <details class="acc<?php echo $index === 0 ? ' is-open' : ''; ?>" data-accordion data-aos="drift-up" data-aos-delay="<?php echo esc_attr($index * 80); ?>"<?php echo $index === 0 ? ' open' : ''; ?>>
                            <summary class="acc__summary">
                                <span class="acc__index mono"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                <h3 class="acc__title"><?php echo esc_html($item['title']); ?></h3>
                                <?php if (!empty($tags)) : ?>
                                    <span class="acc__tags acc__tags--head" aria-hidden="true">
                                        <?php foreach ($tags as $tag) : ?>
                                            <span class="chip"><?php echo esc_html($tag); ?></span>
                                        <?php endforeach; ?>
                                    </span>
                                <?php endif; ?>
                                <span class="icon-btn icon-btn--plus acc__icon" aria-hidden="true"><?php echo theme_icon('plus'); ?></span>
                            </summary>
                            <div class="acc__body">
                                <div class="acc__inner">
                                    <div class="acc__content">
                                        <?php if (!empty($item['text'])) : ?>
                                            <p class="acc__text"><?php echo esc_html($item['text']); ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($tags)) : ?>
                                            <ul class="acc__tags acc__tags--body">
                                                <?php foreach ($tags as $tag) : ?>
                                                    <li class="chip"><?php echo esc_html($tag); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
