<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    get_template_part('template-parts/partials/cta-band', null, array(
        'status' => get_field('b_other_cta_status'),
        'title' => get_field('b_other_cta_title'),
        'text' => get_field('b_other_cta_text'),
        'link_primary' => (array)get_field('b_other_cta_link_primary'),
        'link_secondary' => (array)get_field('b_other_cta_link_secondary'),
        'class' => !empty($block['className']) ? $block['className'] : '',
    ));
endif;
