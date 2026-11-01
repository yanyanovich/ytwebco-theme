<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'work-archive',
        !empty($block['className']) ? $block['className'] : '',
    );

    $title = get_field('b_other_work_title');
    $text = get_field('b_other_work_text');

    $case_ids = theme_get_case_ids();
    $types = get_terms(array(
        'taxonomy' => 'case_type',
        'hide_empty' => true,
        'orderby' => 'term_id',
    ));
    $types = is_wp_error($types) ? array() : $types;

    // ?type=… works without JS: the server marks the other cards hidden, setup-work-filter.js takes over with JS
    $current = isset($_GET['type']) ? sanitize_title(wp_unslash($_GET['type'])) : '';
    if ($current && !in_array($current, wp_list_pluck($types, 'slug'), true)) {
        $current = '';
    }

    $archive_url = get_post_type_archive_link('case');
    $count_label = function ($count) {
        return sprintf($count === 1 ? theme_t('%s project') : theme_t('%s projects'), sprintf('%02d', $count));
    };

    $cards = array();
    foreach ($case_ids as $case_id) {
        $slugs = wp_get_post_terms($case_id, 'case_type', array('fields' => 'slugs'));
        $cards[$case_id] = is_wp_error($slugs) ? array() : $slugs;
    }
    $visible = $current ? count(array_filter($cards, function ($slugs) use ($current) {
        return in_array($current, $slugs, true);
    })) : count($cards);
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-work-filter
             data-count-template="<?php echo esc_attr(theme_t('%s projects')); ?>"
             data-count-template-one="<?php echo esc_attr(theme_t('%s project')); ?>"
             data-section>
        <div class="work-archive__top bp">
            <div class="container">
                <?php get_template_part('template-parts/partials/breadcrumbs', null, array(
                    'items' => array(
                        array('label' => theme_t('Home'), 'url' => home_url('/')),
                        array('label' => $title ?: theme_t('Work')),
                    ),
                )); ?>

                <div class="work-archive__head grid-12">
                    <h1 class="display work-archive__title" data-aos="rise-mask"><?php echo esc_html($title ?: theme_t('Work')); ?></h1>
                    <?php if (!empty($text)) : ?>
                        <p class="work-archive__text" data-aos="drift-up" data-aos-delay="150"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (count($types) > 1) : ?>
                    <div class="work-archive__filter" data-aos="reveal">
                        <span class="label work-archive__filter-label" id="<?php echo esc_attr($block_id); ?>-filter"><?php echo esc_html(theme_t('Filter')); ?></span>
                        <ul class="work-archive__chips" aria-labelledby="<?php echo esc_attr($block_id); ?>-filter">
                            <li>
                                <a class="pick<?php echo $current === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url($archive_url); ?>" data-filter=""<?php echo $current === '' ? ' aria-current="true"' : ''; ?>>
                                    <?php echo esc_html(theme_t('All')); ?> <span class="work-archive__chip-count"><?php echo esc_html(sprintf('%02d', count($cards))); ?></span>
                                </a>
                            </li>
                            <?php foreach ($types as $type) : ?>
                                <li>
                                    <a class="pick<?php echo $current === $type->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('type', $type->slug, $archive_url)); ?>" data-filter="<?php echo esc_attr($type->slug); ?>"<?php echo $current === $type->slug ? ' aria-current="true"' : ''; ?>>
                                        <?php echo esc_html($type->name); ?> <span class="work-archive__chip-count"><?php echo esc_html(sprintf('%02d', $type->count)); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <span class="label work-archive__count" aria-live="polite" data-work-count><?php echo esc_html($count_label($visible)); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($cards)) : ?>
            <div class="container">
                <ul class="work-archive__grid">
                    <?php foreach ($cards as $case_id => $slugs) :
                        $hidden = $current && !in_array($current, $slugs, true);
                        ?>
                        <li class="work-archive__item" data-types="<?php echo esc_attr(implode(' ', $slugs)); ?>"<?php echo $hidden ? ' hidden' : ''; ?>>
                            <?php get_template_part('template-parts/partials/case-card', null, array('post_id' => $case_id, 'heading' => 'h2', 'label' => true)); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>
<?php
endif;
