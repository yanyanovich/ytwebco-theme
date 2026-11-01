<?php
/**
 * CTA band "Want results like this?" (case page, Work archive): status, display H2, two buttons.
 *
 * @var array $args {
 *     @type string $status         e.g. "Available for new projects"
 *     @type string $title          H2 (line breaks kept on desktop)
 *     @type array  $link_primary   ACF-style link array
 *     @type array  $link_secondary ACF-style link array
 *     @type string $class          extra classes
 * }
 */
$status = $args['status'] ?? '';
$title = $args['title'] ?? '';
$link_primary = (array)($args['link_primary'] ?? array());
$link_secondary = (array)($args['link_secondary'] ?? array());

if (empty($title)) {
    return;
}
?>
<section class="cta-band bp <?php echo esc_attr($args['class'] ?? ''); ?>" data-section>
    <div class="container cta-band__inner">
        <div class="cta-band__text">
            <?php if (!empty($status)) : ?>
                <span class="status-pill cta-band__status" data-aos="drift-up">
                    <span class="dot dot--pulse" aria-hidden="true"></span>
                    <?php echo esc_html($status); ?>
                </span>
            <?php endif; ?>
            <h2 class="cta-band__title" data-aos="rise-mask" data-aos-delay="100"><?php echo wp_kses($title, array('br' => array())); ?></h2>
        </div>
        <?php if (!empty($link_primary['url']) || !empty($link_secondary['url'])) : ?>
            <div class="cta-band__actions" data-aos="drift-up" data-aos-delay="200">
                <?php if (!empty($link_primary['url'])) : ?>
                    <a<?php echo get_acf_link_attrs($link_primary, 'btn btn--primary'); ?>>
                        <?php echo esc_html($link_primary['title'] ?: $link_primary['url']); ?>
                        <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                    </a>
                <?php endif; ?>
                <?php render_acf_link($link_secondary, 'btn btn--secondary'); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
