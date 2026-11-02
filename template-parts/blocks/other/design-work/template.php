<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'design-work',
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_dwork_head');
    $works = get_field('b_other_dwork_works') ?: array();
    $link = (array)get_field('b_other_dwork_link');
    $is_telegram = !empty($link['url']) && preg_match('#^https?://(t\.me|telegram\.me)/#i', $link['url']);

    // Gallery tabs: only the paths that have works
    $groups = array();
    foreach ($works as $work) {
        if (!empty($work['image'])) {
            $groups[$work['path'] ?: 'express'][] = $work;
        }
    }
    $tabs = array_filter(array(
        'express' => get_field('b_other_dwork_tab_express'),
        'designer' => get_field('b_other_dwork_tab_designer'),
    ), function ($label, $path) use ($groups) {
        return !empty($groups[$path]);
    }, ARRAY_FILTER_USE_BOTH);
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <?php if (empty($tabs)) : ?>
                <?php // No works yet: a text card that asks for examples in Telegram ?>
                <div class="design-work__card" data-aos="drift-up">
                    <div class="design-work__text">
                        <h3 class="design-work__title"><?php echo esc_html(get_field('b_other_dwork_title')); ?></h3>
                        <?php if ($text = get_field('b_other_dwork_text')) : ?>
                            <p class="design-work__desc"><?php echo esc_html($text); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($link['url'])) : ?>
                        <a<?php echo get_acf_link_attrs($link, 'btn btn--secondary design-work__btn'); ?>>
                            <?php if ($is_telegram) : ?>
                                <span class="design-work__icon" aria-hidden="true"><?php echo theme_icon('telegram'); ?></span>
                            <?php endif; ?>
                            <?php echo esc_html($link['title'] ?: $link['url']); ?>
                            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="design-work__gallery" data-tabs>
                    <?php if (count($tabs) > 1) : ?>
                        <div class="design-work__tabs" role="tablist">
                            <?php $first = true; foreach ($tabs as $path => $label) : ?>
                                <button type="button" class="pick" role="tab" id="<?php echo esc_attr($block_id . '-tab-' . $path); ?>" aria-controls="<?php echo esc_attr($block_id . '-' . $path); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" aria-pressed="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html($label); ?></button>
                            <?php $first = false; endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php $first = true; foreach ($tabs as $path => $label) : ?>
                        <ul class="design-work__grid" role="tabpanel" id="<?php echo esc_attr($block_id . '-' . $path); ?>" aria-labelledby="<?php echo esc_attr($block_id . '-tab-' . $path); ?>"<?php echo $first ? '' : ' hidden'; ?>>
                            <?php foreach ($groups[$path] as $work) : ?>
                                <li class="design-work__item">
                                    <?php echo wp_get_attachment_image((int)$work['image'], 'full', false, array('class' => 'design-work__image', 'loading' => 'lazy', 'alt' => $work['title'])); ?>
                                    <span class="design-work__name"><?php echo esc_html($work['title']); ?></span>
                                    <?php if (!empty($work['credit'])) : ?>
                                        <span class="design-work__credit mono"><?php echo esc_html($work['credit']); ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php $first = false; endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
