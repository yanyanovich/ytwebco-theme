<article id="post-<?php the_ID(); ?>" <?php post_class('example-post-type-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a class="example-post-type-card__thumbnail" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <?php the_post_thumbnail('medium_large'); ?>
        </a>
    <?php endif; ?>

    <header class="example-post-type-card__header">
        <?php the_title('<h2 class="example-post-type-card__title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>'); ?>
    </header>
</article>
