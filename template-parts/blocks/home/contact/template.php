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

    // Row type → icon, plus the URL / note from Theme settings → Contacts when the row leaves them empty
    $contacts = theme_get_contacts();
    $types = array(
        'telegram' => array('icon' => 'telegram', 'url' => $contacts['telegram_url'], 'note' => $contacts['telegram'], 'external' => true),
        'booking' => array('icon' => 'calendar', 'url' => $contacts['booking_url'], 'note' => '', 'external' => true),
        'email' => array('icon' => 'email', 'url' => $contacts['email_url'], 'note' => $contacts['email'], 'external' => false),
        'phone' => array('icon' => 'phone', 'url' => $contacts['phone_url'], 'note' => $contacts['phone'], 'external' => false),
        'location' => array('icon' => 'location', 'url' => '', 'note' => '', 'external' => false),
    );
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

                <?php if (!empty($form_id)) : ?>
                    <div class="contact-form hp-contact__form" data-contact-form data-sending="<?php echo esc_attr($sending); ?>" data-aos="soft-scale" data-aos-delay="200">
                        <div class="contact-form__panel">
                            <?php render_cf7_form($form_id); ?>
                        </div>

                        <?php if (!empty($success_title)) : ?>
                            <div class="contact-form__ok" role="status" aria-live="polite">
                                <?php if (!empty($success_badge)) : ?>
                                    <span class="contact-form__badge mono">
                                        <span class="dot" aria-hidden="true"></span>
                                        <?php echo esc_html($success_badge); ?>
                                    </span>
                                <?php endif; ?>
                                <h3 class="contact-form__ok-title" tabindex="-1"><?php echo esc_html($success_title); ?></h3>
                                <?php if (!empty($success_steps)) : ?>
                                    <ol class="contact-form__steps">
                                        <?php foreach ($success_steps as $index => $step) : ?>
                                            <li class="contact-form__step">
                                                <span class="contact-form__step-index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                                <span class="contact-form__step-text"><?php echo esc_html($step['text']); ?></span>
                                                <?php if (!empty($step['chip'])) : ?>
                                                    <span class="chip"><?php echo esc_html($step['chip']); ?></span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                                <?php if (!empty($success_link_primary['url']) || !empty($success_link_secondary['url'])) : ?>
                                    <div class="contact-form__actions">
                                        <?php if (!empty($success_link_primary['url'])) : ?>
                                            <a<?php echo get_acf_link_attrs($success_link_primary, 'btn btn--primary'); ?>>
                                                <?php echo esc_html($success_link_primary['title'] ?: $success_link_primary['url']); ?>
                                                <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                        <?php render_acf_link($success_link_secondary, 'btn btn--secondary'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($rows) || !empty($note)) : ?>
                    <div class="hp-contact__aside" data-aos="drift-up" data-aos-delay="250">
                        <?php if (!empty($rows)) : ?>
                            <ul class="hp-contact__rows">
                                <?php foreach ($rows as $row) :
                                    $type = $types[$row['type']] ?? $types['location'];
                                    $row_note = !empty($row['note']) ? $row['note'] : $type['note'];
                                    $row_link = !empty($row['link']['url']) ? $row['link'] : array();
                                    if (empty($row_link) && !empty($type['url']) && $type['url'] !== '#') {
                                        $row_link = array('url' => $type['url'], 'title' => '', 'target' => $type['external'] ? '_blank' : '');
                                    }
                                    $tag = !empty($row_link) ? 'a' : 'div';
                                    ?>
                                    <li>
                                        <<?php echo $tag; ?> class="hp-contact__row"<?php echo !empty($row_link) ? get_acf_link_attrs($row_link) : ''; ?>>
                                            <span class="hp-contact__row-icon" aria-hidden="true"><?php echo theme_icon($type['icon'], 20); ?></span>
                                            <span class="hp-contact__row-body">
                                                <span class="hp-contact__row-title"><?php echo esc_html($row['title']); ?></span>
                                                <?php if (!empty($row_note)) : ?>
                                                    <span class="hp-contact__row-note mono"><?php echo esc_html($row_note); ?></span>
                                                <?php endif; ?>
                                            </span>
                                            <?php if (!empty($row_link)) : ?>
                                                <span class="hp-contact__row-arrow arrow" aria-hidden="true"><?php echo theme_icon('arrow-right'); ?></span>
                                            <?php endif; ?>
                                        </<?php echo $tag; ?>>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
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
