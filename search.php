<?php
/**
 * Search results (/?s=…): head with the search field, a list of articles, cases and pages, pagination,
 * and an empty state with links to the main sections. Never indexed (inc/search_author.php).
 *
 * @package ytweb_theme
 */

get_header();

global $wp_query;
$query = trim((string)get_search_query(false));
$found = (int)$wp_query->found_posts;
$paged = max(1, (int)get_query_var('paged'));
$home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

$count_templates = array(
    'one' => theme_t('%s result'),
    'few' => theme_plural_form(2) === 'few' ? theme_t('%s results (2–4)') : theme_t('%s results'),
    'many' => theme_t('%s results'),
);
$count_label = sprintf($count_templates[theme_plural_form($found)], sprintf('%02d', $found));
?>

    <main id="content" tabindex="-1" class="site-main search-page">
        <section class="search-page__top bp" data-section>
            <div class="container">
                <?php get_template_part('template-parts/partials/breadcrumbs', null, array('items' => array(
                    array('label' => theme_t('Home'), 'url' => $home),
                    array('label' => theme_t('Search')),
                ))); ?>

                <p class="label search-page__eyebrow"><span aria-hidden="true">//</span> <?php echo esc_html(theme_t('Search')); ?></p>
                <h1 class="search-page__title">
                    <?php echo $query !== '' ? esc_html(sprintf(theme_t('Results for “%s”'), $query)) : esc_html(theme_t('Search the site')); ?>
                </h1>

                <?php get_template_part('template-parts/search/form', null, array('query' => $query)); ?>

                <?php if ($query !== '') : ?>
                    <p class="label search-page__count" aria-live="polite"><?php echo esc_html($count_label); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <div class="container search-page__body">
            <?php if ($query === '') : ?>
                <p class="search-page__hint"><?php echo esc_html(theme_t('Type a word or a phrase to search articles, cases and services.')); ?></p>

            <?php elseif (have_posts()) : ?>
                <ol class="search-list">
                    <?php while (have_posts()) : the_post();
                        $result = theme_get_search_result(get_post());
                        ?>
                        <li>
                            <article class="search-result search-result--<?php echo esc_attr($result['type']); ?>">
                                <span class="search-result__type mono"><?php echo esc_html($result['label']); ?></span>
                                <div class="search-result__body">
                                    <h2 class="search-result__title"><a class="search-result__link" href="<?php echo esc_url($result['url']); ?>"><?php echo theme_search_highlight($result['title'], $query); // escaped inside ?></a></h2>
                                    <?php if ($result['excerpt']) : ?><p class="search-result__excerpt"><?php echo theme_search_highlight($result['excerpt'], $query); // escaped inside ?></p><?php endif; ?>
                                    <?php if ($result['meta']) : ?><p class="search-result__meta mono"><?php echo esc_html($result['meta']); ?></p><?php endif; ?>
                                </div>
                                <span class="icon-btn search-result__arrow" aria-hidden="true"><?php echo theme_icon('arrow-up-right'); ?></span>
                            </article>
                        </li>
                    <?php endwhile; ?>
                </ol>

                <?php get_template_part('template-parts/blog/pagination', null, array('current' => $paged, 'total' => (int)$wp_query->max_num_pages)); ?>

            <?php else : ?>
                <div class="search-empty">
                    <h2 class="search-empty__title"><?php echo esc_html(sprintf(theme_t('Nothing found for “%s”'), $query)); ?></h2>
                    <p class="search-empty__text"><?php echo esc_html(theme_t('Check the spelling or try a shorter query. Or go straight to the section you need:')); ?></p>
                    <ul class="search-empty__links">
                        <li><a class="pick" href="<?php echo esc_url(trailingslashit($home) . '#services'); ?>"><?php echo esc_html(theme_t('Services')); ?></a></li>
                        <li><a class="pick" href="<?php echo esc_url(get_post_type_archive_link('case')); ?>"><?php echo esc_html(theme_t('Work')); ?></a></li>
                        <li><a class="pick" href="<?php echo esc_url(theme_get_blog_url()); ?>"><?php echo esc_html(theme_t('Blog')); ?></a></li>
                        <li><a class="pick" href="<?php echo esc_url(theme_get_page_url('contact')); ?>"><?php echo esc_html(theme_t('Contact')); ?></a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php
get_footer();
