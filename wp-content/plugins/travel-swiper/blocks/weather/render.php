<?php
/**
 * Render Template: Travel Weather (Images)
 * Muestra clima mensual con imagen de fondo y parallax
 */

$rows   = get_field('layout_rows') ?: '1';
$months = get_field('month_images') ?: [];
$bg_image = get_field('background_image');
$rows_class = 'swiper-rows-' . intval($rows);
$slides_mobile = get_field('slides_per_view_mobile') ?: '1';

$uniq_id = uniqid('tsb_weather_');
?>

<section id="<?php echo esc_attr($uniq_id); ?>"
         class="travel-swiper-block travel-weather-block <?php echo esc_attr($rows_class); ?>"
         data-slides-mobile="<?php echo esc_attr($slides_mobile); ?>">
  <?php if ($bg_image): ?>
    <div class="weather-bg" data-swiper-parallax-x="-40%" style="background-image:url('<?php echo esc_url(wp_get_attachment_image_url($bg_image, 'full')); ?>');"></div>
  <?php endif; ?>

  <div class="weather-content">
    <!-- === COLUMNA IZQUIERDA (LUGARES - DESKTOP) === -->
    <div class="weather-locations desktop-only">
      <span>CUSCO</span>
      <span>INCA TRAIL</span>
      <span>SALKANTAY</span>
      <span>MACHU PICCHU</span>
    </div>

    <!-- === GRID DESKTOP === -->
    <div class="weather-desktop-grid">
      <?php foreach ($months as $m):
        $img = !empty($m['image']) ? wp_get_attachment_image_url($m['image'], 'large') : '';
        if (!$img) continue;
      ?>
        <div class="weather-grid-item">
          <img src="<?php echo esc_url($img); ?>" alt="Weather Month" class="weather-month-image" />
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- === MOBILE === -->
  <div class="weather-mobile-wrapper mobile-only">
    <div class="weather-mobile-row">
      <!-- LUGARES (MOBILE) -->
      <div class="weather-locations-mobile">
        <span>CUSCO</span>
        <span>INCA TRAIL</span>
        <span>SALKANTAY</span>
        <span>MACHU PICCHU</span>
      </div>

      <!-- SLIDER MOBILE -->
      <div class="tsb-swiper swiper weather-mobile-slider">
        <div class="swiper-wrapper">
          <?php foreach ($months as $m):
            $img = !empty($m['image']) ? wp_get_attachment_image_url($m['image'], 'large') : '';
            if (!$img) continue;
          ?>
            <div class="swiper-slide">
              <img src="<?php echo esc_url($img); ?>" alt="Weather Month" class="weather-month-image" />
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- CONTROLES (DEBAJO DE TODO) -->
    <div class="swiper-controls">
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination__mobile"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>
</section>
