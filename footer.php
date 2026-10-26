<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ytweb_theme
 */

?>

<?php
$copyright_string = get_field( 'theme_settings_footer_copyright', 'theme-settings' );
$current_year     = date( "Y" );
$updated_string   = str_replace( "%current_year%", $current_year, $copyright_string ); // Заменяем %current_year% на текущий год
?>
<?php if ( $copyright_string != '' ): ?>
    <div class="">
        <p><?php echo $updated_string; ?></p>
    </div>
<?php endif; ?>
<?php wp_footer(); ?>

<?php theme_print_additional_code('end_of_body'); ?>
</body>
</html>
