<?php
/**
 * Contact form (CF7) with the in-place success panel (Design system · 15 Form states).
 * Homepage contact block and Contact page.
 *
 * @var array $args {
 *     @type int    $form_id CF7 form
 *     @type string $sending button label while the request is in flight
 *     @type array  $success array{badge: string, title: string, steps: array, link_primary: array, link_secondary: array}
 *     @type string $class   extra classes
 * }
 */
$form_id = (int)($args['form_id'] ?? 0);
if (!$form_id) {
    return;
}

$success = (array)($args['success'] ?? array());
$steps = array_values((array)($success['steps'] ?? array()));
$link_primary = (array)($success['link_primary'] ?? array());
$link_secondary = (array)($success['link_secondary'] ?? array());
?>
<div class="contact-form <?php echo esc_attr($args['class'] ?? ''); ?>" data-contact-form data-sending="<?php echo esc_attr($args['sending'] ?? ''); ?>" data-aos="soft-scale" data-aos-delay="200">
    <div class="contact-form__panel">
        <?php render_cf7_form($form_id); ?>
    </div>

    <?php if (!empty($success['title'])) : ?>
        <div class="contact-form__ok" role="status" aria-live="polite">
            <?php if (!empty($success['badge'])) : ?>
                <span class="contact-form__badge mono">
                    <span class="dot" aria-hidden="true"></span>
                    <?php echo esc_html($success['badge']); ?>
                </span>
            <?php endif; ?>
            <h3 class="contact-form__ok-title" tabindex="-1"><?php echo esc_html($success['title']); ?></h3>
            <?php if (!empty($steps)) : ?>
                <ol class="contact-form__steps">
                    <?php foreach ($steps as $index => $step) : ?>
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
            <?php if (!empty($link_primary['url']) || !empty($link_secondary['url'])) : ?>
                <div class="contact-form__actions">
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
    <?php endif; ?>
</div>
