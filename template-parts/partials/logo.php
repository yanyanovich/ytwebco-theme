<?php
/**
 * Logo lockup: "yt/" monogram + divider + "web co." + role line (Logo & favicon sheet).
 * "yt/" stays Latin on UA pages.
 *
 * @var array $args {
 *     @type string $variant  header | footer
 * }
 */
$variant = $args['variant'] ?? 'header';
$home_url = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
?>
<?php if ($variant === 'footer') : ?>
    <a class="logo logo--footer" href="<?php echo esc_url($home_url); ?>">
        <span class="logo__mark" aria-hidden="true">yt<span class="logo__slash">/</span></span>
        <span class="logo__signature"><?php echo esc_html(theme_t('YT Web Co. · founded by Yan Tokaruk')); ?></span>
    </a>
<?php else : ?>
    <a class="logo" href="<?php echo esc_url($home_url); ?>" aria-label="<?php echo esc_attr(theme_t('YT Web Co. — home')); ?>">
        <span class="logo__mark" aria-hidden="true">yt<span class="logo__slash">/</span></span>
        <span class="logo__divider" aria-hidden="true"></span>
        <span class="logo__text" aria-hidden="true">
            <span class="logo__name">web co.</span>
            <span class="logo__role"><?php echo esc_html(theme_t('Web studio · Lutsk')); ?></span>
        </span>
    </a>
<?php endif; ?>
