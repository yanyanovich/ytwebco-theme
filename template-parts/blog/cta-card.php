<?php
/**
 * In-grid CTA card ("// Need help"): two actions, so the card itself is not a link.
 */
$contacts = theme_get_contacts();
?>
<aside class="post-cta-card" data-sticky-hide>
    <p class="post-cta-card__label label"><span aria-hidden="true">//</span> <?php echo esc_html(theme_t('Need help?')); ?></p>
    <h2 class="post-cta-card__title"><?php echo esc_html(theme_t('Site is slow and you can’t see why?')); ?></h2>
    <p class="post-cta-card__text"><?php echo esc_html(theme_t('I’ll look at your site and give you a fixed price.')); ?></p>
    <div class="post-cta-card__actions">
        <a class="btn btn--primary" href="<?php echo esc_url(theme_get_page_url('bryf')); ?>" data-cta="grid-cta-brief">
            <?php echo esc_html(theme_t('Fill in the brief')); ?>
            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
        </a>
        <a class="link-ul post-cta-card__tg" href="<?php echo esc_url($contacts['telegram_url']); ?>" target="_blank" rel="noopener noreferrer" data-cta="grid-cta-telegram">
            Telegram <?php echo theme_icon('arrow-up-right', 14); ?>
        </a>
    </div>
</aside>
