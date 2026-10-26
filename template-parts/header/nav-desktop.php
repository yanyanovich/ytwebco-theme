<?php
/**
 * Desktop navigation. An item with children renders the Services dropdown:
 * numbered sub-items with the menu item description as subtitle, plus an
 * "All services" row that links to the parent item URL.
 *
 * @var array $args {
 *     @type array $menu  theme_get_menu_tree('header_menu')
 * }
 */
$menu = $args['menu'] ?? array();

if (empty($menu)) {
    return;
}
?>
<nav class="header__nav" aria-label="<?php echo esc_attr(theme_t('Main')); ?>">
    <ul class="header__menu dim">
        <?php foreach ($menu as $item) : ?>
            <?php if (!empty($item['children'])) :
                $dropdown_id = 'header-dropdown-' . $item['id'];
                ?>
                <li class="header__item has-dropdown<?php echo $item['current'] ? ' is-current' : ''; ?>" data-dropdown>
                    <a class="header__link" href="<?php echo esc_url($item['url']); ?>"
                       aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr($dropdown_id); ?>">
                        <?php if ($item['current']) : ?><span class="dot dot--sm" aria-hidden="true"></span><?php endif; ?>
                        <?php echo esc_html($item['title']); ?>
                        <span class="header__chevron"><?php echo theme_icon('chevron-down', 10); ?></span>
                    </a>
                    <div class="header__dropdown" id="<?php echo esc_attr($dropdown_id); ?>">
                        <ul class="header__dropdown-list">
                            <?php foreach ($item['children'] as $index => $child) : ?>
                                <li>
                                    <a class="header__dropdown-link"<?php echo theme_menu_link_attrs($child); ?>>
                                        <span class="header__dropdown-index"><?php echo esc_html(theme_menu_index($index)); ?></span>
                                        <span class="header__dropdown-body">
                                            <span class="header__dropdown-title"><?php echo esc_html($child['title']); ?></span>
                                            <?php if ($child['description']) : ?>
                                                <span class="header__dropdown-desc"><?php echo esc_html($child['description']); ?></span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="header__dropdown-arrow arrow" aria-hidden="true">→</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li>
                                <a class="header__dropdown-link header__dropdown-link--all" href="<?php echo esc_url($item['url']); ?>">
                                    <span class="header__dropdown-index" aria-hidden="true">↳</span>
                                    <span class="header__dropdown-all"><?php echo esc_html(theme_t('All services')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php else : ?>
                <li class="header__item<?php echo $item['current'] ? ' is-current' : ''; ?>">
                    <a class="header__link"<?php echo theme_menu_link_attrs($item); ?>>
                        <?php if ($item['current']) : ?><span class="dot dot--sm" aria-hidden="true"></span><?php endif; ?>
                        <?php echo esc_html($item['title']); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
