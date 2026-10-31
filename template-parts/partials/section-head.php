<?php
/**
 * Section head (Design system): 1px top rule, lime index + name on the left,
 * meta text or a link on the right.
 *
 * @var array $args {
 *     @type string $index  e.g. "01"
 *     @type string $name   e.g. "Services"
 *     @type string $meta   e.g. "4 areas"
 *     @type array  $link   ACF link, e.g. "All 06 projects" → /work/ (replaces meta)
 *     @type string $class  extra classes
 * }
 */
$index = $args['index'] ?? '';
$name = $args['name'] ?? '';
$meta = $args['meta'] ?? '';
$link = $args['link'] ?? array();

if (empty($index) && empty($name) && empty($meta) && empty($link['url'])) {
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
    <?php if (!empty($link['url'])) : ?>
        <a<?php echo get_acf_link_attrs($link, 'link-ul section-head__link'); ?>>
            <?php echo esc_html($link['title'] ?: $link['url']); ?>
            <span class="arrow"><?php echo theme_icon('arrow-right', 12); ?></span>
        </a>
    <?php elseif (!empty($meta)) : ?>
        <span class="section-head__meta"><?php echo esc_html($meta); ?></span>
    <?php endif; ?>
</div>
