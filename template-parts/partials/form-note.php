<?php
/**
 * Small line under a form or its success panel: text + link (Contact page → project brief).
 *
 * @var array $args array{text: string, link: array}
 */
$link = (array)($args['link'] ?? array());
if (empty($link['url'])) {
    return;
}
?>
<p class="form-note">
    <?php if (!empty($args['text'])) : ?>
        <span class="form-note__text"><?php echo esc_html($args['text']); ?></span>
    <?php endif; ?>
    <a<?php echo get_acf_link_attrs($link, 'link-ul link-ul--line form-note__link'); ?>>
        <?php echo esc_html($link['title'] ?: $link['url']); ?>
        <span class="arrow"><?php echo theme_icon('arrow-right', 14); ?></span>
    </a>
</p>
