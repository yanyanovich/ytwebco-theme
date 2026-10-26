<?php
/**
 * EN / UA switch — plain links, current language marked with aria-current.
 *
 * @var array $args {
 *     @type string $size  '' | 'sm'
 * }
 */
$languages = theme_get_languages();

if (count($languages) < 2) {
    return;
}

$classes = 'lang-switch' . (!empty($args['size']) ? ' lang-switch--' . $args['size'] : '');
?>
<nav class="<?php echo esc_attr($classes); ?>" aria-label="<?php echo esc_attr(theme_t('Language')); ?>">
    <?php foreach ($languages as $language) : ?>
        <a href="<?php echo esc_url($language['url']); ?>"
           hreflang="<?php echo esc_attr($language['slug']); ?>"
           lang="<?php echo esc_attr($language['slug']); ?>"
            <?php echo $language['current'] ? 'aria-current="true"' : ''; ?>><?php echo esc_html($language['label']); ?></a>
    <?php endforeach; ?>
</nav>
