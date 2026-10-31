<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-faq',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_faq_head');
    $title = get_field('b_hp_faq_title');
    $text = get_field('b_hp_faq_text');
    $link = get_field('b_hp_faq_link');
    $items = get_field('b_hp_faq_items');
    $items = !empty($items) ? $items : array();
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <div class="hp-faq__body grid-12">
                <?php if (!empty($title)) : ?>
                    <h2 class="h2 hp-faq__title" data-aos="rise-mask"><?php echo wp_kses($title, array('br' => array())); ?></h2>
                <?php endif; ?>

                <?php if (!empty($text) || !empty($link['url'])) : ?>
                    <div class="hp-faq__aside" data-aos="drift-up" data-aos-delay="150">
                        <?php if (!empty($text)) : ?>
                            <p class="hp-faq__text"><?php echo esc_html($text); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($link['url'])) : ?>
                            <a<?php echo get_acf_link_attrs($link, 'link-ul hp-faq__link'); ?>>
                                <?php echo esc_html($link['title'] ?: $link['url']); ?>
                                <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($items)) : ?>
                    <div class="hp-faq__list">
                        <?php foreach ($items as $index => $item) : ?>
                            <details class="acc<?php echo $index === 0 ? ' is-open' : ''; ?>" data-accordion data-aos="drift-up" data-aos-delay="<?php echo esc_attr($index * 80); ?>"<?php echo $index === 0 ? ' open' : ''; ?>>
                                <summary class="acc__summary">
                                    <h3 class="acc__title"><?php echo esc_html($item['question']); ?></h3>
                                    <span class="icon-btn icon-btn--plus acc__icon" aria-hidden="true"><?php echo theme_icon('plus'); ?></span>
                                </summary>
                                <div class="acc__body">
                                    <div class="acc__inner">
                                        <div class="acc__content">
                                            <p class="acc__text"><?php echo esc_html($item['answer']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
endif;
