<?php
/**
 * Floating CTA (Interactions · 05): fixed bottom-right lime circle, the label rotates along the edge
 * (12s loop), arrow scales on hover. Homepage only. setup-floating-cta.js hides it while the
 * footer or the contact block is in view.
 *
 * @var array $args {
 *     @type string $url   link target (Contact page)
 *     @type string $label e.g. "Start a project"
 * }
 */
$url = $args['url'] ?? '';
$label = $args['label'] ?? '';

if (empty($url) || empty($label)) {
    return;
}

$ring_text = function_exists('mb_strtoupper') ? mb_strtoupper($label) : strtoupper($label);
?>
<a class="floating-cta" href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr($label); ?>" data-floating-cta>
    <svg class="floating-cta__ring" viewBox="0 0 120 120" aria-hidden="true" focusable="false">
        <defs>
            <path id="floating-cta-path" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"></path>
        </defs>
        <text>
            <textPath href="#floating-cta-path" textLength="286" lengthAdjust="spacing"><?php echo esc_html($ring_text . ' · ' . $ring_text . ' · '); ?></textPath>
        </text>
    </svg>
    <span class="floating-cta__arrow"><?php echo theme_icon('arrow-up-right', 30); ?></span>
</a>
