<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $desktop = (array)get_field('b_case_screens_desktop');
    $mobile = (array)get_field('b_case_screens_mobile');
    $translated = get_field('b_case_screens_translated');

    if (empty($desktop) && empty($mobile)) {
        return;
    }

    $block_classes = array(
        'case-screens',
        !empty($block['className']) ? $block['className'] : '',
    );
    ?>
    <section<?php echo !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : ''; ?>
            class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
            data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, array(
                'index' => theme_case_section_index(),
                'name' => theme_t('Screens'),
                'meta' => theme_t('Desktop + mobile'),
                'meta_mobile' => !empty($mobile) ? theme_t('Swipe →') : '',
            )); ?>

            <?php if (!empty($desktop)) : ?>
                <div class="case-screens__desktop">
                    <?php foreach ($desktop as $i => $screen) {
                        get_template_part('template-parts/partials/case-screen', null, array('screen' => $screen, 'device' => 'desktop', 'delay' => ($i % 2) * 120));
                    } ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mobile)) : ?>
                <div class="case-screens__mobile bp" data-aos="drift-up">
                    <div class="case-screens__track">
                        <?php foreach ($mobile as $screen) {
                            get_template_part('template-parts/partials/case-screen', null, array('screen' => $screen, 'device' => 'mobile'));
                        } ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($translated)) : ?>
                <p class="case-screens__note mono"><?php echo esc_html(theme_t('Screenshots are shown in English translation; the live site is in Ukrainian.')); ?></p>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
