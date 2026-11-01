<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $challenge_lead = get_field('b_case_work_challenge_lead');
    $challenge_text = get_field('b_case_work_challenge_text');
    $items = (array)get_field('b_case_work_items');
    $meta = get_field('b_case_work_meta');

    if (empty($challenge_lead) && empty($items)) {
        return;
    }

    $block_classes = array(
        'case-work',
        empty($challenge_lead) ? 'case-work--wide' : '',
        !empty($block['className']) ? $block['className'] : '',
    );
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container grid-12">
            <?php if (!empty($challenge_lead)) : ?>
                <div class="case-work__challenge">
                    <?php get_template_part('template-parts/partials/section-head', null, array('index' => theme_case_section_index(), 'name' => theme_t('The challenge'))); ?>
                    <p class="case-work__lead" data-aos="drift-up"><?php echo esc_html($challenge_lead); ?></p>
                    <?php if (!empty($challenge_text)) : ?>
                        <p class="case-work__text" data-aos="drift-up" data-aos-delay="100"><?php echo esc_html($challenge_text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($items)) : ?>
                <div class="case-work__did">
                    <?php get_template_part('template-parts/partials/section-head', null, array(
                        'index' => theme_case_section_index(),
                        'name' => theme_t('What I did'),
                        'meta' => empty($challenge_lead) ? $meta : '',
                    )); ?>
                    <ol class="case-did">
                        <?php foreach ($items as $i => $item) : ?>
                            <li class="case-did__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(min($i * 60, 360)); ?>">
                                <span class="case-did__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
                                <span class="case-did__text">
                                    <?php if (!empty($item['lead'])) : ?>
                                        <b><?php echo esc_html($item['lead']); ?></b>
                                    <?php endif; ?>
                                    <?php echo esc_html($item['text']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
