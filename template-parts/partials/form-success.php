<?php
/**
 * In-place success panel of a form (Design system · 15 Form states): contact form and project brief.
 *
 * @var array $args array{badge: string, title: string, steps: array, link_primary: array, link_secondary: array, segments?: int, note?: string, note_link?: array}
 *                   segments — the brief shows its progress bar complete above the badge
 *                   note + note_link — line under the buttons (Contact page: link to the brief)
 */
$steps = array_values((array)($args['steps'] ?? array()));
$link_primary = (array)($args['link_primary'] ?? array());
$link_secondary = (array)($args['link_secondary'] ?? array());
?>
<?php if (!empty($args['title'])) : ?>
    <div class="contact-form__ok" role="status" aria-live="polite">
        <?php if (!empty($args['segments'])) : ?>
            <span class="brief-form__bar brief-form__bar--done" aria-hidden="true">
                <?php echo str_repeat('<span class="brief-form__segment is-done"></span>', (int)$args['segments']); ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($args['badge'])) : ?>
            <span class="contact-form__badge mono">
                <span class="dot" aria-hidden="true"></span>
                <?php echo esc_html($args['badge']); ?>
            </span>
        <?php endif; ?>
        <h3 class="contact-form__ok-title" tabindex="-1"><?php echo esc_html($args['title']); ?></h3>
        <?php if (!empty($steps)) : ?>
            <ol class="contact-form__steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <li class="contact-form__step">
                        <span class="contact-form__step-index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        <span class="contact-form__step-text"><?php echo esc_html($step['text']); ?></span>
                        <?php if (!empty($step['chip'])) : ?>
                            <span class="chip"><?php echo esc_html($step['chip']); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
        <?php if (!empty($link_primary['url']) || !empty($link_secondary['url'])) : ?>
            <div class="contact-form__actions">
                <?php if (!empty($link_primary['url'])) : ?>
                    <a<?php echo get_acf_link_attrs($link_primary, 'btn btn--primary'); ?>>
                        <?php echo esc_html($link_primary['title'] ?: $link_primary['url']); ?>
                        <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                    </a>
                <?php endif; ?>
                <?php render_acf_link($link_secondary, 'btn btn--secondary'); ?>
            </div>
        <?php endif; ?>
        <?php get_template_part('template-parts/partials/form-note', null, array('text' => $args['note'] ?? '', 'link' => $args['note_link'] ?? array())); ?>
    </div>
<?php endif; ?>
