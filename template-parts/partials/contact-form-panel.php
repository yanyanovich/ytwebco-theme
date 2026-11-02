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
 *     @type string $note    line under the form, followed by $note_link (Contact page: link to the brief)
 * }
 */
$form_id = (int)($args['form_id'] ?? 0);
if (!$form_id) {
    return;
}

$success = (array)($args['success'] ?? array());
?>
<div class="contact-form <?php echo esc_attr($args['class'] ?? ''); ?>" data-contact-form data-sending="<?php echo esc_attr($args['sending'] ?? ''); ?>" data-aos="soft-scale" data-aos-delay="200">
    <div class="contact-form__panel">
        <?php render_cf7_form($form_id); ?>
        <?php get_template_part('template-parts/partials/form-note', null, array('text' => $args['note'] ?? '', 'link' => $args['note_link'] ?? array())); ?>
    </div>

    <?php get_template_part('template-parts/partials/form-success', null, $success); ?>
</div>
