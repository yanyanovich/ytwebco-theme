<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : 'booking';
    $block_classes = array(
        'booking',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_booking_head');
    $title = get_field('b_other_booking_title');
    $text = get_field('b_other_booking_text');
    $button = get_field('b_other_booking_button');

    // "user/event" or a full Cal.com URL; falls back to the booking link from Theme settings
    $cal = trim((string)get_field('b_other_booking_cal'));
    if ($cal === '') {
        $booking_url = theme_get_contacts()['booking_url'];
        $cal = $booking_url !== '#' ? $booking_url : '';
    }
    $cal_link = $cal !== '' ? trim((string)preg_replace('#^https?://(app\.)?cal\.com/#', '', $cal), '/') : '';
    $is_cal = $cal_link !== '' && strpos($cal_link, '://') === false;
    $booking_url = $cal === '' ? '' : ($is_cal ? 'https://cal.com/' . $cal_link : $cal);
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <div class="booking__body grid-12">
                <div class="booking__intro">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 booking__title" data-aos="rise-mask"><?php echo wp_kses($title, array('br' => array())); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <p class="booking__text" data-aos="drift-up" data-aos-delay="100"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($button) && $booking_url !== '') : ?>
                        <a class="btn btn--primary" href="<?php echo esc_url($booking_url); ?>" target="_blank" rel="noopener" data-aos="drift-up" data-aos-delay="150">
                            <?php echo esc_html($button); ?>
                            <span class="arrow arrow--diag"><?php echo theme_icon('arrow-up-right'); ?></span>
                            <span class="screen-reader-text"><?php echo esc_html(theme_t('opens in a new tab')); ?></span>
                        </a>
                    <?php elseif (!empty($button)) : ?>
                        <span class="btn btn--primary is-disabled" aria-disabled="true"><?php echo esc_html($button); ?></span>
                        <span class="booking__missing mono">[Cal.com link]</span>
                    <?php endif; ?>
                </div>

                <div class="booking__embed" data-aos="soft-scale" data-aos-delay="150"<?php echo $is_cal ? ' data-cal-embed="' . esc_attr($cal_link) . '"' : ''; ?>>
                    <?php // Placeholder until the Cal.com calendar loads (or while there is no link) ?>
                    <div class="booking__placeholder" aria-hidden="true">
                        <span class="booking__placeholder-tag mono">[Cal.com embed]</span>
                        <div class="booking__calendar">
                            <span class="booking__month"></span>
                            <span class="booking__days">
                                <?php for ($day = 1; $day <= 28; $day++) : ?>
                                    <i<?php echo in_array($day % 7, array(0, 6), true) || $day < 4 ? ' class="is-off"' : ''; ?>></i>
                                <?php endfor; ?>
                            </span>
                        </div>
                        <span class="booking__slots"><i></i><i></i><i></i><i></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
