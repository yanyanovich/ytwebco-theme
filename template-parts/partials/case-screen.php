<?php
/**
 * One case screen: a screenshot in a browser frame (desktop) or a phone frame (mobile), with a caption.
 * Until the screenshot exists, a labelled placeholder with its future file name is shown, as in the design.
 *
 * @var array $args {
 *     @type array  $screen array{image: int, caption: string, placeholder: string}
 *     @type string $device "desktop" | "mobile"
 *     @type int    $delay  reveal delay, ms
 * }
 */
$screen = (array)($args['screen'] ?? array());
$device = ($args['device'] ?? 'desktop') === 'mobile' ? 'mobile' : 'desktop';
$image_id = (int)($screen['image'] ?? 0);
$caption = $screen['caption'] ?? '';
$placeholder = $screen['placeholder'] ?? '';

if (empty($image_id) && empty($placeholder)) {
    return;
}

$size_label = $device === 'mobile' ? '780 × 1688 px' : '2560 × 1600 px';

// The screen keeps the screenshot's own proportions, so nothing is cut at the sides when a file
// is not exactly 780 × 1688 / 2560 × 1600; placeholders use the standard size from CSS
$shot_style = '';
if (!empty($image_id)) {
    $meta = wp_get_attachment_metadata($image_id);
    if (!empty($meta['width']) && !empty($meta['height'])) {
        $shot_style = ' style="aspect-ratio: ' . (int)$meta['width'] . ' / ' . (int)$meta['height'] . '"';
    }
}
?>
<figure class="case-screen case-screen--<?php echo esc_attr($device); ?>"<?php echo $device === 'desktop' ? ' data-aos="drift-up" data-aos-delay="' . esc_attr((int)($args['delay'] ?? 0)) . '"' : ''; ?>>
    <div class="<?php echo $device === 'mobile' ? 'case-phone' : 'case-frame'; ?><?php echo empty($image_id) ? ' bp' : ''; ?>">
        <?php if ($device === 'desktop') : ?>
            <div class="case-frame__bar" aria-hidden="true"><i></i><i></i><i></i></div>
        <?php endif; ?>
        <div class="case-screen__shot"<?php echo $shot_style; ?>>
            <?php if (!empty($image_id)) : ?>
                <?php echo wp_get_attachment_image($image_id, 'full', false, array(
                    'class' => 'case-frame__image',
                    'sizes' => $device === 'mobile' ? '220px' : '(min-width: 1440px) 628px, (min-width: 1025px) 45vw, calc(100vw - 40px)',
                    'loading' => 'lazy',
                )); ?>
            <?php else : ?>
                <span class="case-screen__placeholder mono">
                    <span class="case-screen__placeholder-tag">[PLACEHOLDER · screenshot coming]</span>
                    <span><?php echo esc_html($placeholder); ?></span>
                    <span class="case-screen__placeholder-size"><?php echo esc_html($size_label); ?></span>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($caption)) : ?>
        <figcaption class="case-screen__caption mono"><?php echo esc_html($caption); ?></figcaption>
    <?php endif; ?>
</figure>
