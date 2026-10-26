<?php
/**
 * The header for our theme
 *
 * Desktop: logo, nav with Services dropdown, EN/UA switch, CTA; compacts after 80px scroll.
 * Mobile: logo, lime CTA icon, burger → full-screen menu (no EN/UA switch in the bar).
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ytweb_theme
 */

$menu = theme_get_menu_tree('header_menu');
$contact_url = theme_get_page_url('contact');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0E0F11">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

    <?php theme_print_additional_code('end_of_head'); ?>
</head>

<body id="top" <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php theme_print_additional_code('start_of_body'); ?>

<a class="skip-link" href="#content"><?php echo esc_html(theme_t('Skip to content')); ?></a>

<header id="header" class="header" data-header>
    <div class="header__inner">
        <?php get_template_part('template-parts/partials/logo'); ?>

        <?php get_template_part('template-parts/header/nav-desktop', null, array('menu' => $menu)); ?>

        <div class="header__actions">
            <?php get_template_part('template-parts/partials/lang-switch'); ?>
            <a class="btn btn--primary btn--sm header__cta" href="<?php echo esc_url($contact_url); ?>">
                <span class="dot dot--sm header__cta-dot" aria-hidden="true"></span>
                <?php echo esc_html(theme_t('Start a project')); ?>
                <span class="arrow arrow--diag"><?php echo theme_icon('arrow-diag', 14); ?></span>
            </a>
        </div>

        <div class="header__mob-actions">
            <a class="icon-btn icon-btn--cta" href="<?php echo esc_url($contact_url); ?>" aria-label="<?php echo esc_attr(theme_t('Start a project')); ?>">
                <?php echo theme_icon('arrow-diag'); ?>
            </a>
            <button type="button" class="icon-btn header__burger" aria-label="<?php echo esc_attr(theme_t('Open menu')); ?>"
                    aria-expanded="false" aria-controls="mobile-menu" data-mob-menu-open>
                <?php echo theme_icon('burger', 18); ?>
            </button>
        </div>
    </div>
</header>

<?php get_template_part('template-parts/header/mobile-menu', null, array('menu' => $menu, 'contact_url' => $contact_url)); ?>

<div id="page" class="site">
