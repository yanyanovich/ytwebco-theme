<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $layout = get_field('b_other_info_layout') ?: 'list';
    $block_classes = array(
        'info-list',
        'info-list--' . $layout,
        !empty($block['className']) ? $block['className'] : '',
    );

    $head = (array)get_field('b_other_info_head');
    $title = get_field('b_other_info_title');
    $text = get_field('b_other_info_text');
    $items = (array)get_field('b_other_info_items');
    $note = get_field('b_other_info_note');
    $link = (array)get_field('b_other_info_link');
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php if ($layout !== 'band') : ?>
                <?php get_template_part('template-parts/partials/section-head', null, $head); ?>
            <?php endif; ?>

            <div class="info-list__body<?php echo $layout !== 'band' ? ' grid-12' : ''; ?>">
                <?php if ($layout !== 'band' && (!empty($title) || !empty($text))) : ?>
                    <div class="info-list__intro">
                        <?php if (!empty($title)) : ?>
                            <h2 class="h2 info-list__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                        <?php if (!empty($text)) : ?>
                            <div class="info-list__text" data-aos="drift-up" data-aos-delay="100"><?php echo theme_paragraphs($text); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($items) || !empty($note) || !empty($link['url'])) : ?>
                    <div class="info-list__main">
                        <?php if (!empty($items)) : ?>
                            <<?php echo $layout === 'steps' ? 'ol' : 'ul'; ?> class="info-list__items info-list__items--<?php echo esc_attr(count($items)); ?>">
                                <?php foreach ($items as $index => $item) : ?>
                                    <li class="info-list__item" data-aos="drift-up" data-aos-delay="<?php echo esc_attr(min($index * 80, 320)); ?>">
                                        <?php if ($layout === 'steps') : ?>
                                            <span class="info-list__index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                        <?php elseif ($layout === 'list') : ?>
                                            <span class="info-list__mark" aria-hidden="true"><?php echo theme_icon('check', 14); ?></span>
                                        <?php endif; ?>
                                        <span class="info-list__content">
                                            <?php if (!empty($item['title'])) : ?>
                                                <strong class="info-list__name"><?php echo esc_html($item['title']); ?></strong>
                                            <?php endif; ?>
                                            <?php if (!empty($item['text'])) : ?>
                                                <span class="info-list__desc"><?php echo esc_html($item['text']); ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </<?php echo $layout === 'steps' ? 'ol' : 'ul'; ?>>
                        <?php endif; ?>

                        <?php if (!empty($note)) : ?>
                            <div class="info-list__note"><?php echo theme_paragraphs($note); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($link['url'])) : ?>
                            <a<?php echo get_acf_link_attrs($link, 'btn btn--primary info-list__btn'); ?>>
                                <?php echo esc_html($link['title'] ?: $link['url']); ?>
                                <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
endif;
