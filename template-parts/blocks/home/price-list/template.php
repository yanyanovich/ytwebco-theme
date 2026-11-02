<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-prices',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_prices_head');
    $title = get_field('b_hp_prices_title');
    $text = get_field('b_hp_prices_text');
    $link = (array)get_field('b_hp_prices_link');
    $items = (get_field('b_hp_prices_items') ?: array());

    // "від 12 000 грн" → the first number group in lime
    $format_price = function ($price) {
        return preg_replace('/(\d[\d\s.,]*\d|\d)/u', '<span class="accent">$1</span>', esc_html($price), 1);
    };
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="hp-prices__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 hp-prices__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="hp-prices__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($items)) : ?>
                <?php $has_terms = (bool)array_filter(array_column($items, 'term')); ?>
                <ul class="hp-prices__list<?php echo $has_terms ? ' hp-prices__list--terms' : ''; ?>">
                    <?php foreach ($items as $index => $item) :
                        // A row links to its own page (Services hub) or to the block link (UA homepage)
                        $row_link = !empty($item['link']['url']) ? (array)$item['link'] : $link;
                        $tag = !empty($row_link['url']) ? 'a' : 'div';
                        $tags = array_filter(array_map('trim', preg_split('/\R/u', (string)($item['tags'] ?? ''))));
                        $arrow = !empty($item['link']['url']);
                        ?>
                        <li data-aos="drift-up" data-aos-delay="<?php echo esc_attr(min($index * 60, 360)); ?>">
                            <<?php echo $tag; ?> class="hp-prices__row<?php echo $arrow ? ' hp-prices__row--arrow' : ''; ?>"<?php echo $tag === 'a' ? get_acf_link_attrs($row_link) : ''; ?>>
                                <span class="hp-prices__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                <h3 class="hp-prices__name"><?php echo esc_html($item['title']); ?></h3>
                                <?php if (!empty($item['text']) || !empty($tags)) : ?>
                                    <span class="hp-prices__desc">
                                        <?php echo esc_html($item['text']); ?>
                                        <?php if (!empty($tags)) : ?>
                                            <span class="hp-prices__tags">
                                                <?php foreach ($tags as $chip) : ?>
                                                    <span class="chip"><?php echo esc_html($chip); ?></span>
                                                <?php endforeach; ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($has_terms) : ?>
                                    <span class="hp-prices__term mono"><?php echo esc_html($item['term'] ?? ''); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['price'])) : ?>
                                    <span class="hp-prices__price mono"><?php echo $format_price($item['price']); ?></span>
                                <?php endif; ?>
                                <?php if ($arrow) : ?>
                                    <span class="hp-prices__arrow" aria-hidden="true"><span class="arrow arrow--diag"><?php echo theme_icon('arrow-up-right', 14); ?></span></span>
                                <?php endif; ?>
                            </<?php echo $tag; ?>>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
