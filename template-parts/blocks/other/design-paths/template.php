<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : 'paths';
    $block_classes = array(
        'design-paths',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_paths_head');
    $intro = get_field('b_other_paths_intro');
    $rows = get_field('b_other_paths_rows') ?: array();
    $note = get_field('b_other_paths_show_note') ? get_field('b_other_paths_note') : '';
    $cards = array(
        'express' => array(
            'index' => 'A / 01',
            'title' => get_field('b_other_paths_express_title'),
            'badge' => '',
            'link' => (array)get_field('b_other_paths_express_link'),
            'button' => 'btn btn--secondary',
        ),
        'designer' => array(
            'index' => 'B / 02',
            'title' => get_field('b_other_paths_designer_title'),
            'badge' => get_field('b_other_paths_designer_badge'),
            'link' => (array)get_field('b_other_paths_designer_link'),
            'button' => 'btn btn--primary',
        ),
    );
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <?php if (!empty($intro)) : ?>
                <p class="design-paths__intro" data-aos="drift-up"><?php echo esc_html($intro); ?></p>
            <?php endif; ?>

            <?php // Desktop: one grid (labels | Express | Designer) with subgrid rows, so values line up across both cards ?>
            <div class="design-paths__grid" style="--rows: <?php echo esc_attr(count($rows)); ?>" data-aos="drift-up" data-aos-delay="100">
                <div class="design-paths__labels" aria-hidden="true">
                    <span class="label design-paths__compare"><?php echo esc_html(get_field('b_other_paths_compare')); ?></span>
                    <?php foreach ($rows as $row) : ?>
                        <span class="label design-paths__label"><?php echo esc_html($row['label']); ?></span>
                    <?php endforeach; ?>
                    <span></span>
                </div>

                <?php foreach ($cards as $path => $card) : ?>
                    <section class="design-paths__card design-paths__card--<?php echo esc_attr($path); ?>" aria-labelledby="<?php echo esc_attr($block_id . '-' . $path); ?>">
                        <div class="design-paths__card-head">
                            <span class="design-paths__index mono">
                                <?php echo esc_html($card['index']); ?>
                                <?php if (!empty($card['badge'])) : ?>
                                    <span class="design-paths__badge"><?php echo esc_html($card['badge']); ?></span>
                                <?php endif; ?>
                            </span>
                            <h3 class="design-paths__title" id="<?php echo esc_attr($block_id . '-' . $path); ?>"><?php echo esc_html($card['title']); ?></h3>
                        </div>
                        <dl class="design-paths__rows">
                            <?php foreach ($rows as $row) : ?>
                                <div class="design-paths__row<?php echo !empty($row['is_price']) ? ' is-price' : (!empty($row['summary']) ? ' is-mono' : ''); ?>">
                                    <dt class="label design-paths__dt"><?php echo esc_html($row['label']); ?></dt>
                                    <dd class="design-paths__value"><?php echo esc_html($row[$path]); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                        <div class="design-paths__action">
                            <?php if (!empty($card['link']['url'])) : ?>
                                <a<?php echo get_acf_link_attrs($card['link'], $card['button'] . ' btn--full'); ?> data-design-path="<?php echo esc_attr($path); ?>">
                                    <?php echo esc_html($card['link']['title'] ?: $card['link']['url']); ?>
                                    <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($note)) : ?>
                <p class="design-paths__note" data-aos="drift-up"><?php echo esc_html($note); ?></p>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
