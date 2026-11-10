<?php
/**
 * Numbered pagination (Newer / numbers / Older); "Page 2 of 3" on mobile. Only when there is more than one page.
 *
 * @var array $args {
 *     @type int $current
 *     @type int $total
 * }
 */
$current = (int)($args['current'] ?? 1);
$total = (int)($args['total'] ?? 1);

if ($total < 2) {
    return;
}

$link = function ($page) {
    return $page > 1 ? get_pagenum_link($page) : get_pagenum_link(1);
};
$pages = array_unique(array_filter(array(1, $current - 1, $current, $current + 1, $total), function ($n) use ($total) {
    return $n >= 1 && $n <= $total;
}));
sort($pages);
?>
<nav class="blog-pagination" aria-label="<?php echo esc_attr(theme_t('Pages')); ?>">
    <?php if ($current > 1) : ?>
        <a class="blog-pagination__step" rel="prev" href="<?php echo esc_url($link($current - 1)); ?>">← <?php echo esc_html(theme_t('Newer posts')); ?></a>
    <?php else : ?>
        <span class="blog-pagination__step blog-pagination__step--off" aria-hidden="true">← <?php echo esc_html(theme_t('Newer posts')); ?></span>
    <?php endif; ?>

    <ol class="blog-pagination__numbers mono">
        <?php $prev = 0; foreach ($pages as $n) :
            if ($prev && $n - $prev > 1) : ?><li class="blog-pagination__gap" aria-hidden="true">…</li><?php endif; ?>
            <li>
                <?php if ($n === $current) : ?>
                    <span class="blog-pagination__num is-current" aria-current="page"><?php echo esc_html(sprintf('%02d', $n)); ?></span>
                <?php else : ?>
                    <a class="blog-pagination__num" href="<?php echo esc_url($link($n)); ?>"><?php echo esc_html(sprintf('%02d', $n)); ?></a>
                <?php endif; ?>
            </li>
        <?php $prev = $n; endforeach; ?>
    </ol>
    <p class="blog-pagination__of mono"><?php echo esc_html(sprintf(theme_t('Page %1$s of %2$s'), $current, $total)); ?></p>

    <?php if ($current < $total) : ?>
        <a class="blog-pagination__step" rel="next" href="<?php echo esc_url($link($current + 1)); ?>"><?php echo esc_html(theme_t('Older posts')); ?> →</a>
    <?php else : ?>
        <span class="blog-pagination__step blog-pagination__step--off" aria-hidden="true"><?php echo esc_html(theme_t('Older posts')); ?> →</span>
    <?php endif; ?>
</nav>
