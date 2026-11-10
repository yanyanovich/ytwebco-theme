<?php
/**
 * Blog article (posts). Header with meta row, table of contents (sticky column ≥1200, <details> on mobile),
 * reading column 720px, author box, full CTA card and related block.
 *
 * @package ytweb_theme
 */

get_header();
?>

    <main id="content" tabindex="-1" class="site-main article-page">
        <?php while (have_posts()) : the_post();
            $post_id = get_the_ID();
            $card = theme_get_post_card($post_id);
            $article = theme_prepare_article_content(apply_filters('the_content', get_the_content()));
            $toc = $article['toc'];
            $updated = theme_get_updated_date($post_id);
            $blog_url = theme_get_blog_url();
            $contacts = theme_get_contacts();
            // The full CTA card is written at the end of the post, but the design shows it after the author box
            $full_cta = '';
            if (preg_match('#<aside class="cta-help cta-help--full.*?</aside>#s', $article['html'], $cta_match)) {
                $full_cta = $cta_match[0];
                $article['html'] = str_replace($full_cta, '', $article['html']);
            }

            $toc_items = array_map(function ($item, $index) {
                $number = preg_match('/^\s*(\d+)[\.\)]\s*(.+)$/u', $item['title'], $m) ? sprintf('%02d', $m[1]) : '—';
                $title = isset($m[2]) && $number !== '—' ? $m[2] : $item['title'];
                return array('id' => $item['id'], 'num' => $number, 'title' => $title);
            }, $toc, array_keys($toc));

            $GLOBALS['theme_article_toc'] = count($toc_items) > 1 ? $toc_items : array(); // sticky bar + TOC sheet (footer)
            ?>
            <article class="article" id="post-<?php the_ID(); ?>">
                <header class="article__head bp">
                    <div class="container">
                        <?php get_template_part('template-parts/partials/breadcrumbs', null, array('items' => array(
                            array('label' => theme_t('Home'), 'url' => function_exists('pll_home_url') ? pll_home_url() : home_url('/')),
                            array('label' => theme_t('Blog'), 'url' => $blog_url),
                            array('label' => theme_get_short_title($post_id)),
                        ))); ?>

                        <div class="article__intro">
                            <?php if ($card['category']) : ?><span class="article__cat mono"><?php echo esc_html($card['category']); ?></span><?php endif; ?>
                            <h1 class="article__title"><?php the_title(); ?></h1>
                            <?php $article_lead = trim((string)get_field('lead', $post_id)) ?: $card['lead']; ?>
                            <?php if ($article_lead) : ?><p class="article__lead"><?php echo esc_html($article_lead); ?></p><?php endif; ?>

                            <div class="article__meta">
                                <span class="article__avatar mono" aria-hidden="true"><?php echo esc_html(theme_t('YT')); ?></span>
                                <span class="article__author mono"><?php echo esc_html(theme_t('Yan Tokaruk')); ?></span>
                                <span class="article__meta-item mono"><?php echo esc_html(theme_t('Published')); ?> <time datetime="<?php echo esc_attr($card['iso']); ?>"><?php echo esc_html($card['date']); ?></time></span>
                                <?php if ($updated) : ?>
                                    <span class="article__meta-item mono"><?php echo esc_html(theme_t('Updated')); ?> <time datetime="<?php echo esc_attr($updated); ?>"><?php echo esc_html(theme_blog_date(strtotime($updated))); ?></time></span>
                                <?php endif; ?>
                                <span class="article__meta-item mono"><?php echo esc_html(theme_reading_time_label($card['minutes'])); ?></span>
                            </div>
                        </div>

                        <?php if (count($toc_items) > 1) : ?>
                            <details class="toc-mobile" data-toc-inline>
                                <summary class="toc-mobile__summary">
                                    <span class="toc-mobile__label"><strong><?php echo esc_html(theme_t('Contents')); ?></strong> <span class="mono"><?php echo esc_html(sprintf(theme_t('%s sections'), count($toc_items))); ?></span></span>
                                    <span class="icon-btn toc-mobile__icon" aria-hidden="true"><?php echo theme_icon('plus', 14); ?></span>
                                </summary>
                                <ol class="toc-mobile__list">
                                    <?php foreach ($toc_items as $item) : ?>
                                        <li><a href="#<?php echo esc_attr($item['id']); ?>"><span class="toc__num mono"><?php echo esc_html($item['num']); ?></span> <?php echo esc_html($item['title']); ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </details>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="container article__layout">
                    <?php if (count($toc_items) > 1) : ?>
                        <aside class="article__aside">
                            <nav class="toc" aria-label="<?php echo esc_attr(theme_t('Contents')); ?>" data-toc>
                                <p class="toc__title label"><?php echo esc_html(theme_t('Contents')); ?></p>
                                <ol class="toc__list">
                                    <?php foreach ($toc_items as $item) : ?>
                                        <li><a class="toc__link" href="#<?php echo esc_attr($item['id']); ?>" data-toc-link><span class="toc__num mono"><?php echo esc_html($item['num']); ?></span> <span><?php echo esc_html($item['title']); ?></span></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                            <div class="toc-cta" data-sticky-hide>
                                <p class="toc-cta__title"><?php echo esc_html(theme_t('Site is slow and you can’t see why?')); ?></p>
                                <a class="link-ul link-ul--line toc-cta__link" href="<?php echo esc_url(theme_get_page_url('bryf')); ?>" data-cta="post-aside-brief">
                                    <?php echo esc_html(theme_t('Fill in the brief')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span>
                                </a>
                            </div>
                        </aside>
                    <?php endif; ?>

                    <div class="article__main">
                        <div class="article__content entry-content" data-article-content>
                            <?php echo $article['html']; // the_content output, ids added to H2 ?>
                        </div>

                        <aside class="author-box">
                            <span class="author-box__avatar mono" aria-hidden="true"><?php echo esc_html(theme_t('YT')); ?></span>
                            <div class="author-box__text">
                                <p class="author-box__name"><strong><?php echo esc_html(theme_t('Yan Tokaruk')); ?></strong> <span class="mono"><?php echo esc_html(theme_t('WordPress developer · Lutsk')); ?></span></p>
                                <p class="author-box__bio"><?php echo esc_html(theme_t('Building WordPress sites since 2020. I write about speed, WooCommerce and integrations.')); ?></p>
                                <p class="author-box__links">
                                    <a class="link-ul link-ul--line" href="<?php echo esc_url(theme_get_page_url('process')); ?>"><?php echo esc_html(theme_t('Process')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
                                    <a class="link-ul link-ul--line" href="<?php echo esc_url(theme_get_page_url('contact')); ?>"><?php echo esc_html(theme_t('Contact')); ?> <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span></a>
                                </p>
                            </div>
                        </aside>

                        <?php if ($full_cta !== '') : echo $full_cta; // rendered cta-help block (full variant)
                        else : echo render_block(array('blockName' => 'acf/blog-blocks-cta-help', 'attrs' => array('name' => 'acf/blog-blocks-cta-help', 'data' => array(
                            'b_blog_cta_help_variant' => 'full',
                            'b_blog_cta_help_title' => theme_t('Need help?'),
                            'b_blog_cta_help_text' => theme_t('Site is slow and you can’t see why?') . ' ' . theme_t('I’ll look at your site and give you a fixed price.'),
                        )), 'innerBlocks' => array()));
                        endif; ?>
                    </div>
                </div>

                <?php get_template_part('template-parts/blog/related', null, array('post_id' => $post_id)); ?>
            </article>
        <?php endwhile; ?>
    </main>

<?php
get_footer();
