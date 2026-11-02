<?php
if (is_admin() && !empty($is_preview)) :
    get_block_preview(__DIR__);
    return;
else :
    $block_id = !empty($block['anchor']) ? $block['anchor'] : $block['id'];
    $block_classes = array(
        'brief',
        !empty($block['className']) ? $block['className'] : '',
    );

    $steps = array_values(array_filter((get_field('b_other_brief_steps') ?: array()), function ($step) {
        return !empty($step['questions']);
    }));
    if (empty($steps)) {
        return;
    }

    $total = count($steps);
    $progress = get_field('b_other_brief_progress') ?: '%1$s / %2$s';
    $aside_steps = (get_field('b_other_brief_aside_steps') ?: array());
    $aside_note = get_field('b_other_brief_aside_note');
    $privacy = get_field('b_other_brief_privacy');
    $contacts = theme_get_contacts();
    $language = function_exists('pll_current_language') ? pll_current_language('locale') : get_locale();
    ?>
    <section id="<?php echo esc_attr($block_id); ?>"
             class="<?php echo esc_attr(trim(implode(' ', array_filter($block_classes)))); ?>"
             data-section>
        <div class="container grid-12">
            <aside class="brief__aside" data-aos="drift-up">
                <?php if ($aside_title = get_field('b_other_brief_aside_title')) : ?>
                    <h2 class="label brief__aside-title"><?php echo esc_html($aside_title); ?></h2>
                <?php endif; ?>
                <?php if (!empty($aside_steps)) : ?>
                    <ol class="brief__next">
                        <?php foreach ($aside_steps as $index => $step) : ?>
                            <li class="brief__next-item">
                                <span class="brief__next-index mono" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                <span class="brief__next-text"><?php echo esc_html($step['text']); ?></span>
                                <?php if (!empty($step['chip'])) : ?>
                                    <span class="chip brief__next-chip"><?php echo esc_html($step['chip']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
                <?php if (!empty($aside_note)) : ?>
                    <p class="brief__note mono"><?php echo esc_html($aside_note); ?></p>
                <?php endif; ?>
            </aside>

            <div class="contact-form brief-form brief__form"
                 data-brief
                 data-endpoint="<?php echo esc_url(rest_url('tokaruk-leads/v1/brief')); ?>"
                 data-nonce="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>"
                 data-language="<?php echo esc_attr(strtolower(substr($language, 0, 2))); ?>"
                 data-progress="<?php echo esc_attr($progress); ?>"
                 data-sending="<?php echo esc_attr(get_field('b_other_brief_sending')); ?>"
                 data-error-required="<?php echo esc_attr(get_field('b_other_brief_error_required')); ?>"
                 data-error-email="<?php echo esc_attr(get_field('b_other_brief_error_email')); ?>"
                 data-aos="soft-scale" data-aos-delay="100">
                <form class="contact-form__panel brief-form__panel" novalidate>
                    <div class="brief-form__progress">
                        <p class="brief-form__count mono">
                            <span data-brief-count aria-live="polite"><?php echo esc_html(sprintf($progress, 1, $total)); ?></span>
                            <span class="brief-form__current" data-brief-current aria-hidden="true"><?php echo esc_html($steps[0]['name']); ?></span>
                        </p>
                        <span class="brief-form__bar" role="progressbar" aria-valuemin="1" aria-valuemax="<?php echo esc_attr($total); ?>" aria-valuenow="1" aria-valuetext="<?php echo esc_attr(sprintf($progress, 1, $total) . ' · ' . $steps[0]['name']); ?>" data-brief-bar>
                            <?php for ($i = 0; $i < $total; $i++) : ?>
                                <span class="brief-form__segment<?php echo $i === 0 ? ' is-done' : ''; ?>"></span>
                            <?php endfor; ?>
                        </span>
                        <ol class="brief-form__names mono" aria-hidden="true">
                            <?php foreach ($steps as $index => $step) : ?>
                                <li class="brief-form__name<?php echo $index === 0 ? ' is-current' : ''; ?>" data-brief-name>
                                    <?php echo esc_html(sprintf('%02d %s', $index + 1, $step['name'])); ?><span class="brief-form__check"> ✓</span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>

                    <?php // Honeypot: real visitors never see or fill it ?>
                    <div class="brief-form__trap" aria-hidden="true">
                        <label>Website <input type="text" name="tl_website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <?php foreach ($steps as $index => $step) : ?>
                        <fieldset class="brief-form__step" data-brief-step data-name="<?php echo esc_attr($step['name']); ?>"<?php echo $index ? ' hidden' : ''; ?>>
                            <legend class="brief-form__legend">
                                <span class="brief-form__title" tabindex="-1"><?php echo esc_html($step['title']); ?></span>
                            </legend>

                            <?php foreach ((array)$step['questions'] as $q_index => $question) :
                                $type = $question['type'] ?: 'text';
                                $id = $block_id . '-' . $index . '-' . $q_index;
                                $label = $question['label'] . (!empty($question['hint']) ? ' · ' . $question['hint'] : '');
                                $required = !empty($question['required']);
                                $data = sprintf(' data-key="%s" data-label="%s"%s', esc_attr($question['key']), esc_attr($question['label']), $required ? ' data-required' : '');
                                ?>
                                <?php if ($type === 'single' || $type === 'multi') :
                                    $options = array_filter(array_map('trim', preg_split('/\R/u', (string)$question['options'])));
                                    ?>
                                    <div class="brief-form__group" role="group" aria-labelledby="<?php echo esc_attr($id); ?>" data-brief-chips="<?php echo esc_attr($type); ?>"<?php echo $data; ?>>
                                        <span class="field__label" id="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></span>
                                        <div class="brief-form__picks">
                                            <?php foreach ($options as $option) : ?>
                                                <button type="button" class="pick" aria-pressed="false" value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else :
                                    $input_type = array('email' => 'email', 'tel' => 'text', 'url' => 'url')[$type] ?? 'text';
                                    $autocomplete = array('name' => 'name', 'email' => 'email', 'company' => 'organization')[$question['key']] ?? ($type === 'url' ? 'url' : 'off');
                                    ?>
                                    <div class="field brief-form__field">
                                        <label class="field__label" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
                                        <?php if ($type === 'textarea') : ?>
                                            <textarea class="field__input" id="<?php echo esc_attr($id); ?>" rows="4" placeholder="<?php echo esc_attr($question['placeholder']); ?>" aria-describedby="<?php echo esc_attr($id); ?>-error"<?php echo $data; ?>></textarea>
                                        <?php else : ?>
                                            <input class="field__input" id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr($input_type); ?>" autocomplete="<?php echo esc_attr($autocomplete); ?>" placeholder="<?php echo esc_attr($question['placeholder']); ?>" aria-describedby="<?php echo esc_attr($id); ?>-error"<?php echo $required ? ' aria-required="true"' : ''; ?><?php echo $data; ?>>
                                        <?php endif; ?>
                                        <span class="field__error" id="<?php echo esc_attr($id); ?>-error"></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endforeach; ?>

                    <div class="contact-form__error brief-form__error" data-form-error role="alert" hidden>
                        <span>
                            <?php echo esc_html(get_field('b_other_brief_error_send')); ?>
                            <?php if (!empty($contacts['telegram_url']) && ($error_link = get_field('b_other_brief_error_link'))) : ?>
                                <a href="<?php echo esc_url($contacts['telegram_url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($error_link); ?></a>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="brief-form__nav">
                        <button type="button" class="btn btn--secondary brief-form__back" data-brief-back disabled>
                            <span aria-hidden="true">←</span> <?php echo esc_html(get_field('b_other_brief_back')); ?>
                        </button>
                        <button type="button" class="btn btn--primary brief-form__next" data-brief-next>
                            <?php echo esc_html(get_field('b_other_brief_next')); ?>
                            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                        </button>
                        <button type="submit" class="btn btn--primary brief-form__next" data-brief-send hidden>
                            <span class="btn__text"><?php echo esc_html(get_field('b_other_brief_send')); ?></span>
                            <span class="arrow"><?php echo theme_icon('arrow-right'); ?></span>
                        </button>
                    </div>

                    <?php if (!empty($privacy)) : ?>
                        <p class="contact-form__privacy brief-form__privacy mono"><?php echo esc_html($privacy); ?></p>
                    <?php endif; ?>
                </form>

                <?php get_template_part('template-parts/partials/form-success', null, array(
                    'badge' => get_field('b_other_brief_success_badge'),
                    'title' => get_field('b_other_brief_success_title'),
                    'steps' => (get_field('b_other_brief_success_steps') ?: array()),
                    'link_primary' => (array)get_field('b_other_brief_success_link_primary'),
                    'link_secondary' => (array)get_field('b_other_brief_success_link_secondary'),
                    'segments' => $total,
                )); ?>
            </div>
        </div>
    </section>
<?php
endif;
