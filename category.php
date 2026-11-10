<?php
/**
 * Category archive: the same layout as the blog archive, filtered by category.
 *
 * @package ytweb_theme
 */

get_header();
?>

    <main id="content" tabindex="-1" class="site-main blog-page">
        <?php get_template_part('template-parts/blog/archive'); ?>
    </main>

<?php
get_footer();
