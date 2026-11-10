<?php
/**
 * Blog archive (/blog/, /en/blog/, category archives).
 * Page 1: featured card (latest post) + up to 9 cards; the in-grid CTA card sits after the 3rd card
 * (page 1, "All" filter only). Filter chips are category links; setup-blog-filter.js swaps the results without reload.
 */
global $wp_query;

$paged = max(1, (int)get_query_var('paged'));
$category = is_category() ? get_queried_object() : null;
$ids = wp_list_pluck($wp_query->posts, 'ID');
$total_posts = theme_blog_post_count();
$max_pages = theme_blog_max_pages($wp_query->found_posts);

$categories = get_terms(array('taxonomy' => 'category', 'hide_empty' => true, 'orderby' => 'term_id'));
$categories = is_wp_error($categories) ? array() : array_values(array_filter($categories, function ($term) {
    return $term->count > 0;
}));
$blog_url = theme_get_blog_url();

$count_templates = array(
    'one' => theme_t('%s post'),
    'few' => theme_plural_form(2) === 'few' ? theme_t('%s posts (2–4)') : theme_t('%s posts'),
    'many' => theme_t('%s posts'),
);
$count_label = sprintf($count_templates[theme_plural_form($wp_query->found_posts)], sprintf('%02d', $wp_query->found_posts));

$featured_id = ($paged === 1 && !empty($ids)) ? array_shift($ids) : 0;
$show_cta = $paged === 1 && !$category;
$launch = $show_cta && empty($ids); // only the featured post exists: CTA card + "next articles" note
$work_url = get_post_type_archive_link('case');
?>
<section class="blog-archive" data-blog data-section>
    <div class="blog-archive__top bp">
        <div class="container">
            <?php get_template_part('template-parts/partials/breadcrumbs', null, array('items' => array(
                array('label' => theme_t('Home'), 'url' => function_exists('pll_home_url') ? pll_home_url() : home_url('/')),
                array('label' => theme_t('Blog'), 'url' => $blog_url),
            ))); ?>

            <div class="blog-archive__head">
                <div class="blog-archive__titles">
                    <p class="label blog-archive__eyebrow"><span aria-hidden="true">//</span> <?php echo esc_html(theme_t('Blog')); ?></p>
                    <h1 class="blog-archive__title" data-aos="rise-mask"><?php echo esc_html(theme_t('Notes on fast WordPress sites')); ?></h1>
                </div>
                <p class="blog-archive__lead" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html(theme_t('Practical notes on speed, WooCommerce and integrations — with numbers from real projects.')); ?></p>
            </div>

            <?php if (count($categories) > 1) : ?>
                <div class="blog-archive__filter">
                    <div class="blog-archive__chips" role="group" aria-label="<?php echo esc_attr(theme_t('Filter')); ?>" data-blog-filter>
                        <span class="label blog-archive__filter-label"><?php echo esc_html(theme_t('Filter')); ?></span>
                        <a class="pick<?php echo !$category ? ' is-active' : ''; ?>" href="<?php echo esc_url($blog_url); ?>"<?php echo !$category ? ' aria-current="true"' : ''; ?>>
                            <?php echo esc_html(theme_t('All')); ?> <span class="blog-archive__chip-count"><?php echo esc_html(sprintf('%02d', $total_posts)); ?></span>
                        </a>
                        <?php foreach ($categories as $term) : $active = $category && (int)$category->term_id === (int)$term->term_id; ?>
                            <a class="pick<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>"<?php echo $active ? ' aria-current="true"' : ''; ?>>
                                <?php echo esc_html($term->name); ?> <span class="blog-archive__chip-count"><?php echo esc_html(sprintf('%02d', $term->count)); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <span class="label blog-archive__count" aria-live="polite" data-blog-count><?php echo esc_html($count_label); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container blog-archive__results" data-blog-results>
        <?php if (empty($ids) && !$featured_id) : ?>
            <p class="blog-archive__empty"><?php echo esc_html(theme_t('No posts yet.')); ?></p>
        <?php endif; ?>

        <?php if ($featured_id) : ?>
            <?php get_template_part('template-parts/blog/post-card', null, array('post_id' => $featured_id, 'variant' => 'featured')); ?>
        <?php endif; ?>

        <?php if (!empty($ids) || $launch) : ?>
            <div class="blog-grid<?php echo $launch ? ' blog-grid--launch' : ''; ?>">
                <?php foreach ($ids as $index => $post_id) : ?>
                    <?php get_template_part('template-parts/blog/post-card', null, array('post_id' => $post_id)); ?>
                    <?php if ($show_cta && $index === 2) { get_template_part('template-parts/blog/cta-card'); } ?>
                <?php endforeach; ?>
                <?php if ($show_cta && count($ids) < 3) { get_template_part('template-parts/blog/cta-card'); } ?>

                <?php if ($launch) : ?>
                    <div class="blog-next">
                        <h2 class="blog-next__title"><?php echo esc_html(theme_t('Next articles')); ?></h2>
                        <p class="blog-next__text"><?php echo esc_html(theme_t('A new article every few weeks. Meanwhile, see what I have already built.')); ?></p>
                        <?php if ($work_url) : ?>
                            <a class="link-ul link-ul--line" href="<?php echo esc_url($work_url); ?>"><?php echo esc_html(theme_t('See the work')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php get_template_part('template-parts/blog/pagination', null, array('current' => $paged, 'total' => $max_pages)); ?>
        <span hidden data-blog-count-source><?php echo esc_html($count_label); ?></span>
    </div>
</section>
