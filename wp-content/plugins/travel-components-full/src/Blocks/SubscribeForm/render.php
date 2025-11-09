<?php
if (!defined('ABSPATH')) exit;

$bg_color    = get_field('bg_color') ?: '#007070';
$title       = get_field('title') ?: 'Enjoying this blog?';
$subtitle    = get_field('subtitle') ?: 'Subscribe to receive all the news and discounts before anyone else!';
$button_text = get_field('button_text') ?: 'Subscribe';
$endpoint    = get_field('endpoint') ?: '';
$base_uri    = get_field('base_uri') ?: '';

$recipients = [];
if (have_rows('recipients')) {
  while (have_rows('recipients')) {
    the_row();
    $em = get_sub_field('recipient_email');
    if ($em && is_email($em)) $recipients[] = $em;
  }
}
$recipients_csv = implode(',', $recipients);
?>

<div class="tc-subscribe" style="background-color:<?php echo esc_attr($bg_color); ?>">
  <h3 class="tc-sub-title"><?php echo esc_html($title); ?></h3>
  <p class="tc-sub-desc"><?php echo esc_html($subtitle); ?></p>

  <form class="tc-subscribe-form"
        data-recipients="<?php echo esc_attr($recipients_csv); ?>"
        data-endpoint="<?php echo esc_attr($endpoint); ?>"
        data-baseuri="<?php echo esc_attr($base_uri); ?>">

    <input type="hidden" name="endpoint" value="<?php echo esc_attr($endpoint); ?>" />
    <input type="hidden" name="base_uri" value="<?php echo esc_attr($base_uri); ?>" />

    <input type="text" name="name" placeholder="Name" required />
    <input type="email" name="email" placeholder="E-mail" required />

    <label class="tc-sub-privacy">
      <input type="checkbox" required />
      <span>I have read and accept the privacy policy</span>
    </label>

    <button type="submit" class="tc-sub-btn"><?php echo esc_html($button_text); ?></button>
  </form>

  <p class="tc-subscribe-msg" style="display:none;margin-top:8px;"></p>
</div>
