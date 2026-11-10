<?php
/**
 * Typographic cover (Blog): mono word + caption on the blueprint grid. A trailing "_" is the lime cursor.
 *
 * @var array $args {
 *     @type string $word     cover text, e.g. "AJAX_"
 *     @type string $caption  small mono line, e.g. "cart → checkout"
 *     @type string $variant  card | featured
 *     @type string $number   featured only: the big lime number (falls back to the word)
 *     @type string $label    featured only: category label, top right
 * }
 */
$word = (string)($args['word'] ?? '');
$caption = (string)($args['caption'] ?? '');
$variant = ($args['variant'] ?? 'card') === 'featured' ? 'featured' : 'card';
$number = trim((string)($args['number'] ?? ''));
$label = (string)($args['label'] ?? '');
$host = 'ytwebco.com/blog';

$render_word = function ($text) {
    if ($text !== '' && substr($text, -1) === '_') {
        return esc_html(substr($text, 0, -1)) . '<span class="post-cover__cursor">_</span>';
    }
    return esc_html($text);
};
?>
<div class="post-cover post-cover--<?php echo esc_attr($variant); ?> bp" aria-hidden="true">
    <div class="post-cover__top mono">
        <span class="post-cover__url"><?php echo esc_html($host); ?></span>
        <?php if ($variant === 'featured' && $label !== '') : ?><span><?php echo esc_html($label); ?></span><?php endif; ?>
    </div>
    <div class="post-cover__main">
        <?php if ($variant === 'featured' && $number !== '') : ?>
            <span class="post-cover__number"><?php echo esc_html($number); ?></span>
        <?php else : ?>
            <span class="post-cover__word mono"><?php echo $render_word($word); // escaped in the closure ?></span>
        <?php endif; ?>
        <?php if ($caption !== '') : ?><span class="post-cover__caption mono"><?php echo esc_html($caption); ?></span><?php endif; ?>
    </div>
</div>
