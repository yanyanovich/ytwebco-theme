<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('acf/blocks/default_block_version', fn() => 3);

add_filter('acf/settings/save_json', 'theme_acf_json_save_point');
function theme_acf_json_save_point($path)
{
    $acf_json_dir = get_template_directory() . '/acf-json';

    if (!is_dir($acf_json_dir)) {
        mkdir($acf_json_dir, 0755, true);
    }

    return $acf_json_dir;
}

add_action('acf/init', 'theme_register_acf_options_pages');
function theme_register_acf_options_pages()
{
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    $options_pages = array(
        array(
            'page_title' => __('Theme Settings', 'ytweb_theme'),
            'menu_title' => __('Theme Settings', 'ytweb_theme'),
            'menu_slug' => 'theme-settings',
            'icon_url' => 'dashicons-admin-generic',
            'redirect' => false,
            'post_id' => 'theme-settings',
            'load_json' => get_template_directory() . '/acf-json/group_66f668a53a340.json',
            'capability' => 'manage_options',
        ),
        array(
            'page_title' => __('Scripts/metrics', 'ytweb_theme'),
            'menu_title' => __('Scripts/metrics', 'ytweb_theme'),
            'menu_slug' => 'scripts-metrics',
            'icon_url' => 'dashicons-media-code',
            'redirect' => false,
            'post_id' => 'scripts-metrics',
            'load_json' => get_template_directory() . '/acf-json/group_6780f26a86595.json',
            'capability' => 'manage_options',
        ),
    );

    /*if (function_exists('wpcf7_contact_form')) {
        $options_pages[] = array(
            'page_title' => __('Default forms', 'ytweb_theme'),
            'menu_title' => __('Default forms', 'ytweb_theme'),
            'menu_slug' => 'default-contact-forms',
            'icon_url' => 'dashicons-forms',
            'redirect' => false,
            'post_id' => 'default-contact-forms',
            'load_json' => get_template_directory() . '/acf-json/group_67813b70bfb06.json',
            'capability' => 'manage_options',
        );
    }*/

    foreach ($options_pages as $options_page) {
        acf_add_options_page($options_page);
    }
}

//add nofollow options to link
//use $nofollow = isset($link['nofollow']) && $link['nofollow'] ? ' rel="nofollow"' : ''; to get value
function theme_add_nofollow_option_to_link_field($field)
{
    if ($field['type'] !== 'link') {
        return;
    }

    $nofollow = isset($field['value']['nofollow']) ? $field['value']['nofollow'] : 0;
    ?>
    <ul class="acf-hl acf-link-target">
        <li class="acf-rel">
            <label>
                <input class="nofollow-checkbox" type="checkbox" name="<?php echo esc_attr($field['name']); ?>[nofollow]" value="1" <?php checked($nofollow, 1); ?>>
                <?php esc_html_e('Add nofollow', 'ytweb_theme'); ?>
            </label>
        </li>
    </ul>
    <?php
}
add_action('acf/render_field', 'theme_add_nofollow_option_to_link_field');

function theme_add_inline_acf_admin_script()
{
    ?>
    <script type="text/javascript">
        (function ($) {
            $(document).on('change', '.nofollow-checkbox', function () {
                let currentCheckbox = $(this);
                let name = currentCheckbox.attr('name');
                if (currentCheckbox.prop('checked')) {
                    $('input[name="' + name + '"]').val(1);
                } else {
                    $('input[name="' + name + '"]').val(0);
                }

            });
        })(jQuery);
    </script>
    <?php
}
add_action('acf/input/admin_footer', 'theme_add_inline_acf_admin_script');

function get_block_preview($dir)
{
    $base_dirname = "template-parts";

    $base_pos = strpos($dir, $base_dirname);

    if ($base_pos === false || !file_exists($dir . '/preview.png')) {
        return;
    }

    $auto_path = substr($dir, $base_pos);
    $preview_url = get_template_directory_uri() . '/' . str_replace('\\', '/', $auto_path) . '/preview.png';

    echo '<img src="' . esc_url($preview_url) . '" alt="" style="max-width: 470px">';
}

function get_acf_link_attrs(array $link, string $class = ''): string
{
    if (empty($link['url'])) {
        return '';
    }

    $target = !empty($link['target']) ? $link['target'] : '_self';
    $rel = $target === '_blank' ? 'noopener noreferrer' : '';

    if (!empty($link['nofollow'])) {
        $rel = trim($rel . ' nofollow');
    }

    $link_attrs = array(
            'href' => $link['url'],
            'class' => $class,
            'title' => $link['title'] ?? '',
            'target' => $target,
            'rel' => $rel,
    );

    $link_attrs_html = '';

    foreach ($link_attrs as $attr => $value) {
        if ($value === '') {
            continue;
        }

        $value = $attr === 'href' ? esc_url($value) : esc_attr($value);
        $link_attrs_html .= ' ' . $attr . '="' . $value . '"';
    }

    return $link_attrs_html;
}

function render_acf_link($link, $class = '', $custom_title = '')
{
    if (empty($link) || empty($link['url'])) {
        return;
    }

    $link_atts = get_acf_link_attrs($link, $class);
    $link_title = !empty($link['title']) ? $link['title'] : $link['url'];
    $title = !empty($custom_title) ? $custom_title : $link_title;
    ?>
    <a<?php echo $link_atts; ?>>
        <?php echo wp_kses_post($title); ?>
    </a>
    <?php
}

function theme_order_acf_post_type($query)
{
    $post_types = array('acf-field-group');
    $post_type = $query->get('post_type');

    if ($query->is_main_query() && in_array($post_type, $post_types, true) && !isset($_GET['orderby'])) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }

    return $query;
}
add_filter('pre_get_posts', 'theme_order_acf_post_type');