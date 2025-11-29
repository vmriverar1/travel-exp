<?php
$seo_text = get_field('seo_text') ?: 'Soy un texto SEO. Soy un texto SEO. Soy un texto SEO.';
$terms = get_field('selected_destinations') ?: [];

if (empty($terms)) return;

error_log('=== Debug Travel Destinations ===');
error_log('Cantidad de términos: ' . count($terms));
?>

<section class="travel-destinations-block">

  <!-- === GRID PRINCIPAL (DESKTOP) === -->
  <div class="travel-destinations__grid desktop-only">

    <!-- === COLUMNA 1 (20% + 80%) === -->
    <div class="travel-column column-1">
      <?php
      $term1 = $terms[0] ?? null;
      $term2 = $terms[1] ?? null;

      // === Primer destino ===
      if ($term1):
        $term_link = get_term_link($term1->term_id, $term1->taxonomy);
        if (!is_wp_error($term_link)):
          $taxonomy_prefix = $term1->taxonomy . '_' . $term1->term_id;
          $image = get_field('thumbnail', $taxonomy_prefix);
          $image_url = $image['url'] ?? '';

          error_log('Term1: ' . $term1->name . ' | Tax: ' . $term1->taxonomy . ' | Field: image | Result: ' . print_r($image, true));
      ?>
        <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item small">
          <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term1->name); ?>">
          <?php endif; ?>
          <span class="travel-destination__title"><?php echo esc_html($term1->name); ?></span>
        </a>
      <?php endif; endif; ?>

      <!-- === Segundo destino === -->
      <?php
      if ($term2):
        $term_link = get_term_link($term2->term_id, $term2->taxonomy);
        if (!is_wp_error($term_link)):
          $taxonomy_prefix = $term2->taxonomy . '_' . $term2->term_id;
          $image = get_field('thumbnail', $taxonomy_prefix);
          $image_url = $image['url'] ?? '';

          error_log('Term2: ' . $term2->name . ' | Tax: ' . $term2->taxonomy . ' | Result: ' . print_r($image, true));
      ?>
        <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item large">
          <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term2->name); ?>">
          <?php endif; ?>
          <span class="travel-destination__title"><?php echo esc_html($term2->name); ?></span>
        </a>
      <?php endif; endif; ?>
    </div>

    <!-- === COLUMNA 2 (50% + 50%) === -->
    <div class="travel-column column-2">
      <?php
      for ($i = 2; $i <= 3; $i++):
        $term = $terms[$i] ?? null;
        if (!$term) continue;

        $term_link = get_term_link($term->term_id, $term->taxonomy);
        if (is_wp_error($term_link)) continue;

        $taxonomy_prefix = $term->taxonomy . '_' . $term->term_id;
        $image = get_field('thumbnail', $taxonomy_prefix);
        $image_url = $image['url'] ?? '';

        error_log('Col2 Term: ' . $term->name . ' | Tax: ' . $term->taxonomy . ' | Field image: ' . print_r($image, true));
      ?>
        <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item medium">
          <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
          <?php endif; ?>
          <span class="travel-destination__title"><?php echo esc_html($term->name); ?></span>
        </a>
      <?php endfor; ?>
    </div>

    <!-- === COLUMNA 3 (50% + 50%) === -->
    <div class="travel-column column-3">
      <?php
      for ($i = 4; $i <= 5; $i++):
        $term = $terms[$i] ?? null;
        if (!$term) continue;

        $term_link = get_term_link($term->term_id, $term->taxonomy);
        if (is_wp_error($term_link)) continue;

        $taxonomy_prefix = $term->taxonomy . '_' . $term->term_id;
        $image = get_field('thumbnail', $taxonomy_prefix);
        $image_url = $image['url'] ?? '';

        error_log('Col3 Term: ' . $term->name . ' | Tax: ' . $term->taxonomy . ' | Result: ' . print_r($image, true));
      ?>
        <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item medium">
          <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
          <?php endif; ?>
          <span class="travel-destination__title"><?php echo esc_html($term->name); ?></span>
        </a>
      <?php endfor; ?>
    </div>

    <!-- === COLUMNA 4 (TEXTO 20% + IMAGEN 80%) === -->
    <div class="travel-column column-4">
      <article class="travel-destination-text">
        <h3 class="travel-destinations__heading">Popular destinations</h3>
        <p class="travel-destinations__text"><?php echo esc_html($seo_text); ?></p>
      </article>

      <?php
      $term = $terms[6] ?? null;
      if ($term):
        $term_link = get_term_link($term->term_id, $term->taxonomy);
        if (!is_wp_error($term_link)):
          $taxonomy_prefix = $term->taxonomy . '_' . $term->term_id;
          $image = get_field('thumbnail', $taxonomy_prefix);
          $image_url = $image['url'] ?? '';

          error_log('Col4 Term: ' . $term->name . ' | Tax: ' . $term->taxonomy . ' | Result: ' . print_r($image, true));
      ?>
        <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item large">
          <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
          <?php endif; ?>
          <span class="travel-destination__title"><?php echo esc_html($term->name); ?></span>
        </a>
      <?php endif; endif; ?>
    </div>
  </div>

  <!-- === MOBILE (SEO TEXT + SWIPER) === -->
  <aside class="travel-destinations__content mobile-only">
    <h3 class="travel-destinations__heading">Popular destinations</h3>
    <p class="travel-destinations__text"><?php echo esc_html($seo_text); ?></p>
  </aside>

  <!-- === SWIPER MOBILE === -->
  <section class="travel-swiper-block travel-swiper--destinations swiper-rows-1 mobile-only">
    <div class="tsb-swiper swiper">
      <div class="swiper-wrapper">
        <?php foreach ($terms as $term):
          $term_link = get_term_link($term->term_id, $term->taxonomy);
          if (is_wp_error($term_link)) continue;

          $taxonomy_prefix = $term->taxonomy . '_' . $term->term_id;
          $image = get_field('thumbnail', $taxonomy_prefix);
          $image_url = $image['url'] ?? '';

          error_log('Swiper Term: ' . $term->name . ' | Tax: ' . $term->taxonomy . ' | Field: ' . print_r($image, true));
        ?>
          <div class="swiper-slide">
            <a href="<?php echo esc_url($term_link); ?>" class="travel-destination-item">
              <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
              <?php endif; ?>
              <span class="travel-destination__title"><?php echo esc_html($term->name); ?></span>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Controles -->
      <div class="swiper-controls">
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination__mobile"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </section>
</section>
