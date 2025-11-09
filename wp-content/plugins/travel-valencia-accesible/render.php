<?php
$image = get_field('background_image');
if (!$image) return;

$img_alt = get_field('background_alt') ?: $image['alt'] ?: '';
$overlay_color = get_field('overlay_color') ?: '#2A2A2A';
$text_color = get_field('text_color') ?: '#FFFFFF';
$tint_opacity = get_field('tint_opacity') ?: 50;
$icons = get_field('icons');
?>
<section class="vtb-banner" style="--overlay-color: <?php echo esc_attr($overlay_color); ?>; --text-color: <?php echo esc_attr($text_color); ?>; --tint-opacity: <?php echo esc_attr($tint_opacity); ?>%;">
  <figure class="vtb-figure">
    <img class="vtb-bg" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
  </figure>

  <?php if (have_rows('icons')): ?>
    <div class="vtb-overlay">
      <ul class="vtb-icons" role="list">
        <?php $i = 0;
        while (have_rows('icons')): the_row();
          $i++; ?>
          <?php
          $icon = get_sub_field('icon');
          $title = get_sub_field('title');
          $desc = get_sub_field('description');
          $show_icon = get_sub_field('show_icon');
          $show_title = get_sub_field('show_title');
          $show_desc = get_sub_field('show_desc');
          ?>
          <li class="vtb-item <?php echo $i === 3 ? 'vtb-item--center' : ''; ?>">
            <?php if ($show_icon && $icon): ?>
              <img class="vtb-icon" src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($title ?: ''); ?>" loading="lazy">
            <?php endif; ?>

            <?php if ($show_title && $title): ?>
              <h3 class="vtb-title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>

            <?php if ($show_desc && $desc): ?>
              <div class="vtb-desc"><?php echo apply_filters('the_content', $desc); ?></div>
            <?php endif; ?>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  <?php endif; ?>
</section>