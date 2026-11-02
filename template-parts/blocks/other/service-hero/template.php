<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_classes = array(
        'service-hero',
        'bp',
        !empty($block['className']) ? $block['className'] : '',
    );

    $label = get_field('b_other_service_hero_label');
    $title = get_field('b_other_service_hero_title');
    $text = get_field('b_other_service_hero_text');
    $link_primary = (array)get_field('b_other_service_hero_link_primary');
    $link_secondary = (array)get_field('b_other_service_hero_link_secondary');
    $card_label = get_field('b_other_service_hero_card_label');
    $rows = (get_field('b_other_service_hero_rows') ?: array());
    $tags = array_filter(array_map('trim', preg_split('/\R/u', (string)get_field('b_other_service_hero_tags'))));
    // Website design: no rows of its own, the card shows Express / Designer from the Design paths block
    $paths = empty($rows) ? theme_get_design_paths_summary(get_the_ID()) : array();
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/breadcrumbs'); ?>

            <div class="service-hero__body grid-12">
                <div class="service-hero__intro">
                    <?php if (!empty($label)) : ?>
                        <span class="label service-hero__label" data-aos="reveal"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                    <h1 class="service-hero__title" data-aos="rise-mask" data-aos-delay="100"><?php echo wp_kses($title ?: get_the_title(), array('br' => array())); ?></h1>
                    <?php if (!empty($text)) : ?>
                        <p class="service-hero__text" data-aos="drift-up" data-aos-delay="200"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($link_primary['url']) || !empty($link_secondary['url'])) : ?>
                        <div class="service-hero__actions" data-aos="drift-up" data-aos-delay="250">
                            <?php if (!empty($link_primary['url'])) : ?>
                                <a<?php echo get_acf_link_attrs($link_primary, 'btn btn--primary'); ?>>
                                    <?php echo esc_html($link_primary['title'] ?: $link_primary['url']); ?>
                                    <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php render_acf_link($link_secondary, 'btn btn--secondary'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($paths)) : ?>
                    <a href="#paths" class="service-hero__card service-hero__card--paths" data-aos="soft-scale" data-aos-delay="300">
                        <span class="service-hero__card-head">
                            <span class="label"><?php echo esc_html($card_label); ?></span>
                            <span class="label">A · B</span>
                        </span>
                        <span class="service-hero__card-body">
                            <?php foreach ($paths as $path) : ?>
                                <span class="service-hero__path">
                                    <span class="service-hero__path-title"><?php echo esc_html($path['title']); ?></span>
                                    <span class="service-hero__path-price mono accent"><?php echo esc_html($path['price']); ?></span>
                                    <span class="service-hero__path-meta mono"><?php echo esc_html(trim(($path['timeline_label'] ?? '') . ' · ' . $path['timeline'], ' ·')); ?></span>
                                    <span class="service-hero__path-meta mono"><?php echo esc_html($path['revisions']); ?></span>
                                </span>
                            <?php endforeach; ?>
                        </span>
                    </a>
                <?php elseif (!empty($rows)) : ?>
                    <div class="service-hero__card" data-aos="soft-scale" data-aos-delay="300">
                        <div class="service-hero__card-head">
                            <span class="label">quote.summary</span>
                            <?php if (!empty($card_label)) : ?>
                                <span class="label"><?php echo esc_html($card_label); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="service-hero__card-body">
                            <dl>
                                <?php foreach ($rows as $index => $row) : ?>
                                    <div class="service-hero__row">
                                        <dt class="label"><?php echo esc_html($row['label']); ?></dt>
                                        <dd class="mono<?php echo $index === 0 ? ' accent' : ''; ?>"><?php echo esc_html($row['value']); ?></dd>
                                    </div>
                                <?php endforeach; ?>
                            </dl>
                            <?php if (!empty($tags)) : ?>
                                <ul class="service-hero__tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <li class="chip"><?php echo esc_html($tag); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
endif;
