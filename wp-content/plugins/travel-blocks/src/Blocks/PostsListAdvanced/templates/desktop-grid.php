<?php
$posts_per_page = (int)(get_field('pla_posts_per_page') ?: 6);
$category_ids   = get_field('pla_categories') ?: [];
$specific_posts = get_field('pla_specific_posts') ?: [];
$show_excerpt   = (bool)get_field('pla_show_excerpt');
$style          = get_field('pla_style') ?: '';
$enable_swiper_desktop = (bool)get_field('pla_enable_swiper_desktop');
$enable_swiper_mobile  = (bool)get_field('pla_enable_swiper_mobile');
$slides_desktop = (int)(get_field('pla_slides_desktop') ?: 3);
$slides_mobile  = (int)(get_field('pla_slides_mobile') ?: 1);

$args = [
  'post_type'      => 'post',
  'posts_per_page' => $posts_per_page,
  'no_found_rows'  => true
];
if ($category_ids)  $args['category__in'] = array_map('intval', $category_ids);
if ($specific_posts) {
  $args['post__in'] = array_map('intval', $specific_posts);
  $args['orderby']  = 'post__in';
}

$q = new \WP_Query($args);
$total_posts = $q->found_posts;

$cfg = [
  'enableDesktopSwiper' => $enable_swiper_desktop,
  'enableMobileSwiper'  => $enable_swiper_mobile,
  'slidesDesktop'       => max(2, min(4, $slides_desktop)),
  'slidesMobile'        => max(1, min(2, $slides_mobile)),
  'style'               => $style,
  'showArrows'          => true,
  'showDots'            => true,
];
$block_id = 'pla-' . ($block['id'] ?? uniqid());
?>
<section id="<?php echo esc_attr($block_id); ?>" class="acf-gbr-pla align<?php echo esc_attr($block['align'] ?? 'wide'); ?>">
  <div class="pla-config" data-pla-config="<?php echo esc_attr(wp_json_encode($cfg)); ?>"></div>

  <!-- SSR inicial (grid visible por SEO) -->
  <div class="pla-mount">
    <div class="pla-grid">
      <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
        <article class="pla-card">
          <h3 class="pla-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <?php if ($show_excerpt): ?><div class="pla-excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
        </article>
      <?php endwhile; wp_reset_postdata(); else: ?>
        <p><?php esc_html_e('No hay resultados.', 'acf-gbr'); ?></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- DESKTOP SWIPER -->
  <template data-desktop-swiper>
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php if (isset($q) && $q->have_posts()): $q->rewind_posts(); while ($q->have_posts()): $q->the_post(); ?>
          <div class="swiper-slide">
            <article class="pla-card">
              <h3 class="pla-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <?php if ($show_excerpt): ?><div class="pla-excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
            </article>
          </div>
        <?php endwhile; wp_reset_postdata(); endif; ?>
      </div>

      <!-- 👇 flechas y dots -->
      
        <div class="swiper-button-prev">
          <svg width="14" height="14" viewBox="0 0 24 24"><path d="M15 18L9 12L15 6" stroke="#fff" stroke-width="2"/></svg>
        </div>
        <div class="swiper-button-next">
          <svg width="14" height="14" viewBox="0 0 24 24"><path d="M9 6L15 12L9 18" stroke="#fff" stroke-width="2"/></svg>
        </div>
        <div class="swiper-pagination"></div>
     
    </div>
  </template>

  <!-- MOBILE GRID -->
  <template data-mobile-grid>
    <div class="pla-grid pla-grid--mobile">
      <?php if (isset($q) && $q->have_posts()): $q->rewind_posts(); while ($q->have_posts()): $q->the_post(); ?>
        <article class="pla-card">
          <h3 class="pla-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <?php if ($show_excerpt): ?><div class="pla-excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
        </article>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
  </template>

  <!-- MOBILE SWIPER -->
  <template data-mobile-swiper>
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php if (isset($q) && $q->have_posts()): $q->rewind_posts(); while ($q->have_posts()): $q->the_post(); ?>
          <div class="swiper-slide">
            <article class="pla-card">
              <h3 class="pla-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <?php if ($show_excerpt): ?><div class="pla-excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
            </article>
          </div>
        <?php endwhile; wp_reset_postdata(); endif; ?>
      </div>

      <!-- 👇 flechas y dots -->
      
        <div class="swiper-button-prev">
          <svg width="14" height="14" viewBox="0 0 24 24"><path d="M15 18L9 12L15 6" stroke="#fff" stroke-width="2"/></svg>
        </div>
        <div class="swiper-button-next">
          <svg width="14" height="14" viewBox="0 0 24 24"><path d="M9 6L15 12L9 18" stroke="#fff" stroke-width="2"/></svg>
        </div>
        <div class="swiper-pagination"></div>
      
    </div>
  </template>
</section>
