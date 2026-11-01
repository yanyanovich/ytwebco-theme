<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-about',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_about_head');
    $label = get_field('b_hp_about_label');
    $title = get_field('b_hp_about_title');
    $lead = get_field('b_hp_about_lead');
    $text = get_field('b_hp_about_text');
    $image_id = (int)get_field('b_hp_about_image');
    $image_caption = get_field('b_hp_about_image_caption');
    $stats = get_field('b_hp_about_stats');
    $stats = !empty($stats) ? $stats : array();
    $tools_label = get_field('b_hp_about_tools_label');
    $tools = get_field('b_hp_about_tools');
    $tools = !empty($tools) ? $tools : array();

    // Without a toolbox (UA local landing) the block is shorter: narrower photo, text column wider
    if (empty($tools)) {
        $block_classes[] = 'hp-about--compact';
    }
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <div class="hp-about__body grid-12">
                <div class="hp-about__photo bp" data-aos="soft-scale">
                    <?php if (!empty($image_id)) : ?>
                        <?php echo wp_get_attachment_image($image_id, 'full', false, array(
                            'class' => 'hp-about__image',
                            'sizes' => '(min-width: 1025px) 40vw, 100vw',
                            'loading' => 'lazy',
                        )); ?>
                    <?php endif; ?>
                    <?php if (!empty($image_caption)) : ?>
                        <span class="hp-about__caption mono"><?php echo esc_html($image_caption); ?></span>
                    <?php endif; ?>
                </div>

                <div class="hp-about__content">
                    <?php if (!empty($label) || !empty($title)) : ?>
                        <div class="hp-about__heading">
                            <?php if (!empty($label)) : ?>
                                <span class="label hp-about__label" data-aos="reveal"><?php echo esc_html($label); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($title)) : ?>
                                <h2 class="h2" data-aos="rise-mask" data-aos-delay="100"><?php echo esc_html($title); ?></h2>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($lead)) : ?>
                        <p class="lead hp-about__lead" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($lead); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="hp-about__text" data-aos="drift-up" data-aos-delay="200"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($stats)) : ?>
                        <dl class="hp-about__stats" data-aos="drift-up" data-aos-delay="250">
                            <?php foreach ($stats as $stat) :
                                $label_mobile = !empty($stat['label_mobile']) ? $stat['label_mobile'] : $stat['label'];
                                ?>
                                <div class="hp-about__stat">
                                    <dt class="label">
                                        <span class="hp-about__stat-label"><?php echo esc_html($stat['label']); ?></span>
                                        <span class="hp-about__stat-label hp-about__stat-label--mob"><?php echo esc_html($label_mobile); ?></span>
                                    </dt>
                                    <dd class="mono hp-about__stat-value"><?php echo esc_html($stat['value']); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>

                    <?php if (!empty($tools)) : ?>
                        <div class="hp-about__tools">
                            <?php if (!empty($tools_label)) : ?>
                                <span class="label" data-aos="reveal"><?php echo esc_html($tools_label); ?></span>
                            <?php endif; ?>
                            <ul class="hp-about__tool-list">
                                <?php foreach ($tools as $index => $tool) : ?>
                                    <li class="hp-about__tool" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(min($index * 40, 480)); ?>">
                                        <?php if (!empty($tool['code'])) : ?>
                                            <span class="hp-about__tool-code mono" aria-hidden="true"><?php echo esc_html($tool['code']); ?></span>
                                        <?php endif; ?>
                                        <?php echo esc_html($tool['name']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
