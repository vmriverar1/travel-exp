<?php
/**
 * Render Template: Travel Category Packages (Dynamic Grid + Slider)
 */

$packages_title = get_field('packages_title') ?: 'Inca Trail Treks to Machu Picchu';
$seo_text = get_field('seo_text') ?: 'The iconic path to Machu Picchu, with permits managed and expert local guides.';
$cta_text = get_field('cta_text') ?: 'See Details';

// Query dinámico – últimos 6 paquetes publicados
$args = [
  'post_type'      => 'package',
  'posts_per_page' => 6,
  'post_status'    => 'publish',
];
$query = new WP_Query($args);
if (!$query->have_posts()) return;
?>

<section class="travel-category-packages-block">

  <!-- === TEXTO SUPERIOR (SOLO DESKTOP) === -->
  <div class="tcp-header desktop-only">
    <h2 class="tcp-title"><?php echo esc_html($packages_title); ?></h2>
    <p class="tcp-description"><?php echo esc_html($seo_text); ?></p>
  </div>

  <!-- === GRID DESKTOP === -->
  <div class="tcp-grid desktop-only">
    <?php while ($query->have_posts()): $query->the_post();
      $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
      $price = get_field('precio_desde') ?: 'From $1,145';
      $duration = get_field('duracion') ?: 'Full Day';
    ?>
      <a href="<?php the_permalink(); ?>" class="tcp-item">
        <?php if ($img): ?>
          <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
        <?php endif; ?>

        <div class="tcp-overlay">
          <span class="tcp-badge">Inca Trail</span>
          <h3 class="tcp-name"><?php the_title(); ?></h3>
          <p class="tcp-meta"><?php echo esc_html($duration . ' | ' . $price); ?></p>
          <span class="tcp-btn"><?php echo esc_html($cta_text); ?></span>
        </div>
      </a>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>

  <!-- === MOBILE (SLIDER) === -->
  <div class="tcp-mobile mobile-only">
    <div class="tcp-header">
      <h2 class="tcp-title"><?php echo esc_html($packages_title); ?></h2>
      <p class="tcp-description"><?php echo esc_html($seo_text); ?></p>
    </div>

    <div class="swiper tcp-swiper">
      <div class="swiper-wrapper">
        <?php
        while ($query->have_posts()): $query->the_post();
          $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
          $price = get_field('precio_desde') ?: 'From $1,145';
          $duration = get_field('duracion') ?: 'Full Day';
        ?>
          <div class="swiper-slide">
            <a href="<?php the_permalink(); ?>" class="tcp-item">
              <?php if ($img): ?>
                <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
              <?php endif; ?>
              <div class="tcp-overlay">
                <span class="tcp-badge">Inca Trail</span>
                <h3 class="tcp-name"><?php the_title(); ?></h3>
                <p class="tcp-meta"><?php echo esc_html($duration . ' | ' . $price); ?></p>
                <span class="tcp-btn"><?php echo esc_html($cta_text); ?></span>
              </div>
            </a>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <!-- Controles -->
      <div class="tcp-controls">
        <button class="tcp-nav tcp-prev"></button>
        <div class="tcp-pagination"></div>
        <button class="tcp-nav tcp-next"></button>
      </div>
    </div>
  </div>
</section>
