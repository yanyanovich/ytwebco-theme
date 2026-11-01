<?php
/**
 * Case results check list (right column next to the outcome line or a single number).
 *
 * @var array $args {
 *     @type string[] $points
 * }
 */
$points = array_values((array)($args['points'] ?? array()));

if (empty($points)) {
    return;
}
?>
<ul class="case-results__points">
    <?php foreach ($points as $i => $point) : ?>
        <li data-aos="drift-up" data-aos-delay="<?php echo esc_attr($i * 80); ?>">
            <span class="case-results__check" aria-hidden="true"><?php echo theme_icon('check', 12); ?></span>
            <?php echo esc_html($point); ?>
        </li>
    <?php endforeach; ?>
</ul>
