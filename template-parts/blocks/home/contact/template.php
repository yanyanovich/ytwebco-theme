<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'hp-contact',
        'bp',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = get_field('b_hp_contact_head');
    $status = get_field('b_hp_contact_status');
    $title = get_field('b_hp_contact_title');
    $text = get_field('b_hp_contact_text');
    $rows = get_field('b_hp_contact_rows');
    $rows = !empty($rows) ? $rows : array();
    $note = get_field('b_hp_contact_note');
    $form_id = (int)get_field('b_hp_contact_form');
    $sending = get_field('b_hp_contact_sending');
    $success_badge = get_field('b_hp_contact_success_badge');
    $success_title = get_field('b_hp_contact_success_title');
    $success_steps = get_field('b_hp_contact_success_steps');
    $success_steps = !empty($success_steps) ? $success_steps : array();
    $success_link_primary = get_field('b_hp_contact_success_link_primary');
    $success_link_secondary = get_field('b_hp_contact_success_link_secondary');

    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, (array)$head); ?>

            <div class="hp-contact__body grid-12">
                <div class="hp-contact__intro">
                    <?php if (!empty($status)) : ?>
                        <span class="status-pill hp-contact__status" data-aos="drift-up">
                            <span class="dot dot--pulse" aria-hidden="true"></span>
                            <?php echo esc_html($status); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 hp-contact__title" data-aos="rise-mask" data-aos-delay="100"><?php echo wp_kses($title, array('br' => array())); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="hp-contact__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>

                <?php get_template_part('template-parts/partials/contact-form-panel', null, array(
                    'form_id' => $form_id,
                    'sending' => $sending,
                    'success' => array(
                        'badge' => $success_badge,
                        'title' => $success_title,
                        'steps' => $success_steps,
                        'link_primary' => $success_link_primary,
                        'link_secondary' => $success_link_secondary,
                    ),
                    'class' => 'hp-contact__form',
                )); ?>

                <?php if (!empty($rows) || !empty($note)) : ?>
                    <div class="hp-contact__aside" data-aos="drift-up" data-aos-delay="250">
                        <?php get_template_part('template-parts/partials/contact-rows', null, array('rows' => $rows)); ?>
                        <?php if (!empty($note)) : ?>
                            <p class="hp-contact__note mono"><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
endif;
