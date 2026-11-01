<?php
/**
 * Case study page (Case · Desktop / Mobile frames). The sections are case blocks in the editor
 * (hero, work, results, screens, CTA band); previous / next project is added here for every case.
 *
 * @package ytweb_theme
 */

get_header();

while (have_posts()) :
    the_post();

    $neighbours = theme_get_case_neighbours(get_the_ID());
    ?>
    <main id="content" tabindex="-1" class="site-main case-page">
        <?php the_content(); ?>

        <?php if (!empty($neighbours['prev']) && !empty($neighbours['next'])) : ?>
            <nav class="case-nav" aria-label="<?php echo esc_attr(theme_t('More projects')); ?>">
                <div class="container case-nav__inner">
                    <?php foreach (array('prev' => theme_t('Previous project'), 'next' => theme_t('Next project')) as $direction => $label) :
                        $neighbour = $neighbours[$direction];
                        $metric = get_field('case_metric', $neighbour);
                        ?>
                        <a class="case-nav__link case-nav__link--<?php echo esc_attr($direction); ?>" href="<?php echo esc_url(get_permalink($neighbour)); ?>" rel="<?php echo esc_attr($direction); ?>">
                            <span class="label case-nav__label">
                                <?php if ($direction === 'prev') : ?><span class="arrow arrow--back" aria-hidden="true"><?php echo theme_icon('arrow-right', 12); ?></span><?php endif; ?>
                                <?php echo esc_html($label); ?>
                                <?php if ($direction === 'next') : ?><span class="arrow" aria-hidden="true"><?php echo theme_icon('arrow-right', 12); ?></span><?php endif; ?>
                            </span>
                            <span class="case-nav__title"><?php echo esc_html(get_the_title($neighbour)); ?></span>
                            <?php if (!empty($metric)) : ?>
                                <span class="case-nav__metric mono"><?php echo esc_html($metric); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </nav>
        <?php endif; ?>
    </main>
<?php
endwhile;

get_footer();
