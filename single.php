<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package ytweb_theme
 */

get_header();
?>

    <main id="content" tabindex="-1" class="site-main">
        <?php the_content(); ?>
    </main>

<?php
get_footer();
