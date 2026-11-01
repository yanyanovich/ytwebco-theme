<?php
/**
 * Template Name: Policy
 *
 * Legal pages (Privacy policy): one narrow column — breadcrumbs, H1, last updated date, a table of
 * contents built from the H2s of the content and numbered sections. Content = core blocks
 * (H2, paragraphs, lists); every H2 starts a section.
 *
 * @package ytweb_theme
 */

get_header();

while (have_posts()) :
    the_post();

    $sections = theme_get_policy_sections(apply_filters('the_content', get_the_content()));
    ?>
    <main id="content" tabindex="-1" class="site-main policy bp">
        <div class="container policy__inner">
            <?php get_template_part('template-parts/partials/breadcrumbs'); ?>

            <h1 class="policy__title"><?php the_title(); ?></h1>
            <p class="policy__updated mono">
                <?php echo esc_html(theme_t('Last updated:')); ?>
                <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(get_the_modified_date()); ?></time>
            </p>

            <?php if (count($sections['items']) > 1) : ?>
                <nav class="policy__toc" aria-labelledby="policy-toc-title">
                    <span class="label" id="policy-toc-title"><?php echo esc_html(theme_t('Contents')); ?></span>
                    <ol>
                        <?php foreach ($sections['items'] as $index => $item) : ?>
                            <li>
                                <a href="#<?php echo esc_attr($item['id']); ?>">
                                    <span class="mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                    <?php echo esc_html($item['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <div class="policy__content">
                <?php echo $sections['html']; ?>
            </div>
        </div>
    </main>
<?php
endwhile;

get_footer();
