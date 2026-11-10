<?php
$title = trim((string)get_field('b_blog_faq_title'));
$items = array_values(array_filter((array)(get_field('b_blog_faq_items') ?: array()), function ($item) {
    return !empty($item['question']) && !empty($item['answer']);
}));

if (empty($items)) {
    return;
}
?>
<section class="post-faq<?php echo !empty($block['className']) ? ' ' . esc_attr($block['className']) : ''; ?>">
    <h2 class="post-faq__title"><?php echo esc_html($title ?: theme_t('Questions')); ?></h2>
    <div class="post-faq__list">
        <?php foreach ($items as $index => $item) : ?>
            <details class="acc" data-accordion>
                <summary class="acc__summary">
                    <h3 class="acc__title"><?php echo esc_html($item['question']); ?></h3>
                    <span class="icon-btn icon-btn--plus acc__icon" aria-hidden="true"><?php echo theme_icon('plus'); ?></span>
                </summary>
                <div class="acc__body">
                    <div class="acc__inner">
                        <div class="acc__content">
                            <p class="acc__text"><?php echo wp_kses($item['answer'], array('a' => array('href' => array()))); ?></p>
                        </div>
                    </div>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
