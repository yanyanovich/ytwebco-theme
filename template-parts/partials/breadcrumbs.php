<?php
/**
 * Breadcrumbs (mono 12): links, the current page last as plain text.
 * By default the trail comes from theme_get_breadcrumbs(), the same one the BreadcrumbList schema uses.
 *
 * @var array $args {
 *     @type array $items list of array{label: string, url?: string}; the last one is the current page
 *     @type string $class extra classes
 * }
 */
$items = array_values(array_filter((array)($args['items'] ?? theme_get_breadcrumbs()), function ($item) {
    return !empty($item['label']);
}));

if (empty($items)) {
    return;
}

$last = count($items) - 1;
?>
<nav class="crumbs <?php echo esc_attr($args['class'] ?? ''); ?>" aria-label="<?php echo esc_attr(theme_t('Breadcrumb')); ?>">
    <?php foreach ($items as $index => $item) : ?>
        <?php if ($index === $last || empty($item['url'])) : ?>
            <span<?php echo $index === $last ? ' aria-current="page"' : ''; ?>><?php echo esc_html($item['label']); ?></span>
        <?php else : ?>
            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
        <?php endif; ?>
        <?php if ($index !== $last) : ?>
            <span aria-hidden="true">/</span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
