<?php
/**
 * Search field + button. @var array $args { @type string $query current query }
 */
$query = (string)($args['query'] ?? '');
$action = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
?>
<form class="search-form" role="search" method="get" action="<?php echo esc_url($action); ?>">
    <label class="screen-reader-text" for="search-field"><?php echo esc_html(theme_t('Search the site')); ?></label>
    <input class="field__input search-form__input" id="search-field" type="search" name="s" value="<?php echo esc_attr($query); ?>"
           placeholder="<?php echo esc_attr(theme_t('Search articles, work and services')); ?>" autocomplete="off" enterkeyhint="search">
    <button class="btn btn--primary search-form__button" type="submit">
        <?php echo esc_html(theme_t('Find')); ?>
        <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
    </button>
</form>
