<?php
/**
 * Contact rows (homepage contact block, Contact page): icon, title, note, arrow when it is a link.
 * The type picks the icon and, when the row leaves them empty, the URL / note from Theme settings → Contacts.
 *
 * @var array $args {
 *     @type array $rows list of array{type: string, title: string, note?: string, link?: array}
 * }
 */
$rows = array_values((array)($args['rows'] ?? array()));

if (empty($rows)) {
    return;
}

$contacts = theme_get_contacts();
$types = array(
    'telegram' => array('icon' => 'telegram', 'url' => $contacts['telegram_url'], 'note' => $contacts['telegram'], 'external' => true),
    'booking' => array('icon' => 'calendar', 'url' => $contacts['booking_url'], 'note' => '', 'external' => true),
    'email' => array('icon' => 'email', 'url' => $contacts['email_url'], 'note' => $contacts['email'], 'external' => false),
    'phone' => array('icon' => 'phone', 'url' => $contacts['phone_url'], 'note' => $contacts['phone'], 'external' => false),
    'location' => array('icon' => 'location', 'url' => '', 'note' => '', 'external' => false),
);
?>
<ul class="contact-rows">
    <?php foreach ($rows as $row) :
        $type = $types[$row['type'] ?? ''] ?? $types['location'];
        $note = !empty($row['note']) ? $row['note'] : $type['note'];
        $link = !empty($row['link']['url']) ? $row['link'] : array();
        if (empty($link) && !empty($type['url']) && $type['url'] !== '#') {
            $link = array('url' => $type['url'], 'title' => '', 'target' => $type['external'] ? '_blank' : '');
        }
        $tag = !empty($link) ? 'a' : 'div';
        ?>
        <li>
            <<?php echo $tag; ?> class="contact-rows__row"<?php echo !empty($link) ? get_acf_link_attrs($link) : ''; ?>>
                <span class="contact-rows__icon" aria-hidden="true"><?php echo theme_icon($type['icon'], 20); ?></span>
                <span class="contact-rows__body">
                    <span class="contact-rows__title"><?php echo esc_html($row['title']); ?></span>
                    <?php if (!empty($note)) : ?>
                        <span class="contact-rows__note mono"><?php echo esc_html($note); ?></span>
                    <?php endif; ?>
                </span>
                <?php if (!empty($link)) : ?>
                    <span class="contact-rows__arrow arrow" aria-hidden="true"><?php echo theme_icon('arrow-right'); ?></span>
                <?php endif; ?>
            </<?php echo $tag; ?>>
        </li>
    <?php endforeach; ?>
</ul>
