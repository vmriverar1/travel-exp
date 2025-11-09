<?php
$rows   = get_field('layout_rows') ?: '1';
$slides = get_field('slides') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
?>

<section class="travel-swiper-block <?php echo esc_attr($rows_class); ?>">
  <div class="tsb-swiper swiper">
    <div class="swiper-wrapper">
      <?php if ($slides): ?>
        <?php foreach ($slides as $s): ?>
          <div class="swiper-slide">
            <article class="tsb-card">
              <?php if (!empty($s['image'])) echo wp_get_attachment_image($s['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
              <?php if (!empty($s['caption'])): ?>
                <p class="tsb-caption"><?php echo esc_html($s['caption']); ?></p>
              <?php endif; ?>
            </article>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="swiper-slide">
          <div class="tsb-card">
            <p>Demo Team</p>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="swiper-controls">
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination__mobile"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>
</section>