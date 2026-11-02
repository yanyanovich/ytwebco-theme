<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'compare-table',
        !empty($block['className']) ? $block['className'] : '',
    );

    $split = function ($value) {
        // Only trailing blank lines go: an empty first heading (the corner cell) must stay
        return array_map('trim', preg_split('/\R/u', rtrim((string)$value)));
    };
    $head = (array)get_field('b_other_table_head');
    $title = get_field('b_other_table_title');
    $text = get_field('b_other_table_text');
    $columns = $split(get_field('b_other_table_columns'));
    $rows = get_field('b_other_table_rows') ?: array();
    $note = get_field('b_other_table_note');
    if (empty($rows)) {
        return;
    }
    $corner = array_shift($columns);
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container">
            <?php get_template_part('template-parts/partials/section-head', null, $head); ?>

            <?php if (!empty($title) || !empty($text)) : ?>
                <div class="compare-table__intro grid-12">
                    <?php if (!empty($title)) : ?>
                        <h2 class="h2 compare-table__title" data-aos="rise-mask"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($text)) : ?>
                        <div class="compare-table__text" data-aos="drift-up" data-aos-delay="100"><?php echo theme_paragraphs($text); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php // Wide tables scroll sideways on phones; the row labels stay pinned ?>
            <div class="compare-table__scroll" data-aos="drift-up" data-aos-delay="150" tabindex="0" role="region" aria-label="<?php echo esc_attr($title ?: $head['name'] ?? ''); ?>">
                <table class="compare-table__table compare-table__table--cols-<?php echo esc_attr(count($columns)); ?>">
                    <?php if (array_filter($columns)) : ?>
                        <thead>
                            <tr>
                                <th scope="col" class="compare-table__corner"><?php echo esc_html($corner); ?></th>
                                <?php foreach ($columns as $column) : ?>
                                    <th scope="col"><?php echo esc_html($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                    <?php endif; ?>
                    <tbody>
                        <?php foreach ($rows as $row) :
                            $cells = $split($row['cells']);
                            ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($row['label']); ?></th>
                                <?php foreach ($cells as $cell) : ?>
                                    <td><?php echo esc_html($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($note)) : ?>
                <div class="compare-table__note" data-aos="drift-up"><?php echo theme_paragraphs($note); ?></div>
            <?php endif; ?>
        </div>
    </section>
<?php
endif;
