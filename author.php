<?php
/**
 * Author page: profile of the single author (Yan Tokaruk) with a facts strip and the list of articles.
 * The text is the same for every language through the theme strings (Languages → Strings translations).
 *
 * @package ytweb_theme
 */

get_header();

global $wp_query;
$paged = max(1, (int)get_query_var('paged'));
$ids = wp_list_pluck($wp_query->posts, 'ID');
$total_posts = (int)$wp_query->found_posts;
$contacts = theme_get_contacts();
$home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

// Public profiles from Theme settings → Contacts (Upwork, LinkedIn, GitHub…), only real links
$profiles = array_values(array_filter(theme_get_socials(), function ($social) {
    return $social['url'] && $social['url'] !== '#' && !preg_match('#(^|//)(t\.me|telegram\.me)/#i', $social['url']); // Telegram has its own link above
}));
?>

    <main id="content" tabindex="-1" class="site-main author-page">
        <section class="author-hero bp" data-section>
            <div class="container">
                <?php get_template_part('template-parts/partials/breadcrumbs', null, array('items' => array(
                    array('label' => theme_t('Home'), 'url' => $home),
                    array('label' => theme_t('Blog'), 'url' => theme_get_blog_url()),
                    array('label' => theme_t('Yan Tokaruk')),
                ))); ?>

                <div class="author-hero__grid">
                    <span class="author-hero__avatar mono" aria-hidden="true"><?php echo esc_html(theme_t('YT')); ?></span>
                    <div class="author-hero__text">
                        <p class="label author-hero__eyebrow"><span aria-hidden="true">//</span> <?php echo esc_html(theme_t('Author')); ?></p>
                        <h1 class="author-hero__name"><?php echo esc_html(theme_t('Yan Tokaruk')); ?></h1>
                        <p class="author-hero__role mono"><?php echo esc_html(theme_t('WordPress developer · Lutsk')); ?></p>
                        <p class="author-hero__bio"><?php echo esc_html(theme_t('Web developer from Lutsk. Since 2020 I build WordPress and WooCommerce sites and online stores, speed them up and connect Telegram bots and integrations. I work directly with the client, with no agencies or account managers.')); ?></p>
                        <p class="author-hero__links">
                            <a class="link-ul link-ul--line" href="<?php echo esc_url(theme_get_page_url('process')); ?>"><?php echo esc_html(theme_t('Process')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
                            <a class="link-ul link-ul--line" href="<?php echo esc_url(theme_get_page_url('contact')); ?>"><?php echo esc_html(theme_t('Contact')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
                            <a class="link-ul link-ul--line" href="<?php echo esc_url($contacts['telegram_url']); ?>" target="_blank" rel="noopener noreferrer">Telegram <span class="arrow"><?php echo theme_icon('arrow-up-right', 14); ?></span></a>
                            <?php foreach ($profiles as $profile) : ?>
                                <a class="link-ul link-ul--line" href="<?php echo esc_url($profile['url']); ?>" target="_blank" rel="noopener noreferrer me"><?php echo esc_html($profile['label']); ?> <span class="arrow"><?php echo theme_icon('arrow-up-right', 14); ?></span></a>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>

                <figure class="post-stat post-stat--3 author-hero__stats">
                    <figcaption class="post-stat__head label"><?php echo esc_html(theme_t('Facts')); ?></figcaption>
                    <div class="post-stat__cells">
                        <div class="post-stat__cell">
                            <p class="post-stat__value"><span class="post-stat__number"><?php echo esc_html($total_posts ?: count(get_posts(array('post_type' => 'post', 'posts_per_page' => -1, 'fields' => 'ids')))); ?></span></p>
                            <p class="post-stat__label"><?php echo esc_html(theme_t('articles on the blog')); ?></p>
                        </div>
                        <div class="post-stat__cell">
                            <p class="post-stat__value"><span class="post-stat__number">2020</span></p>
                            <p class="post-stat__label"><?php echo esc_html(theme_t('building WordPress sites')); ?></p>
                        </div>
                        <div class="post-stat__cell">
                            <p class="post-stat__value"><span class="post-stat__number">99</span><span class="post-stat__unit">/ 100</span></p>
                            <p class="post-stat__label"><?php echo esc_html(theme_t('PageSpeed, mobile · ytwebco.com')); ?></p>
                        </div>
                    </div>
                </figure>
            </div>
        </section>

        <section class="container author-page__posts">
            <h2 class="author-page__title"><?php echo esc_html(theme_t('Articles by the author')); ?></h2>
            <?php if (!empty($ids)) : ?>
                <div class="blog-grid">
                    <?php foreach ($ids as $post_id) {
                        get_template_part('template-parts/blog/post-card', null, array('post_id' => $post_id));
                    } ?>
                </div>
                <?php get_template_part('template-parts/blog/pagination', null, array('current' => $paged, 'total' => (int)$wp_query->max_num_pages)); ?>
            <?php else : ?>
                <p class="author-page__empty"><?php echo esc_html(theme_t('No posts yet.')); ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php
get_footer();
