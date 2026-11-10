<?php
$head = trim((string)get_field('b_blog_stat_head'));
$items = array_slice(array_filter((array)(get_field('b_blog_stat_items') ?: array()), function ($item) {
    return !empty($item['number']);
}), 0, 3);

if (empty($items)) {
    return;
}
?>
<figure class="post-stat post-stat--<?php echo count($items); ?><?php echo !empty($block['className']) ? ' ' . esc_attr($block['className']) : ''; ?>">
    <?php if ($head !== '') : ?><figcaption class="post-stat__head label"><?php echo esc_html($head); ?></figcaption><?php endif; ?>
    <div class="post-stat__cells">
        <?php foreach ($items as $item) : ?>
            <div class="post-stat__cell">
                <p class="post-stat__value">
                    <span class="post-stat__number"><?php echo esc_html($item['number']); ?></span>
                    <?php if (!empty($item['unit'])) : ?><span class="post-stat__unit"><?php echo esc_html($item['unit']); ?></span><?php endif; ?>
                </p>
                <?php if (!empty($item['label'])) : ?><p class="post-stat__label"><?php echo esc_html($item['label']); ?></p><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</figure>
