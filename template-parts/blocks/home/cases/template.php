<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-cases',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_cases_head');
    $title = get_field('b_hp_cases_title');
    $text = get_field('b_hp_cases_text');
    $link = get_field('b_hp_cases_link');
    $case_ids = get_field('b_hp_cases_items');

    // Nothing picked → every published case in menu order (current language via Polylang)
    if (empty($case_ids)) {
        $case_ids = get_posts(array(
            'post_type' => 'case',
            'posts_per_page' => -1,
            'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'),
            'fields' => 'ids',
        ));
    }
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="hp-cases__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 hp-cases__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text) || !empty($link['url'])) : ?>
                        <div class="hp-cases__aside" data-aos="drift-up" data-aos-delay="150">
                            <?php if (!empty($text)) : ?>
                                <p class="hp-cases__text"><?php echo esc_html($text); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($link['url'])) : ?>
                                <a<?php echo get_acf_link_attrs($link, 'link-ul link-ul--line hp-cases__link'); ?>>
                                    <?php echo esc_html($link['title'] ?: $link['url']); ?>
                                    <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($case_ids)) : ?>
                <?php // Three selected cases (UA local landing) sit in one row of three compact cards ?>
                <ul class="hp-cases__grid<?php echo count($case_ids) === 3 ? ' hp-cases__grid--three' : ''; ?>">
                    <?php foreach ($case_ids as $index => $case_id) : ?>
                        <li class="hp-cases__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(($index % 2) * 120); ?>">
                            <?php get_template_part('template-parts/partials/case-card', null, array('post_id' => $case_id, 'label' => (bool)get_field('b_hp_cases_labels'))); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
