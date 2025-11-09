<?php
$rows   = get_field('layout_rows') ?: '1';
$slides = get_field('slides') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_'); // 
?>
<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block <?php echo esc_attr($rows_class); ?> travel-icons-block">
  <div class="tsb-swiper swiper">
    <div class="swiper-wrapper">
      <?php if ($slides): ?>
        <?php foreach ($slides as $s): ?>
          <div class="swiper-slide">
            <article class="icon-card">
              <?php if (!empty($s['icon'])): ?>
                <div class="icon-img">
                  <?php echo wp_get_attachment_image($s['icon'], 'medium', false, ['loading' => 'lazy']); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($s['title'])): ?>
                <h4 class="icon-title"><?php echo esc_html($s['title']); ?></h4>
              <?php endif; ?>

              <?php if (!empty($s['description'])): ?>
                <div class="icon-desc"><?php echo wp_kses_post($s['description']); ?></div>
              <?php endif; ?>
            </article>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for ($i = 0; $i < 8; $i++): ?>
          <div class="swiper-slide">
            <article class="icon-card">
              <div class="icon-img">
                <img src="https://via.placeholder.com/80" alt="Demo Icon">
              </div>
              <h4 class="icon-title">Local, authentic, yours</h4>
              <div class="icon-desc">
                <p>We are a company born in Cusco, operated by local families who know every corner and every story.</p>
              </div>
            </article>
          </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>

    <!-- Controles dentro del swiper (flechas + dots) -->
    <div class="swiper-controls">
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination__mobile"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>
</section>
