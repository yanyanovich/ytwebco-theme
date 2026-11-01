<?php
/**
 * Case study page (Case · Desktop / Mobile frames): title + meta + hero, the challenge,
 * what I did, results, screens, CTA band and previous / next project.
 * Sections without data are skipped and the 01 / 02 / … numbering follows what is shown.
 *
 * @package ytweb_theme
 */

get_header();

while (have_posts()) :
    the_post();

    $post_id = get_the_ID();
    $card = theme_get_case_card($post_id);

    $category = get_field('case_category');
    $summary = get_field('case_summary');
    $live_url = get_field('case_live_url');
    $hero_id = (int)get_field('case_hero');
    $meta = array(
        theme_t('Client') => get_field('case_client'),
        theme_t('Role') => get_field('case_role'),
        theme_t('Engagement') => get_field('case_engagement'),
        theme_t('Year') => get_field('case_year'),
    );

    $challenge_lead = get_field('case_challenge_lead');
    $challenge_text = get_field('case_challenge_text');
    $did = (array)get_field('case_did');

    $results_meta = get_field('case_results_meta');
    $results_type = get_field('case_results_type') ?: 'numbers';
    $results = (array)get_field('case_results');
    $compare = (array)get_field('case_compare');
    $outcome_line = get_field('case_outcome_line');
    $outcome_text = get_field('case_outcome_text');
    $points = array_filter(array_map('trim', preg_split('/\R/', (string)get_field('case_outcome_points'))));

    $stack = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)get_field('case_page_stack')))));
    $stack = !empty($stack) ? $stack : $card['stack'];

    $screens_desktop = (array)get_field('case_screens_desktop');
    $screens_mobile = (array)get_field('case_screens_mobile');
    $translated = get_field('case_translated');

    $has_results = $results_type === 'outcome' ? !empty($outcome_line) : !empty($results);
    $has_compare = $results_type === 'numbers' && count($results) === 1 && !empty($compare['before']) && !empty($compare['after']);

    // "After" bar width = after / before (numbers only, "20,000+" → 20000)
    $compare_width = 0;
    if ($has_compare) {
        $to_number = function ($value) {
            return (float)preg_replace('/[^\d.]/', '', str_replace(',', '', $value));
        };
        $before = $to_number($compare['before']);
        $compare_width = $before > 0 ? max(2, min(100, $to_number($compare['after']) / $before * 100)) : 0;
    }

    $section = 0;
    $index = function () use (&$section) {
        return sprintf('%02d', ++$section);
    };

    $neighbours = theme_get_case_neighbours($post_id);
    $contacts = theme_get_contacts();
    ?>
    <main id="content" tabindex="-1" class="site-main case-page">
        <section class="case-hero bp" data-section>
            <div class="container">
                <?php get_template_part('template-parts/partials/breadcrumbs', null, array(
                    'items' => array(
                        array('label' => theme_t('Home'), 'url' => home_url('/')),
                        array('label' => theme_t('Work'), 'url' => get_post_type_archive_link('case')),
                        array('label' => get_the_title()),
                    ),
                )); ?>

                <div class="case-hero__head grid-12">
                    <div class="case-hero__intro">
                        <?php if (!empty($category)) : ?>
                            <span class="label case-hero__category" data-aos="reveal"><?php echo esc_html($category); ?></span>
                        <?php endif; ?>
                        <h1 class="h1 case-hero__title" data-aos="rise-mask" data-aos-delay="100"><?php the_title(); ?></h1>
                        <?php if (!empty($summary)) : ?>
                            <p class="case-hero__summary" data-aos="drift-up" data-aos-delay="200"><?php echo esc_html($summary); ?></p>
                        <?php endif; ?>
                    </div>

                    <dl class="case-hero__meta" data-aos="drift-up" data-aos-delay="250">
                        <?php foreach ($meta as $label => $value) :
                            if (empty($value)) {
                                continue;
                            }
                            ?>
                            <div class="case-hero__meta-row">
                                <dt class="label"><?php echo esc_html($label); ?></dt>
                                <dd class="mono"><?php echo esc_html($value); ?></dd>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!empty($live_url)) : ?>
                            <div class="case-hero__meta-row">
                                <dt class="label"><?php echo esc_html(theme_t('Live site')); ?></dt>
                                <dd class="mono">
                                    <a class="link-ul" href="<?php echo esc_url($live_url); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($card['domain'] ?: wp_parse_url($live_url, PHP_URL_HOST)); ?>
                                        <span class="arrow arrow--diag"><?php echo theme_icon('arrow-up-right', 12); ?></span>
                                        <span class="screen-reader-text"><?php echo esc_html(theme_t('opens in a new tab')); ?></span>
                                    </a>
                                </dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <?php if (!empty($hero_id)) : ?>
                    <div class="case-frame case-hero__frame" data-aos="soft-scale" data-aos-delay="300">
                        <div class="case-frame__bar" aria-hidden="true">
                            <i></i><i></i><i></i>
                            <?php if (!empty($card['domain'])) : ?>
                                <span class="mono"><?php echo esc_html($card['domain']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="case-hero__shot">
                            <?php echo wp_get_attachment_image($hero_id, 'full', false, array(
                                'class' => 'case-frame__image',
                                'sizes' => '(min-width: 1440px) 1280px, calc(100vw - 40px)',
                                'loading' => 'eager',
                                'fetchpriority' => 'high',
                            )); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php if (!empty($challenge_lead) || !empty($did)) : ?>
            <section class="case-work<?php echo empty($challenge_lead) ? ' case-work--wide' : ''; ?>" data-section>
                <div class="container grid-12">
                    <?php if (!empty($challenge_lead)) : ?>
                        <div class="case-work__challenge">
                            <?php get_template_part('template-parts/partials/section-head', null, array('index' => $index(), 'name' => theme_t('The challenge'))); ?>
                            <p class="case-work__lead" data-aos="drift-up"><?php echo esc_html($challenge_lead); ?></p>
                            <?php if (!empty($challenge_text)) : ?>
                                <p class="case-work__text" data-aos="drift-up" data-aos-delay="100"><?php echo esc_html($challenge_text); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($did)) : ?>
                        <div class="case-work__did">
                            <?php get_template_part('template-parts/partials/section-head', null, array(
                                'index' => $index(),
                                'name' => theme_t('What I did'),
                                'meta' => empty($challenge_lead) ? $meta[theme_t('Role')] : '',
                            )); ?>
                            <ol class="case-did">
                                <?php foreach ($did as $i => $item) : ?>
                                    <li class="case-did__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(min($i * 60, 360)); ?>">
                                        <span class="case-did__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
                                        <span class="case-did__text">
                                            <?php if (!empty($item['lead'])) : ?>
                                                <b><?php echo esc_html($item['lead']); ?></b>
                                            <?php endif; ?>
                                            <?php echo esc_html($item['text']); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($has_results || !empty($stack)) : ?>
            <section class="case-results" data-section>
                <div class="container">
                    <?php if ($has_results) : ?>
                        <?php get_template_part('template-parts/partials/section-head', null, array('index' => $index(), 'name' => theme_t('Results'), 'meta' => $results_meta)); ?>

                        <?php if ($results_type === 'outcome') : ?>
                            <div class="case-results__body case-results__body--outcome grid-12">
                                <div class="case-results__main" data-aos="drift-up">
                                    <span class="case-results__line mono"><?php echo esc_html($outcome_line); ?></span>
                                    <?php if (!empty($outcome_text)) : ?>
                                        <span class="case-results__label"><?php echo esc_html($outcome_text); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php get_template_part('template-parts/partials/case-points', null, array('points' => $points)); ?>
                            </div>
                        <?php else : ?>
                            <div class="case-results__body<?php echo count($results) > 1 ? ' case-results__body--pair' : ''; ?> grid-12" data-section="0.5" data-count-group>
                                <?php foreach ($results as $i => $result) : ?>
                                    <div class="case-results__main" data-aos="drift-up" data-aos-delay="<?php echo esc_attr($i * 120); ?>">
                                        <span class="case-results__number mono"><span data-count data-delay="<?php echo esc_attr($i * 120); ?>"><?php echo esc_html($result['value']); ?></span></span>
                                        <?php if (!empty($result['label'])) : ?>
                                            <span class="case-results__label"><?php echo esc_html($result['label']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($result['note'])) : ?>
                                            <span class="case-results__note mono"><?php echo esc_html($result['note']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ($has_compare) : ?>
                                    <div class="case-compare" data-aos="drift-up" data-aos-delay="150">
                                        <div class="case-compare__row">
                                            <div class="case-compare__head">
                                                <span class="label"><?php echo esc_html(theme_t('Before')); ?></span>
                                                <span class="case-compare__value mono"><?php echo esc_html($compare['before']); ?><?php if (!empty($compare['unit'])) : ?><span> <?php echo esc_html($compare['unit']); ?></span><?php endif; ?></span>
                                            </div>
                                            <span class="case-compare__bar" aria-hidden="true"><span style="width: 100%" data-bar="100"></span></span>
                                        </div>
                                        <div class="case-compare__row case-compare__row--after">
                                            <div class="case-compare__head">
                                                <span class="label"><?php echo esc_html(theme_t('After')); ?></span>
                                                <span class="case-compare__value mono"><?php echo esc_html($compare['after']); ?><?php if (!empty($compare['unit'])) : ?><span> <?php echo esc_html($compare['unit']); ?></span><?php endif; ?></span>
                                            </div>
                                            <span class="case-compare__bar" aria-hidden="true"><span style="width: <?php echo esc_attr(round($compare_width, 1)); ?>%" data-bar="<?php echo esc_attr(round($compare_width, 1)); ?>"></span></span>
                                        </div>
                                    </div>
                                <?php elseif (count($results) === 1) : ?>
                                    <?php get_template_part('template-parts/partials/case-points', null, array('points' => $points)); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($stack)) : ?>
                        <div class="case-results__stack">
                            <span class="label"><?php echo esc_html(theme_t('Stack')); ?></span>
                            <ul class="case-results__chips">
                                <?php foreach ($stack as $item) : ?>
                                    <li class="chip"><?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($screens_desktop) || !empty($screens_mobile)) : ?>
            <section class="case-screens" data-section>
                <div class="container">
                    <?php get_template_part('template-parts/partials/section-head', null, array(
                        'index' => $index(),
                        'name' => theme_t('Screens'),
                        'meta' => theme_t('Desktop + mobile'),
                        'meta_mobile' => !empty($screens_mobile) ? theme_t('Swipe →') : '',
                        'class' => 'case-screens__head',
                    )); ?>

                    <?php if (!empty($screens_desktop)) : ?>
                        <div class="case-screens__desktop">
                            <?php foreach ($screens_desktop as $i => $screen) {
                                get_template_part('template-parts/partials/case-screen', null, array('screen' => $screen, 'device' => 'desktop', 'delay' => ($i % 2) * 120));
                            } ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($screens_mobile)) : ?>
                        <div class="case-screens__mobile bp" data-aos="drift-up">
                            <div class="case-screens__track">
                                <?php foreach ($screens_mobile as $screen) {
                                    get_template_part('template-parts/partials/case-screen', null, array('screen' => $screen, 'device' => 'mobile'));
                                } ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($translated)) : ?>
                        <p class="case-screens__note mono"><?php echo esc_html(theme_t('Screenshots are shown in English translation; the live site is in Ukrainian.')); ?></p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php get_template_part('template-parts/partials/cta-band', null, array(
            'status' => theme_t('Available for new projects'),
            'title' => theme_t('Want results like this?'),
            'link_primary' => array('url' => theme_get_booking_url(), 'title' => theme_t('Book a free call')),
            'link_secondary' => array('url' => $contacts['telegram_url'], 'title' => theme_t('Message on Telegram'), 'target' => '_blank'),
        )); ?>

        <?php if (!empty($neighbours['prev']) && !empty($neighbours['next'])) : ?>
            <nav class="case-nav" aria-label="<?php echo esc_attr(theme_t('More projects')); ?>">
                <div class="container case-nav__inner">
                    <?php foreach (array('prev' => theme_t('Previous project'), 'next' => theme_t('Next project')) as $direction => $label) :
                        $neighbour = $neighbours[$direction];
                        ?>
                        <a class="case-nav__link case-nav__link--<?php echo esc_attr($direction); ?>" href="<?php echo esc_url(get_permalink($neighbour)); ?>" rel="<?php echo esc_attr($direction); ?>">
                            <span class="label case-nav__label">
                                <?php if ($direction === 'prev') : ?><span class="arrow arrow--back" aria-hidden="true"><?php echo theme_icon('arrow-right', 12); ?></span><?php endif; ?>
                                <?php echo esc_html($label); ?>
                                <?php if ($direction === 'next') : ?><span class="arrow" aria-hidden="true"><?php echo theme_icon('arrow-right', 12); ?></span><?php endif; ?>
                            </span>
                            <span class="case-nav__title"><?php echo esc_html(get_the_title($neighbour)); ?></span>
                            <?php if ($metric = get_field('case_metric', $neighbour)) : ?>
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
