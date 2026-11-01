<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'service-price',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_price_head');
    $value = get_field('b_other_price_value');
    $label = get_field('b_other_price_label');
    $rows = (array)get_field('b_other_price_rows');
    $link = (array)get_field('b_other_price_link');
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <div class="service-price__body grid-12">
                <?php if (!empty($value)) : ?>
                    <div class="service-price__main" data-aos="drift-up">
                        <span class="service-price__value mono"><?php echo esc_html($value); ?></span>
                        <?php if (!empty($label)) : ?>
                            <span class="service-price__label"><?php echo esc_html($label); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="service-price__side" data-aos="drift-up" data-aos-delay="150">
                    <?php if (!empty($rows)) : ?>
                        <dl class="service-price__rows">
                            <?php foreach ($rows as $row) : ?>
                                <div class="service-price__row">
                                    <dt class="label"><?php echo esc_html($row['label']); ?></dt>
                                    <dd class="mono"><?php echo esc_html($row['value']); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>
                    <?php if (!empty($link['url'])) : ?>
                        <a<?php echo get_acf_link_attrs($link, 'btn btn--primary service-price__btn'); ?>>
                            <?php echo esc_html($link['title'] ?: $link['url']); ?>
                            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
