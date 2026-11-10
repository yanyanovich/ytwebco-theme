<?php
$label = trim((string)get_field('b_blog_callout_label'));
$text = trim((string)get_field('b_blog_callout_text'));

if ($text === '') {
    return;
}
?>
<aside class="post-callout<?php echo !empty($block['className']) ? ' ' . esc_attr($block['className']) : ''; ?>">
    <p class="post-callout__text">
        <?php if ($label !== '') : ?><strong class="post-callout__label"><?php echo esc_html($label); ?></strong> <?php endif; ?>
        <?php echo wp_kses($text, array('a' => array('href' => array(), 'title' => array()), 'strong' => array(), 'em' => array(), 'code' => array())); ?>
    </p>
</aside>
