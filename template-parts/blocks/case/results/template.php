<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $case_id = !empty($post_id) ? (int)$post_id : get_the_ID();

    $meta = get_field('b_case_results_meta');
    $type = get_field('b_case_results_type') ?: 'numbers';
    $numbers = (get_field('b_case_results_numbers') ?: array());
    $compare = (array)get_field('b_case_results_compare');
    $proof = array_values(array_filter((get_field('b_case_results_proof') ?: array()), function ($item) {
        return !empty($item['image']);
    }));
    $line = get_field('b_case_results_line');
    $text = get_field('b_case_results_text');
    $points = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string)get_field('b_case_results_points')))));
    $stack = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string)get_field('b_case_results_stack')))));
    $stack = !empty($stack) ? $stack : theme_get_case_card($case_id)['stack'];

    $has_results = $type === 'outcome' ? !empty($line) : !empty($numbers);
    $has_compare = $type === 'numbers' && count($numbers) === 1 && !empty($compare['before']) && !empty($compare['after']);

    if (!$has_results && empty($stack)) {
        return;
    }

    // "After" bar width = after / before (numbers only, "20,000+" → 20000)
    $compare_width = 0;
    if ($has_compare) {
        $to_number = function ($value) {
            return (float)preg_replace('/[^\d.]/', '', str_replace(',', '', $value));
        };
        $before = $to_number($compare['before']);
        $compare_width = $before > 0 ? max(2, min(100, $to_number($compare['after']) / $before * 100)) : 0;
    }

    $block_classes = array(
        'case-results',
        !empty($block['className']) ? $block['className'] : '',
    );
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container">
            <?php if ($has_results) : ?>
                <?php get_template_part('template-parts/partials/section-head', null, array('index' => theme_case_section_index(), 'name' => theme_t('Results'), 'meta' => $meta)); ?>

                <?php if ($type === 'outcome') : ?>
                    <div class="case-results__body case-results__body--outcome grid-12">
                        <div class="case-results__main" data-aos="drift-up">
                            <span class="case-results__line mono"><?php echo esc_html($line); ?></span>
                            <?php if (!empty($text)) : ?>
                                <span class="case-results__label"><?php echo esc_html($text); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php get_template_part('template-parts/partials/case-points', null, array('points' => $points)); ?>
                    </div>
                <?php else : ?>
                    <div class="case-results__body<?php echo count($numbers) > 1 ? ' case-results__body--pair' : ''; ?> grid-12" data-section="0.5" data-count-group>
                        <?php foreach ($numbers as $i => $number) : ?>
                            <div class="case-results__main" data-aos="drift-up" data-aos-delay="<?php echo esc_attr($i * 120); ?>">
                                <span class="case-results__number mono"><span data-count data-delay="<?php echo esc_attr($i * 120); ?>"><?php echo esc_html($number['value']); ?></span></span>
                                <?php if (!empty($number['label'])) : ?>
                                    <span class="case-results__label"><?php echo esc_html($number['label']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($number['note'])) : ?>
                                    <span class="case-results__note mono"><?php echo esc_html($number['note']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($has_compare) : ?>
                            <div class="case-compare" data-aos="drift-up" data-aos-delay="150">
                                <?php foreach (array('before' => 100, 'after' => $compare_width) as $row => $width) : ?>
                                    <div class="case-compare__row case-compare__row--<?php echo esc_attr($row); ?>">
                                        <div class="case-compare__head">
                                            <span class="label"><?php echo esc_html($row === 'before' ? theme_t('Before') : theme_t('After')); ?></span>
                                            <span class="case-compare__value mono"><?php echo esc_html($compare[$row]); ?><?php if (!empty($compare['unit'])) : ?><span> <?php echo esc_html($compare['unit']); ?></span><?php endif; ?></span>
                                        </div>
                                        <span class="case-compare__bar" aria-hidden="true"><span style="width: <?php echo esc_attr(round($width, 1)); ?>%" data-bar="<?php echo esc_attr(round($width, 1)); ?>"></span></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (count($numbers) === 1) : ?>
                            <?php get_template_part('template-parts/partials/case-points', null, array('points' => $points)); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($proof)) : ?>
                <div class="case-results__proof<?php echo count($proof) > 1 ? ' case-results__proof--pair' : ''; ?>">
                    <?php foreach ($proof as $i => $item) : ?>
                        <figure class="case-results__proof-item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr($i * 120); ?>">
                            <?php echo wp_get_attachment_image((int)$item['image'], 'large', false, array(
                                'class' => 'case-results__proof-image',
                                'sizes' => '(min-width: 1025px) 580px, calc(100vw - 40px)',
                                'loading' => 'lazy',
                            )); ?>
                            <?php if (!empty($item['caption'])) : ?>
                                <figcaption class="case-results__proof-caption mono"><?php echo esc_html($item['caption']); ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($stack)) : ?>
                <div class="case-results__stack">
                    <span class="label"><?php echo esc_html(theme_t('Stack')); ?></span>
                    <ul class="case-results__chips">
                        <?php foreach ($stack as $item) : ?>
                            <li class="chip"><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
