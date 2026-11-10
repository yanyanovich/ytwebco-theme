<?php
$variant = get_field('b_blog_cta_help_variant') === 'full' ? 'full' : 'inline';
$title = trim((string)get_field('b_blog_cta_help_title'));
$text = trim((string)get_field('b_blog_cta_help_text'));
$link = (array)get_field('b_blog_cta_help_link');
$brief_url = !empty($link['url']) ? $link['url'] : theme_get_page_url('bryf');
$brief_label = !empty($link['title']) ? $link['title'] : theme_t('Fill in the brief');
$contacts = theme_get_contacts();
$classes = array('cta-help', 'cta-help--' . $variant, !empty($block['className']) ? $block['className'] : '');

if ($text === '') {
    return;
}
?>
<aside class="<?php echo esc_attr(trim(implode(' ', array_filter($classes)))); ?>"<?php echo $variant === 'full' ? ' data-sticky-hide data-article-end' : ''; ?>>
    <div class="cta-help__body">
        <?php if ($variant === 'full') : ?>
            <span class="status-pill cta-help__status">
                <span class="dot dot--pulse" aria-hidden="true"></span>
                <?php echo esc_html(theme_t('Available for new projects')); ?>
            </span>
            <h3 class="cta-help__title"><?php echo esc_html($title ?: theme_t('Need help?')); ?></h3>
        <?php endif; ?>
        <p class="cta-help__text"><?php echo esc_html($text); ?></p>
    </div>
    <div class="cta-help__actions">
        <?php if ($variant === 'full') : ?>
            <a class="btn btn--primary" href="<?php echo esc_url($brief_url); ?>" data-cta="post-cta-brief">
                <?php echo esc_html($brief_label); ?>
                <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
            </a>
            <a class="btn btn--secondary" href="<?php echo esc_url($contacts['telegram_url']); ?>" target="_blank" rel="noopener noreferrer" data-cta="post-cta-telegram">
                <?php echo esc_html(theme_t('Message on Telegram')); ?>
            </a>
        <?php else : ?>
            <a class="btn btn--secondary" href="<?php echo esc_url($brief_url); ?>" data-cta="post-cta-inline">
                <?php echo esc_html($brief_label); ?>
            </a>
        <?php endif; ?>
    </div>
</aside>
