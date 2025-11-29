<?php
if (!defined('ABSPATH')) exit;

$items = get_field('breadcrumb_items');
$color = get_field('arrow_color') ?: '#E78C85';
$font_size_desk = get_field('font_size_desktop') ?: '16';
$font_size_mob = get_field('font_size_mobile') ?: '14';
$strikethrough = get_field('strikethrough') ? 'text-decoration: line-through;' : '';
?>

<?php if ($items): ?>
  <nav class="tc-breadcrumb" style="--arrow-color:<?php echo esc_attr($color); ?>;--font-desk:<?php echo esc_attr($font_size_desk); ?>px;--font-mob:<?php echo esc_attr($font_size_mob); ?>px;<?php echo esc_attr($strikethrough); ?>">
    <?php foreach ($items as $index => $item):
      $text = $item['text'] ?? '';
      $url  = $item['link'] ?? '';
      if (!$text) continue;
    ?>
      <span class="tc-breadcrumb__item">
        <?php if ($url): ?>
          <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($text); ?></a>
        <?php else: ?>
          <?php echo esc_html($text); ?>
        <?php endif; ?>
      </span>

      <?php if ($index < count($items) - 1): ?>
        <span class="tc-breadcrumb__arrow">
          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="var(--arrow-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 4 14 10 6 16"></polyline>
          </svg>
        </span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>
<?php endif; ?>
