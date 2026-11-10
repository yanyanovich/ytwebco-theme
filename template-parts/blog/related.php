<?php
/**
 * Related block: the service card (ACF related_service) plus 2 posts, otherwise 3 posts.
 * Posts come from the same category first, then the latest.
 *
 * @var array $args { @type int $post_id }
 */
$related = theme_get_related((int)($args['post_id'] ?? 0));

if (empty($related['service']) && empty($related['posts'])) {
    return;
}

$service_id = $related['service'];
$locale = function_exists('pll_current_language') ? (string)pll_current_language('locale') : get_locale();
$is_ua = strpos($locale, 'uk') === 0;
?>
<section class="blog-related bp" data-section>
    <div class="container">
        <div class="blog-related__head">
            <h2 class="blog-related__title"><?php echo esc_html(theme_t('Next articles')); ?></h2>
            <a class="link-ul link-ul--line blog-related__all" href="<?php echo esc_url(theme_get_blog_url()); ?>"><?php echo esc_html(theme_t('All posts')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
        </div>
        <div class="blog-grid blog-grid--related">
            <?php if ($service_id) :
                $desc = (string)get_post_meta($service_id, '_yoast_wpseo_metadesc', true);
                ?>
                <article class="post-card post-card--service">
                    <?php get_template_part('template-parts/blog/cover', null, array('word' => '90+_', 'caption' => theme_t('Service'))); ?>
                    <div class="post-card__body">
                        <div class="post-card__text">
                            <div class="post-card__tags"><span class="post-card__cat mono"><?php echo esc_html(theme_t('Service')); ?></span></div>
                            <h3 class="post-card__title"><a class="post-card__link" href="<?php echo esc_url(get_permalink($service_id)); ?>" data-cta="related-service"><?php echo esc_html(get_the_title($service_id)); ?></a></h3>
                            <?php if ($desc) : ?><p class="post-card__lead"><?php echo esc_html($desc); ?></p><?php endif; ?>
                        </div>
                        <div class="post-card__foot">
                            <p class="post-card__meta mono"><?php echo esc_html(theme_t('from $20/hour')); ?></p>
                            <span class="icon-btn post-card__arrow" aria-hidden="true"><?php echo theme_icon('arrow-up-right'); ?></span>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
            <?php foreach ($related['posts'] as $id) {
                get_template_part('template-parts/blog/post-card', null, array('post_id' => $id));
            } ?>
        </div>
    </div>
</section>
