<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'contact-page',
        !empty($block['className']) ? $block['className'] : '',
    );

    $direct_head = (array)get_field('b_other_contact_direct_head');
    $rows = (array)get_field('b_other_contact_rows');
    $cards = (array)get_field('b_other_contact_cards');
    $form_head = (array)get_field('b_other_contact_form_head');
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container grid-12">
            <div class="contact-page__direct">
                <?php get_template_part('template-parts/partials/section-head', null, $direct_head); ?>
                <div class="contact-page__rows" data-aos="drift-up">
                    <?php get_template_part('template-parts/partials/contact-rows', null, array('rows' => $rows)); ?>
                </div>

                <?php if (!empty($cards)) : ?>
                    <ul class="contact-page__cards" data-aos="drift-up" data-aos-delay="100">
                        <?php foreach ($cards as $card) : ?>
                            <li class="contact-page__card">
                                <?php if (($card['type'] ?? '') === 'reply') : ?>
                                    <span class="dot dot--pulse contact-page__card-dot" aria-hidden="true"></span>
                                <?php else : ?>
                                    <span class="contact-page__card-icon" aria-hidden="true"><?php echo theme_icon('location', 16); ?></span>
                                <?php endif; ?>
                                <span class="contact-page__card-title"><?php echo esc_html($card['title']); ?></span>
                                <?php if (!empty($card['note'])) : ?>
                                    <span class="contact-page__card-note mono"><?php echo esc_html($card['note']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="contact-page__write">
                <?php get_template_part('template-parts/partials/section-head', null, $form_head); ?>
                <?php get_template_part('template-parts/partials/contact-form-panel', null, array(
                    'form_id' => (int)get_field('b_other_contact_form'),
                    'sending' => get_field('b_other_contact_sending'),
                    'success' => array(
                        'badge' => get_field('b_other_contact_success_badge'),
                        'title' => get_field('b_other_contact_success_title'),
                        'steps' => (array)get_field('b_other_contact_success_steps'),
                        'link_primary' => (array)get_field('b_other_contact_success_link_primary'),
                        'link_secondary' => (array)get_field('b_other_contact_success_link_secondary'),
                    ),
                    'class' => 'contact-page__form',
                )); ?>
            </div>
        </div>
    </section>
<?php
endif;
