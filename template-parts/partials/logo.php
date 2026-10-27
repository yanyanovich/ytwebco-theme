<?php
/**
 * YT Web Co. logo, inlined from media/logo (design/assets/logo, text outlined).
 * Header / mobile menu: full EN or UA lockup; the compact lockup (no role line)
 * fades in when the header compacts. Footer: monogram + signature line.
 *
 * @var array $args {
 *     @type string $variant  header | footer
 * }
 */
$variant = $args['variant'] ?? 'header';
$home_url = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
$lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
?>
<?php if ($variant === 'footer') : ?>
    <a class="logo logo--footer" href="<?php echo esc_url($home_url); ?>">
        <?php echo theme_svg('media/logo/logo-monogram-dark.svg', array('class' => 'logo__monogram')); ?>
        <span class="logo__signature"><?php echo esc_html(theme_t('YT Web Co. · founded by Yan Tokaruk')); ?></span>
    </a>
<?php else : ?>
    <a class="logo" href="<?php echo esc_url($home_url); ?>" aria-label="<?php echo esc_attr(theme_t('YT Web Co. — home')); ?>">
        <?php echo theme_svg('media/logo/logo-full-' . ($lang === 'uk' ? 'ua' : 'en') . '-dark.svg', array('class' => 'logo__full')); ?>
        <?php echo theme_svg('media/logo/logo-compact-dark.svg', array('class' => 'logo__compact')); ?>
    </a>
<?php endif; ?>
