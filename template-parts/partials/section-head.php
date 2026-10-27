<?php
/**
 * Section head (Design system): 1px top rule, lime index + name on the left, meta on the right.
 *
 * @var array $args {
 *     @type string $index  e.g. "01"
 *     @type string $name   e.g. "Services"
 *     @type string $meta   e.g. "4 areas"
 *     @type string $class  extra classes
 * }
 */
$index = $args['index'] ?? '';
$name = $args['name'] ?? '';
$meta = $args['meta'] ?? '';

if (empty($index) && empty($name) && empty($meta)) {
    return;
}
?>
<div class="section-head label <?php echo esc_attr($args['class'] ?? ''); ?>" data-aos="reveal">
    <span class="section-head__name">
        <?php if (!empty($index)) : ?>
            <span class="section-head__index"><?php echo esc_html($index); ?></span>
        <?php endif; ?>
        <?php if (!empty($index) && !empty($name)) : ?>
            <span aria-hidden="true">/</span>
        <?php endif; ?>
        <?php echo esc_html($name); ?>
    </span>
    <?php if (!empty($meta)) : ?>
        <span class="section-head__meta"><?php echo esc_html($meta); ?></span>
    <?php endif; ?>
</div>
