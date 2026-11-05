<?php
/**
 * Work archive (/work/, /en/work/). The content comes from the "Archive page" assigned to Cases in
 * Settings → Reading (one per language): Work archive, CTA band and SEO text blocks.
 *
 * @package ytweb_theme
 */

get_header();

$archive_page_content = theme_get_archive_page_content('case');
?>

    <main id="content" tabindex="-1" class="site-main work-page">
        <?php if (!empty($archive_page_content)) : ?>
            <?php echo $archive_page_content; ?>
        <?php else : ?>
            <?php // No archive page yet: the grid with its default heading
            echo render_block(array('blockName' => 'acf/other-blocks-work-archive', 'attrs' => array('name' => 'acf/other-blocks-work-archive', 'data' => array()), 'innerBlocks' => array())); ?>
        <?php endif; ?>
    </main>

<?php
get_footer();
