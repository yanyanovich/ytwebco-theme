<?php
$quote = trim((string)get_field('b_blog_quote_text'));
$author = trim((string)get_field('b_blog_quote_author'));
$company = trim((string)get_field('b_blog_quote_company'));

// Real testimonials only: nothing is rendered unless all three are filled
if ($quote === '' || $author === '' || $company === '') {
    return;
}
?>
<figure class="post-quote<?php echo !empty($block['className']) ? ' ' . esc_attr($block['className']) : ''; ?>">
    <blockquote class="post-quote__text"><p><?php echo esc_html($quote); ?></p></blockquote>
    <figcaption class="post-quote__cite mono">— <?php echo esc_html($author); ?>, <?php echo esc_html($company); ?></figcaption>
</figure>
