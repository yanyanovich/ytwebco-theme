<?php
/**
 * Sticky CTA bar for phones (Sticky CTA frames, UA/EN, 390 + 360).
 * Site bar: Telegram + "Start a project". Article bar (posts with a TOC): "Contents 04 / 12" + Telegram + "Let's talk",
 * reading progress along the top edge, the TOC opens as a bottom sheet.
 * Hidden by default (visibility:hidden + inert); setup-sticky-cta.js shows it by the rules in the brief.
 */
if (!theme_sticky_cta_enabled()) {
    return;
}

$toc = (is_singular('post') && !empty($GLOBALS['theme_article_toc'])) ? $GLOBALS['theme_article_toc'] : array();
$article = !empty($toc);
$total = count($toc);
$contacts = theme_get_contacts();
$brief_url = theme_get_page_url('bryf');
?>
<div class="sticky-cta<?php echo $article ? ' sticky-cta--article' : ''; ?>" data-sticky-cta data-mode="<?php echo $article ? 'article' : 'site'; ?>"
     role="region" aria-label="<?php echo esc_attr(theme_t('Quick actions')); ?>" aria-hidden="true" inert>
    <?php if ($article) : ?>
        <div class="sticky-cta__progress" aria-hidden="true"><span data-sticky-progress></span></div>
        <button type="button" class="sticky-cta__toc" data-cta="sticky-toc" aria-haspopup="dialog" aria-controls="toc-sheet" data-toc-open>
            <?php echo theme_icon('burger', 16); ?>
            <span><?php echo esc_html(theme_t('Contents')); ?></span>
            <span class="sticky-cta__cur mono" data-toc-current>01</span>
            <span class="sticky-cta__total mono">/ <?php echo esc_html(sprintf('%02d', $total)); ?></span>
        </button>
    <?php endif; ?>
    <a class="sticky-cta__tg" href="<?php echo esc_url($contacts['telegram_url']); ?>" target="_blank" rel="noopener noreferrer"
       aria-label="<?php echo esc_attr(theme_t('Message on Telegram')); ?>" data-cta="sticky-telegram">
        <?php echo theme_icon('telegram', 18); ?>
    </a>
    <a class="sticky-cta__brief" href="<?php echo esc_url($brief_url); ?>" data-cta="sticky-brief">
        <?php echo esc_html($article ? theme_t('Let’s talk') : theme_t('Start a project')); ?>
        <span aria-hidden="true"><?php echo theme_icon('arrow-right', 16); ?></span>
    </a>
</div>

<?php if ($article) : ?>
    <div class="toc-sheet" id="toc-sheet" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr(theme_t('Contents')); ?>" hidden data-toc-sheet>
        <div class="toc-sheet__backdrop" data-toc-close></div>
        <div class="toc-sheet__panel">
            <div class="toc-sheet__head">
                <span class="toc-sheet__title"><?php echo esc_html(theme_t('Contents')); ?>
                    <span class="toc-sheet__count mono"><span data-toc-current>01</span> <span>/ <?php echo esc_html(sprintf('%02d', $total)); ?></span></span>
                </span>
                <button type="button" class="icon-btn toc-sheet__close" data-toc-close aria-label="<?php echo esc_attr(theme_t('Close contents')); ?>"><?php echo theme_icon('close', 16); ?></button>
            </div>
            <nav class="toc-sheet__nav" aria-label="<?php echo esc_attr(theme_t('Contents')); ?>">
                <?php foreach ($toc as $index => $item) : ?>
                    <a class="toc-sheet__link" href="#<?php echo esc_attr($item['id']); ?>" data-sheet-link="<?php echo esc_attr($index); ?>">
                        <span class="toc-sheet__num mono"><?php echo esc_html($item['num']); ?></span>
                        <span><?php echo esc_html($item['title']); ?></span>
                        <span class="toc-sheet__dot" aria-hidden="true"></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>
<?php endif; ?>
