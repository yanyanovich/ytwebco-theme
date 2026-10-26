<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ytweb_theme
 */

get_header();

$queried_object = get_queried_object();
$archive_page_content = theme_get_archive_page_content($queried_object->name);

?>

<main id="content" tabindex="-1" class="site-main example-post-type-archive">
    <?php if (!empty($archive_page_content)) : ?>
        <div class="example-post-type-archive__content">
            <?php echo $archive_page_content; ?>
        </div>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
        <div class="example-post-type-archive__list">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_type());
            endwhile;
            ?>
        </div>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>

    <?php
    global $wp_query;
    if ($wp_query->max_num_pages > 1) :
        ?>
        <div class="example-post-type-archive__load-more">
            <button class="" type="button" data-loading_text="<?php esc_attr_e('Loading...', 'ytweb_theme'); ?>" data-default_text="<?php esc_attr_e('Show more', 'ytweb_theme'); ?>">
                <?php esc_html_e('Show more', 'ytweb_theme'); ?>
            </button>
        </div>
        <div class="list-pagination hide" data-pagination
             data-pagination-posts-per-page="<?php echo esc_attr($wp_query->posts_per_page); ?>"
             data-pagination-max-num-pages="<?php echo esc_attr($wp_query->max_num_pages); ?>"
             data-pagination-paged="<?php echo esc_attr(max(1, get_query_var('paged'))); ?>"></div>
    <?php endif; ?>
</main>

<?php
get_footer();
