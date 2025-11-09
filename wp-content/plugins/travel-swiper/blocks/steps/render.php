<?php
$rows   = get_field('layout_rows') ?: '3';
$steps  = get_field('steps') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
?>

<section class="travel-swiper-block travel-swiper--steps <?php echo esc_attr($rows_class); ?>">
  <div class="tsb-swiper swiper">
    <div class="swiper-wrapper">
      <?php if ($steps): ?>
        <?php foreach ($steps as $s): ?>
          <div class="swiper-slide">
            <article class="step-card">
              <?php if (!empty($s['icon'])): ?>
                <div class="step-icon">
                  <?php echo wp_get_attachment_image($s['icon'], 'medium', false, ['loading' => 'lazy']); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($s['title'])): ?>
                <h3 class="step-title"><?php echo esc_html($s['title']); ?></h3>
              <?php endif; ?>

              <?php if (!empty($s['description'])): ?>
                <div class="step-desc"><?php echo wp_kses_post($s['description']); ?></div>
              <?php endif; ?>

              <?php if (!empty($s['image'])): ?>
                <div class="step-image">
                  <?php echo wp_get_attachment_image($s['image'], 'large', false, ['loading' => 'lazy']); ?>

                  <?php if (!empty($s['cta']) && !empty($s['link'])): ?>
                    <a href="<?php echo esc_url($s['link']); ?>" class="step-button">
                      <?php echo esc_html($s['cta']); ?>
                    </a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </article>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="swiper-slide">
            <article class="step-card">
              <div class="step-icon"><img src="https://via.placeholder.com/64" alt="Icon demo"></div>
              <h3 class="step-title">Step Title <?php echo $i; ?></h3>
              <div class="step-desc"><p>Sample description for step <?php echo $i; ?>.</p></div>
              <div class="step-image">
                <img src="https://via.placeholder.com/400x250" alt="Step Image">
                <a href="#" class="step-button">Learn more</a>
              </div>
            </article>
          </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>

    <div class="swiper-controls">
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination__mobile"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>
</section>
