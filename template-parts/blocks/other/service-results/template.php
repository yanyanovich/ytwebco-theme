<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'service-results',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_results_head');
    $value = get_field('b_other_results_value');
    $label = get_field('b_other_results_label');
    $link = (array)get_field('b_other_results_link');
    $bars = (array)get_field('b_other_results_bars');
    $cards = (array)get_field('b_other_results_cards');
    $note = get_field('b_other_results_note');
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <div class="service-results__body grid-12" data-section="0.5" data-count-group>
                <div class="service-results__main" data-aos="drift-up">
                    <?php if (!empty($value)) : ?>
                        <span class="service-results__value"><?php echo esc_html($value); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($label)) : ?>
                        <span class="service-results__label"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($link['url'])) : ?>
                        <a<?php echo get_acf_link_attrs($link, 'link-ul link-ul--line service-results__link'); ?>>
                            <?php echo esc_html($link['title'] ?: $link['url']); ?>
                            <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="service-results__side" data-aos="drift-up" data-aos-delay="150">
                    <?php if (!empty($bars)) : ?>
                        <dl class="service-results__bars">
                            <?php foreach ($bars as $bar) :
                                $width = max(0, min(100, (int)$bar['width']));
                                ?>
                                <div class="service-results__bar<?php echo !empty($bar['accent']) ? ' is-accent' : ''; ?>">
                                    <dt class="label"><?php echo esc_html($bar['label']); ?></dt>
                                    <dd class="service-results__bar-value mono">
                                        <?php echo esc_html($bar['value']); ?>
                                        <?php if (!empty($bar['unit'])) : ?>
                                            <span class="service-results__unit"><?php echo esc_html($bar['unit']); ?></span>
                                        <?php endif; ?>
                                    </dd>
                                    <dd class="service-results__track" aria-hidden="true">
                                        <span class="service-results__fill" style="width: <?php echo esc_attr($width); ?>%" data-bar="<?php echo esc_attr($width); ?>"></span>
                                    </dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>

                    <?php if (!empty($cards)) : ?>
                        <dl class="service-results__cards">
                            <?php foreach ($cards as $card) : ?>
                                <div class="service-results__card<?php echo !empty($card['accent']) ? ' is-accent' : ''; ?>">
                                    <dt class="label"><?php echo esc_html($card['label']); ?></dt>
                                    <dd class="service-results__card-value mono"><?php echo esc_html($card['value']); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>

                    <?php if (!empty($note)) : ?>
                        <p class="service-results__note mono"><?php echo esc_html($note); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
