<?php
/**
 * Mobile menu: full-screen sheet (Menu spec frame).
 * Numbered items, Services as an accordion, contacts, EN/UA switch;
 * status + CTA pinned to the bottom. Behaviour lives in setup-header.js.
 *
 * @var array $args {
 *     @type array  $menu         theme_get_menu_tree('header_menu')
 *     @type string $contact_url
 * }
 */
$menu = $args['menu'] ?? array();
$contact_url = $args['contact_url'] ?? '#';
$contacts = theme_get_contacts();
$step = 0; // stagger index for the opening animation
?>
<div id="mobile-menu" class="mob-menu bp" role="dialog" aria-modal="true"
     aria-label="<?php echo esc_attr(theme_t('Menu')); ?>" hidden data-mob-menu>
    <div class="mob-menu__head">
        <?php get_template_part('template-parts/partials/logo'); ?>
        <div class="mob-menu__head-actions">
            <span class="label" aria-hidden="true"><?php echo esc_html(theme_t('Menu')); ?></span>
            <button type="button" class="icon-btn icon-btn--close" aria-label="<?php echo esc_attr(theme_t('Close menu')); ?>"
                    aria-controls="mobile-menu" data-mob-menu-close>
                <?php echo theme_icon('close'); ?>
            </button>
        </div>
    </div>

    <div class="mob-menu__scroll">
        <?php if (!empty($menu)) : ?>
            <nav class="mob-menu__nav" aria-label="<?php echo esc_attr(theme_t('Main')); ?>">
                <ul class="mob-menu__list dim">
                    <?php foreach ($menu as $index => $item) : ?>
                        <li class="mob-menu__row" style="--i: <?php echo (int)$step++; ?>">
                            <?php if (!empty($item['children'])) :
                                $sub_id = 'mob-menu-sub-' . $item['id'];
                                ?>
                                <button type="button" class="mob-menu__item" aria-expanded="false"
                                        aria-controls="<?php echo esc_attr($sub_id); ?>" data-mob-menu-toggle>
                                    <span class="mob-menu__index"><?php echo esc_html(theme_menu_index($index)); ?></span>
                                    <span class="mob-menu__title">
                                        <?php if ($item['current']) : ?><span class="dot dot--sm" aria-hidden="true"></span><?php endif; ?>
                                        <?php echo esc_html($item['title']); ?>
                                    </span>
                                    <span class="icon-btn icon-btn--plus" aria-hidden="true"><?php echo theme_icon('plus'); ?></span>
                                </button>
                                <div class="mob-menu__sub" id="<?php echo esc_attr($sub_id); ?>" inert>
                                    <ul class="mob-menu__sub-list">
                                        <?php foreach ($item['children'] as $child_index => $child) : ?>
                                            <li>
                                                <a class="mob-menu__sub-link"<?php echo theme_menu_link_attrs($child); ?>>
                                                    <span class="mob-menu__sub-index"><?php echo esc_html(theme_menu_index($child_index)); ?></span>
                                                    <span class="mob-menu__sub-body">
                                                        <span class="mob-menu__sub-title"><?php echo esc_html($child['title']); ?></span>
                                                        <?php if ($child['description']) : ?>
                                                            <span class="mob-menu__sub-desc"><?php echo esc_html($child['description']); ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="arrow" aria-hidden="true">→</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else : ?>
                                <a class="mob-menu__item"<?php echo theme_menu_link_attrs($item); ?>>
                                    <span class="mob-menu__index"><?php echo esc_html(theme_menu_index($index)); ?></span>
                                    <span class="mob-menu__title">
                                        <?php if ($item['current']) : ?><span class="dot dot--sm" aria-hidden="true"></span><?php endif; ?>
                                        <?php echo esc_html($item['title']); ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <div class="mob-menu__extra" style="--i: <?php echo (int)$step++; ?>">
            <div class="mob-menu__contacts">
                <a class="mob-menu__contact" href="<?php echo esc_url($contacts['telegram_url']); ?>" data-track="telegram">
                    <span class="label">Telegram</span>
                    <span class="mono"><?php echo esc_html($contacts['telegram']); ?></span>
                </a>
                <a class="mob-menu__contact" href="<?php echo esc_url($contacts['email_url']); ?>" data-track="email">
                    <span class="label">Email</span>
                    <span class="mono"><?php echo esc_html($contacts['email']); ?></span>
                </a>
            </div>
            <div class="mob-menu__meta">
                <span class="mono mob-menu__note"><?php echo esc_html(theme_t('I reply within one working day.')); ?></span>
                <?php get_template_part('template-parts/partials/lang-switch', null, array('size' => 'sm')); ?>
            </div>
        </div>
    </div>

    <div class="mob-menu__bottom" style="--i: <?php echo (int)$step; ?>">
        <span class="status-pill"><span class="dot" aria-hidden="true"></span><?php echo esc_html(theme_t('Available for new projects')); ?></span>
        <a class="btn btn--primary btn--full" href="<?php echo esc_url($contact_url); ?>">
            <?php echo esc_html(theme_t('Start a project')); ?>
            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
        </a>
    </div>
</div>
