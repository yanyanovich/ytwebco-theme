<?php
/**
 * Blog card. Featured (latest post, page 1) or regular. The title link stretches over the whole card.
 *
 * @var array $args {
 *     @type int    $post_id
 *     @type string $variant  card | featured
 * }
 */
$post_id = (int)($args['post_id'] ?? 0);
if (!$post_id) {
    return;
}

$card = theme_get_post_card($post_id);
$featured = ($args['variant'] ?? 'card') === 'featured';
$heading = $featured ? 'h2' : 'h2';
?>
<article class="post-card<?php echo $featured ? ' post-card--featured' : ''; ?>" data-category="<?php echo esc_attr($card['category_slug']); ?>">
    <?php get_template_part('template-parts/blog/cover', null, array(
        'word' => $card['cover_text'],
        'caption' => $card['cover_caption'],
        'variant' => $featured ? 'featured' : 'card',
        'number' => $card['featured_number'],
        'label' => $card['category'],
    )); ?>

    <div class="post-card__body">
        <div class="post-card__text">
            <div class="post-card__tags">
                <?php if ($featured) : ?>
                    <span class="post-card__latest mono"><span class="dot" aria-hidden="true"></span><?php echo esc_html(theme_t('Latest')); ?></span>
                <?php endif; ?>
                <?php if ($card['category']) : ?><span class="post-card__cat mono"><?php echo esc_html($card['category']); ?></span><?php endif; ?>
            </div>
            <<?php echo $heading; ?> class="post-card__title">
                <a class="post-card__link" href="<?php echo esc_url($card['url']); ?>"><?php echo esc_html($card['title']); ?></a>
            </<?php echo $heading; ?>>
            <?php if ($card['lead']) : ?><p class="post-card__lead"><?php echo esc_html($card['lead']); ?></p><?php endif; ?>
        </div>
        <div class="post-card__foot">
            <p class="post-card__meta mono">
                <time datetime="<?php echo esc_attr($card['iso']); ?>"><?php echo esc_html($card['date']); ?></time>
                <span aria-hidden="true">·</span>
                <?php echo esc_html(theme_reading_time_label($card['minutes'])); ?>
            </p>
            <?php if ($featured) : ?><span class="post-card__read"><?php echo esc_html(theme_t('Read the post')); ?></span><?php endif; ?>
            <span class="icon-btn post-card__arrow" aria-hidden="true"><?php echo theme_icon('arrow-up-right'); ?></span>
        </div>
    </div>
</article>
