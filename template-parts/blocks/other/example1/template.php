<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'example-block',
        !empty($block['className']) ? $block['className'] : '',
        !empty($block['align']) ? 'align' . $block['align'] : '',
    );
    $style_attr = get_custom_block_styles($block);

    $title = get_field('b_other_ex1_title');
    ?>
    <section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"<?php echo $style_attr; ?>
             data-section>

    </section>
<?php
endif;
