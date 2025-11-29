<?php
/**
 * Render Template: Travel Category Packages (Grid + Slider) con botón de favorito
 */

$packages_title = get_field('packages_title') ?: 'Inca Trail Treks to Machu Picchu';
$seo_text = get_field('seo_text') ?: 'The iconic path to Machu Picchu, with permits managed and expert local guides.';
$cta_text = get_field('cta_text') ?: 'See Details';

$args = [
  'post_type'      => 'package',
  'posts_per_page' => 5,
  'post_status'    => 'publish',
];

$query = new WP_Query($args);
if (!$query->have_posts()) return;
?>

<section class="travel-packages-section">

  <!-- === GRID DESKTOP === -->
  <div class="tcp-desktop-grid">
    <div class="tcp-header">
      <h2 class="tcp-title"><?php echo esc_html($packages_title); ?></h2>
      <p class="tcp-description"><?php echo esc_html($seo_text); ?></p>
    </div>

    <?php
    $i = 0;
    while ($query->have_posts()): $query->the_post();
      $i++;

      $image = get_field('main_image', get_the_ID()) ?: get_the_post_thumbnail_url(get_the_ID(), 'large');
      $raw_price = get_field('price_from', get_the_ID());
      $price = $raw_price ? '$' . number_format((float)$raw_price, 0, '.', ',') : null;
      $day = get_field('days', get_the_ID()) ?: 'Full Day';
      $locs = get_field('locations', get_the_ID());
      $tag = get_field('tag_label', get_the_ID()) ?: 'By Train';

      if (is_array($locs)) {
        $loc_names = [];
        foreach ($locs as $loc_id) {
          $post_obj = get_post($loc_id);
          if ($post_obj) $loc_names[] = $post_obj->post_title;
        }
        $locs = implode(', ', $loc_names);
      }
    ?>
      <a href="<?php the_permalink(); ?>" class="tcp-card tcp-card-<?php echo $i; ?>">
        <?php if ($image): ?>
          <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>">
        <?php endif; ?>

        <span class="tcp-badge"><?php echo esc_html($tag); ?></span>

        <button class="favorite-btn" type="button" aria-label="Add to favorites">
          <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194Z" fill="black" />
          </svg>
        </button>

        <div class="tcp-overlay">
          <h3 class="tcp-name"><?php the_title(); ?></h3>

          <?php if ($locs): ?>
            <p class="tcp-location"><?php echo esc_html($locs); ?></p>
          <?php endif; ?>

          <p class="tcp-meta">
            <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
            <?php if ($price): ?> | From <?php echo esc_html($price); ?><?php endif; ?>
          </p>

          <span class="tcp-btn"><?php echo esc_html($cta_text); ?></span>
        </div>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- === SLIDER MOBILE === -->
  <div class="tcp-mobile-slider">
    <div class="tcp-header">
      <h2 class="tcp-title"><?php echo esc_html($packages_title); ?></h2>
      <p class="tcp-description"><?php echo esc_html($seo_text); ?></p>
    </div>

    <div class="swiper tcp-swiper">
      <div class="swiper-wrapper">
        <?php
        $query->rewind_posts();
        while ($query->have_posts()): $query->the_post();

          $image = get_field('main_image', get_the_ID()) ?: get_the_post_thumbnail_url(get_the_ID(), 'large');
          $raw_price = get_field('price_from', get_the_ID());
          $price = $raw_price ? '$' . number_format((float)$raw_price, 0, '.', ',') : null;
          $day = get_field('days', get_the_ID()) ?: 'Full Day';
          $locs = get_field('locations', get_the_ID());
          $tag = get_field('tag_label', get_the_ID()) ?: 'By Train';

          if (is_array($locs)) {
            $loc_names = [];
            foreach ($locs as $loc_id) {
              $post_obj = get_post($loc_id);
              if ($post_obj) $loc_names[] = $post_obj->post_title;
            }
            $locs = implode(', ', $loc_names);
          }
        ?>
          <div class="swiper-slide">
            <a href="<?php the_permalink(); ?>" class="tcp-card">
              <?php if ($image): ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>">
              <?php endif; ?>

              <span class="tcp-badge"><?php echo esc_html($tag); ?></span>

              <button class="favorite-btn" type="button" aria-label="Add to favorites">
                <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.81383 14.7194C7.73973 14.7194 7.66563 14.6911 7.60898 14.6348L1.524 8.59288C1.49335 8.56261 1.47085 8.52692 1.45688 8.48929C-0.503553 6.45203 -0.48687 3.20042 1.51546 1.18412C3.04253 -0.354195 5.53643 -0.362731 7.07436 1.16434L7.78745 1.87239L8.49551 1.15929C10.0222 -0.378638 12.5161 -0.387561 14.054 1.13951C16.0703 3.14184 16.1103 6.39345 14.1642 8.44428C14.1502 8.4823 14.1281 8.518 14.0978 8.54826L8.05554 14.6332C8.00123 14.6879 7.92712 14.719 7.84992 14.7194Z" fill="black" />
                </svg>
              </button>

              <div class="tcp-overlay">
                <h3 class="tcp-name"><?php the_title(); ?></h3>

                <?php if ($locs): ?>
                  <p class="tcp-location"><?php echo esc_html($locs); ?></p>
                <?php endif; ?>

                <p class="tcp-meta">
                  <?php echo esc_html(is_numeric($day) ? "{$day} Days" : $day); ?>
                  <?php if ($price): ?> | From <?php echo esc_html($price); ?><?php endif; ?>
                </p>

                <span class="tcp-btn"><?php echo esc_html($cta_text); ?></span>
              </div>
            </a>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <div class="swiper-controls">
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination__mobile"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </div>
</section>
