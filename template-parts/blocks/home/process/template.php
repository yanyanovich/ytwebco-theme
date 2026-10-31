<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-process',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_process_head');
    $title = get_field('b_hp_process_title');
    $text = get_field('b_hp_process_text');
    $steps = get_field('b_hp_process_steps');
    $steps = !empty($steps) ? $steps : array();
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="hp-process__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 hp-process__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="hp-process__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($steps)) : ?>
                <ol class="hp-process__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                        <li class="hp-process__step" data-aos="drift-up" data-aos-delay="<?php echo esc_attr($index * 120); ?>">
                            <span class="hp-process__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            <div class="hp-process__body">
                                <?php if (!empty($step['title'])) : ?>
                                    <h3 class="hp-process__step-title"><?php echo esc_html($step['title']); ?></h3>
                                <?php endif; ?>
                                <?php if (!empty($step['text'])) : ?>
                                    <p class="hp-process__step-text"><?php echo esc_html($step['text']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($step['chip'])) : ?>
                                    <span class="chip"><?php echo esc_html($step['chip']); ?></span>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
