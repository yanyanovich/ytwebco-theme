<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Open Graph images for blog posts (1200×630 PNG), drawn with GD + FreeType from the design hand-off template
 * (frames 14–17). Generated when a post is saved, stored as uploads/og-{lang}-{slug}.png and handed to Yoast.
 * A manually chosen Yoast social image always wins. Fonts: fonts/og/*.ttf, static instances built from the theme's own
 * variable woff2 files (.agent-notes/scripts/make-og-fonts.py), because GD cannot read woff2 or variable fonts.
 *
 * Title size by length: ≤26 chars → 104px, 1 line · ≤70 → 72px, ≤2 lines · ≤110 → 58px, ≤3 lines · longer → no image.
 * "|" in og_title forces a line break; a no-break space follows every number so a line never ends with "10".
 */

const THEME_OG_W = 1200;
const THEME_OG_H = 630;
const THEME_OG_MAX_CHARS = 110;

function theme_og_font($name)
{
    return get_template_directory() . '/fonts/og/' . $name . '.ttf';
}

/**
 * Per-glyph advance in px (GD gives ink boxes only, so it is measured as "|c|" minus "||").
 * GD ignores GPOS kerning (the fonts have no 'kern' table); the negative tracking of the design hides most of it.
 */
function theme_og_advance($font, $px, $char)
{
    static $cache = array();
    $key = $font . '|' . $px . '|' . $char;

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    // "|c|" minus "||": the first bar's offset and both bars' bearings cancel, what is left is the advance of c
    $pt = $px * 0.75;
    $bars = imagettfbbox($pt, 0, $font, '||');
    $glyph = $char === "\xC2\xA0" ? ' ' : $char;
    $with = imagettfbbox($pt, 0, $font, '|' . $glyph . '|');

    return $cache[$key] = ($with[2] - $with[0]) - ($bars[2] - $bars[0]);
}

function theme_og_chars($text)
{
    return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
}

function theme_og_text_width($font, $px, $text, $spacing_em = 0.0)
{
    $width = 0.0;
    $chars = theme_og_chars($text);

    foreach ($chars as $char) {
        $width += theme_og_advance($font, $px, $char) + $spacing_em * $px;
    }

    return $width;
}

/**
 * Draws text char by char (letter-spacing). $baseline is the y of the baseline, $x the left edge.
 */
function theme_og_text($im, $font, $px, $x, $baseline, $text, $color, $spacing_em = 0.0)
{
    foreach (theme_og_chars($text) as $char) {
        $glyph = $char === "\xC2\xA0" ? ' ' : $char;
        if ($glyph !== ' ') {
            imagettftext($im, $px * 0.75, 0, (int)round($x), (int)round($baseline), $color, $font, $glyph);
        }
        $x += theme_og_advance($font, $px, $char) + $spacing_em * $px;
    }

    return $x;
}

/**
 * Greedy wrap of one segment into lines no wider than $max; words split on a regular space only (NBSP sticks).
 */
function theme_og_wrap_greedy($font, $px, $text, $max, $spacing_em)
{
    $words = array_values(array_filter(explode(' ', $text), function ($w) {
        return $w !== '';
    }));
    $lines = array();
    $current = '';

    foreach ($words as $word) {
        $try = $current === '' ? $word : $current . ' ' . $word;
        if ($current !== '' && theme_og_text_width($font, $px, $try, $spacing_em) > $max) {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $try;
        }
    }

    if ($current !== '') {
        $lines[] = $current;
    }

    return $lines;
}

/**
 * Wrap with "balance": the same number of lines as the greedy wrap, but the narrowest width that still fits.
 */
function theme_og_wrap_balanced($font, $px, $text, $max, $spacing_em)
{
    $lines = theme_og_wrap_greedy($font, $px, $text, $max, $spacing_em);
    $count = count($lines);

    if ($count < 2) {
        return $lines;
    }

    $high = $max;
    $low = $max / $count / 2;

    for ($i = 0; $i < 14; $i++) {
        $mid = ($low + $high) / 2;
        if (count(theme_og_wrap_greedy($font, $px, $text, $mid, $spacing_em)) <= $count) {
            $high = $mid;
        } else {
            $low = $mid;
        }
    }

    return theme_og_wrap_greedy($font, $px, $text, $high + 1, $spacing_em);
}

/**
 * Title lines and size: array(size, line-height, lines) or WP_Error when the title is too long.
 */
function theme_og_layout_title($title)
{
    // a no-break space after every number ("10 steps" never breaks between "10" and "steps")
    $title = preg_replace('/(\d[\d.,+%]*)\s+/u', "$1\xC2\xA0", trim($title));
    // one-letter words (з, в, у, і, й, a, I) stay with the next word: no line ends with a lonely preposition
    $title = preg_replace('/(^|\s|\|)(\p{L})\s+(?=\S)/u', "$1$2\xC2\xA0", $title);
    $chars = mb_strlen(str_replace('|', '', $title));

    if ($chars > THEME_OG_MAX_CHARS) {
        return new WP_Error('og_too_long', sprintf('og_title has %d characters, the limit is %d', $chars, THEME_OG_MAX_CHARS));
    }

    $font = theme_og_font('inter-tight-500');
    $tiers = array(
        array('px' => 104, 'lh' => 0.95, 'lines' => 1, 'upto' => 26),
        array('px' => 72, 'lh' => 1.02, 'lines' => 2, 'upto' => 70),
        array('px' => 58, 'lh' => 1.02, 'lines' => 3, 'upto' => 110),
    );
    $segments = preg_split('/\s*\|\s*/u', $title);

    foreach ($tiers as $tier) {
        if ($chars > $tier['upto']) {
            continue;
        }
        $lines = array();
        foreach ($segments as $segment) {
            $lines = array_merge($lines, theme_og_wrap_balanced($font, $tier['px'], $segment, 1056, -0.045));
        }
        if (count($lines) <= $tier['lines']) {
            return array('px' => $tier['px'], 'lh' => $tier['lh'], 'lines' => $lines);
        }
    }

    return new WP_Error('og_too_long', 'og_title does not fit on three lines, shorten it');
}

/**
 * Draws the image. $data: title, category, minutes, lang (ua|en), stat (optional "99 / 100 PageSpeed").
 *
 * @return resource|GdImage|WP_Error
 */
function theme_og_render(array $data)
{
    if (!function_exists('imagettftext')) {
        return new WP_Error('og_no_gd', 'GD with FreeType is not available on this server');
    }

    $layout = theme_og_layout_title($data['title']);
    if (is_wp_error($layout)) {
        return $layout;
    }

    $im = imagecreatetruecolor(THEME_OG_W, THEME_OG_H);
    imagealphablending($im, true);
    imagesavealpha($im, false);

    $c = function ($r, $g, $b, $a = 0) use ($im) {
        return imagecolorallocatealpha($im, $r, $g, $b, $a);
    };
    $bg = $c(14, 15, 17);
    $text = $c(237, 237, 237);
    $text2 = $c(184, 187, 192);
    $muted = $c(142, 145, 150);
    $lime = $c(198, 244, 50);
    $grid = $c(23, 24, 26);
    $line = $c(36, 38, 43);

    imagefilledrectangle($im, 0, 0, THEME_OG_W, THEME_OG_H, $bg);
    for ($x = 0; $x < THEME_OG_W; $x += 80) {
        imageline($im, $x, 0, $x, THEME_OG_H, $grid);
    }
    for ($y = 0; $y < THEME_OG_H; $y += 80) {
        imageline($im, 0, $y, THEME_OG_W, $y, $grid);
    }

    $inter500 = theme_og_font('inter-tight-500');
    $inter600 = theme_og_font('inter-tight-600');
    $mono400 = theme_og_font('jetbrains-mono-400');
    $mono500 = theme_og_font('jetbrains-mono-500');

    // font metrics (hhea) as a fraction of the em
    $inter_asc = 0.96875;
    $inter_content = 1.21;
    $mono_asc = 1.02;
    $mono_content = 1.32;

    $left = 72;
    $right = THEME_OG_W - 72;

    // ---- header: logo + host
    $header_center = 56 + (34 * $mono_content) / 2;
    $x = $left;
    $baseline = $header_center - (34 * $mono_content) / 2 + 34 * $mono_asc;
    $x = theme_og_text($im, $mono500, 34, $x, $baseline, 'yt', $text, -0.06);
    $x = theme_og_text($im, $mono500, 34, $x, $baseline, '/', $lime, -0.06);
    $x += 14;
    imagefilledrectangle($im, (int)round($x), (int)round($header_center - 16), (int)round($x), (int)round($header_center + 15), $c(42, 44, 49));
    $x += 1 + 14;
    $baseline = $header_center - (24 * $inter_content) / 2 + 24 * $inter_asc;
    theme_og_text($im, $inter600, 24, $x, $baseline, 'web co.', $text, -0.02);

    $host = $data['lang'] === 'en' ? 'ytwebco.com/en/blog' : 'ytwebco.com/blog';
    $w = theme_og_text_width($mono400, 18, $host);
    $baseline = $header_center - (18 * $mono_content) / 2 + 18 * $mono_asc;
    theme_og_text($im, $mono400, 18, $right - $w, $baseline, $host, $muted);

    // ---- footer geometry (needed to place the title block)
    $stat = trim((string)($data['stat'] ?? ''));
    $footer_content = $stat !== '' ? 58 : 56;
    $content_bottom = THEME_OG_H - 48;
    $footer_top = $content_bottom - $footer_content - 24 - 1;

    // ---- title block, bottom-aligned 40px above the footer rule
    $px = $layout['px'];
    $line_h = $px * $layout['lh'];
    $title_bottom = $footer_top - 40;
    $title_top = $title_bottom - $line_h * count($layout['lines']);

    foreach ($layout['lines'] as $i => $line_text) {
        $line_top = $title_top + $i * $line_h;
        $baseline = $line_top + ($line_h - $px * $inter_content) / 2 + $px * $inter_asc;
        theme_og_text($im, $inter500, $px, $left, $baseline, $line_text, $text, -0.045);
    }

    // ---- category chip, 28px above the title
    $category = trim((string)($data['category'] ?? ''));
    if ($category !== '') {
        $chip_h = 40;
        $chip_top = $title_top - 28 - $chip_h;
        $label_w = theme_og_text_width($mono400, 18, $category);
        $chip_w = 1 + 14 + 8 + 10 + $label_w + 14 + 1;
        imagefilledrectangle($im, $left, (int)round($chip_top), (int)round($left + $chip_w), (int)round($chip_top + $chip_h), $c(198, 244, 50, 118)); // ~8% lime
        imagerectangle($im, $left, (int)round($chip_top), (int)round($left + $chip_w), (int)round($chip_top + $chip_h), $c(198, 244, 50, 76)); // ~40% lime
        $cy = $chip_top + $chip_h / 2;
        imagefilledellipse($im, (int)round($left + 1 + 14 + 4), (int)round($cy), 8, 8, $lime);
        $baseline = $cy - (18 * $mono_content) / 2 + 18 * $mono_asc;
        theme_og_text($im, $mono400, 18, $left + 1 + 14 + 8 + 10, $baseline, $category, $lime);
    }

    // ---- footer rule, author line, stat or arrow box
    imageline($im, $left, (int)round($footer_top), $right, (int)round($footer_top), $line);

    $minutes = (int)$data['minutes'];
    $byline = $data['lang'] === 'en'
        ? sprintf('Yan Tokaruk · %d min read', $minutes)
        : sprintf('Ян Токарук · %d хв читання', $minutes);
    $box_top = $content_bottom - 4 - 20 * $mono_content;
    theme_og_text($im, $mono400, 20, $left, $box_top + 20 * $mono_asc, $byline, $text2);

    if ($stat !== '') {
        $parts = preg_split('/\s+/u', $stat, 2);
        $big = $parts[0];
        $rest = $parts[1] ?? '';
        $rest_w = $rest !== '' ? theme_og_text_width($mono400, 20, $rest) : 0;
        $big_w = theme_og_text_width($mono500, 64, $big, -0.05);
        $gap = $rest !== '' ? 12 : 0;
        $x = $right - $rest_w - $gap - $big_w;
        $stat_top = $content_bottom - 58;
        $baseline = $stat_top + (57.6 - 64 * $mono_content) / 2 + 64 * $mono_asc;
        theme_og_text($im, $mono500, 64, $x, $baseline, $big, $lime, -0.05);
        if ($rest !== '') {
            theme_og_text($im, $mono400, 20, $x + $big_w + $gap, $baseline, $rest, $lime);
        }
    } else {
        $bx = $right - 56;
        $by = $content_bottom - 56;
        imagefilledrectangle($im, $bx + 4, $by, $bx + 51, $by + 55, $lime);
        imagefilledrectangle($im, $bx, $by + 4, $bx + 55, $by + 51, $lime);
        foreach (array(array($bx + 4, $by + 4), array($bx + 51, $by + 4), array($bx + 4, $by + 51), array($bx + 51, $by + 51)) as $corner) {
            imagefilledellipse($im, $corner[0], $corner[1], 8, 8, $lime);
        }
        // arrow ↗
        $dark = $c(14, 15, 17);
        imagesetthickness($im, 3);
        imageline($im, $bx + 20, $by + 36, $bx + 36, $by + 20, $dark);
        imageline($im, $bx + 22, $by + 20, $bx + 36, $by + 20, $dark);
        imageline($im, $bx + 36, $by + 20, $bx + 36, $by + 34, $dark);
        imagesetthickness($im, 1);
    }

    return $im;
}

/**
 * Everything the template needs for one post.
 */
function theme_og_post_data($post_id)
{
    $lang = function_exists('pll_get_post_language') ? (string)pll_get_post_language($post_id) : 'ua';
    $category = theme_get_post_category($post_id);
    $og_title = function_exists('get_field') ? trim((string)get_field('og_title', $post_id)) : '';

    return array(
        'title' => $og_title !== '' ? $og_title : get_the_title($post_id),
        'category' => $category ? html_entity_decode($category->name, ENT_QUOTES, 'UTF-8') : '',
        'minutes' => theme_get_reading_time($post_id),
        'lang' => $lang === 'en' ? 'en' : 'ua',
        'stat' => function_exists('get_field') ? trim((string)get_field('og_stat', $post_id)) : '',
    );
}

function theme_og_file($post_id)
{
    $post = get_post($post_id);
    $lang = function_exists('pll_get_post_language') ? (string)pll_get_post_language($post_id) : 'ua';
    $upload = wp_upload_dir();

    return array(
        'path' => trailingslashit($upload['basedir']) . 'og-' . ($lang ?: 'ua') . '-' . $post->post_name . '.png',
        'url' => trailingslashit($upload['baseurl']) . 'og-' . ($lang ?: 'ua') . '-' . $post->post_name . '.png',
    );
}

/**
 * Renders and saves the image of a post. Stores _yt_og_error when it cannot be made.
 *
 * @return true|WP_Error
 */
function theme_og_generate($post_id)
{
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'post' || $post->post_status !== 'publish') {
        return new WP_Error('og_skip', 'not a published post');
    }

    $im = theme_og_render(theme_og_post_data($post_id));
    if (is_wp_error($im)) {
        update_post_meta($post_id, '_yt_og_error', $im->get_error_message());
        delete_post_meta($post_id, '_yt_og_image');

        return $im;
    }

    $file = theme_og_file($post_id);
    wp_mkdir_p(dirname($file['path']));
    $saved = imagepng($im, $file['path'], 6);
    imagedestroy($im);

    if (!$saved) {
        update_post_meta($post_id, '_yt_og_error', 'could not write ' . basename($file['path']));

        return new WP_Error('og_write', 'could not write the image');
    }

    delete_post_meta($post_id, '_yt_og_error');
    update_post_meta($post_id, '_yt_og_image', $file['url'] . '?v=' . filemtime($file['path']));

    return true;
}

// Generate after ACF has saved og_title / og_stat
add_action('acf/save_post', function ($post_id) {
    if (!is_numeric($post_id) || get_post_type($post_id) !== 'post' || wp_is_post_revision($post_id)) {
        return;
    }
    theme_og_generate((int)$post_id);
}, 30);

// Posts published without the ACF form (REST, quick edit): same image, from what is stored
add_action('transition_post_status', function ($new, $old, $post) {
    if ($post->post_type === 'post' && $new === 'publish' && $old !== 'publish' && !doing_action('acf/save_post')) {
        add_action('shutdown', function () use ($post) {
            theme_og_generate($post->ID);
        });
    }
}, 10, 3);

// Yoast: the generated image is added only when no other social image exists (a manual Yoast image or a featured
// image is already in the container by then and wins).
add_action('wpseo_add_opengraph_additional_images', function ($container) {
    if (!is_singular('post') || !is_object($container) || !method_exists($container, 'get_images') || $container->get_images()) {
        return;
    }

    $url = get_post_meta(get_queried_object_id(), '_yt_og_image', true);
    if ($url && method_exists($container, 'add_image')) {
        $container->add_image(array('url' => $url, 'width' => THEME_OG_W, 'height' => THEME_OG_H, 'type' => 'image/png'));
    }
});

add_filter('wpseo_twitter_image', function ($image) {
    if (!is_singular('post') || get_post_meta(get_queried_object_id(), '_yoast_wpseo_twitter-image', true) || get_post_meta(get_queried_object_id(), '_yoast_wpseo_opengraph-image', true)) {
        return $image;
    }

    $generated = get_post_meta(get_queried_object_id(), '_yt_og_image', true);

    return $generated ?: $image;
});

// Editor notice when the title is too long for the template
add_action('admin_notices', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'post') {
        return;
    }

    $error = get_post_meta(get_the_ID(), '_yt_og_error', true);
    if ($error) {
        printf('<div class="notice notice-warning"><p><strong>OG image was not generated:</strong> %s. Shorten the “OG title” field (up to %d characters).</p></div>', esc_html($error), THEME_OG_MAX_CHARS);
    }
});
