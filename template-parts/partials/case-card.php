<?php
/**
 * Case card (Selected work grid, Work archive, related cases).
 * The title link stretches over the whole card; brand colours drive the hover gradient.
 *
 * @var array $args {
 *     @type int    $post_id
 *     @type string $heading  h2 | h3
 *     @type string $sizes    sizes attribute for the mockup image
 * }
 */
$post_id = $args['post_id'] ?? 0;
if (!$post_id) {
    return;
}

$card = theme_get_case_card($post_id);
$heading = $args['heading'] ?? 'h3';
$heading = in_array($heading, array('h2', 'h3'), true) ? $heading : 'h3';
$sizes = $args['sizes'] ?? '(min-width: 1025px) 50vw, 100vw';

$style = array();
if ($card['brand']) {
    $style[] = '--brand: ' . $card['brand'];
}
if ($card['brand_2']) {
    $style[] = '--brand-2: ' . $card['brand_2'];
}
?>
<article class="case-card"<?php echo $style ? ' style="' . esc_attr(implode('; ', $style)) . '"' : ''; ?>>
    <div class="case-card__stage bp">
        <?php if ($card['image_id']) : ?>
            <?php echo wp_get_attachment_image($card['image_id'], 'full', false, array(
                'class' => 'case-card__image',
                'sizes' => $sizes,
                'loading' => 'lazy',
            )); ?>
        <?php else : ?>
            <span class="case-card__placeholder mono">[screenshot]</span>
        <?php endif; ?>
    </div>

    <div class="case-card__head">
        <div class="case-card__intro">
            <<?php echo $heading; ?> class="case-card__title">
                <a class="case-card__link" href="<?php echo esc_url($card['url']); ?>"><?php echo esc_html($card['title']); ?></a>
            </<?php echo $heading; ?>>
            <?php if ($card['excerpt']) : ?>
                <p class="case-card__excerpt"><?php echo esc_html($card['excerpt']); ?></p>
            <?php endif; ?>
        </div>
        <span class="icon-btn case-card__arrow" aria-hidden="true"><?php echo theme_icon('arrow-up-right'); ?></span>
    </div>

    <?php if ($card['metric'] || $card['stack']) : ?>
        <div class="case-card__foot">
            <?php if ($card['metric']) : ?>
                <p class="case-card__metric mono"><?php echo esc_html($card['metric']); ?></p>
            <?php endif; ?>
            <?php if ($card['stack']) : ?>
                <ul class="case-card__stack">
                    <?php foreach ($card['stack'] as $item) : ?>
                        <li class="chip"><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
