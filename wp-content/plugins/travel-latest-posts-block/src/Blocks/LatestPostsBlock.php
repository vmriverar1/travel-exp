<?php
namespace Travel\Latest\Blocks;

class LatestPostsBlock {
  public function render($block = [], $content = '', $is_preview = false, $post_id = 0) {
    $id = 'travel-lp-' . ($block['id'] ?? uniqid());
    $class = 'travel-lp ' . ($block['className'] ?? '');
    $align = !empty($block['align']) ? 'align' . $block['align'] : '';

    // Campos ACF
    $title        = get_field('travel_title') ?: __('Latest posts', 'travel-lp');
    $button_text  = get_field('travel_button_text') ?: __('CHECK IT OUT >', 'travel-lp');
    $number_posts = intval(get_field('travel_number_posts')) ?: 2;
    $selected     = get_field('travel_selected_posts');
    $featured     = get_field('travel_featured_first');
    $force_latest = (bool) get_field('travel_show_last_post');

    // Normalizar
    $selected_ids = [];
    if (is_array($selected)) {
      $selected_ids = array_map(fn($p) => is_object($p) ? intval($p->ID) : intval($p), $selected);
    }
    $featured_id = $featured ? (is_object($featured) ? intval($featured->ID) : intval($featured)) : 0;

    $posts = [];

    // Destacado
    if ($featured_id) {
      $fp = get_post($featured_id);
      if ($fp && $fp->post_status === 'publish') $posts[] = $fp;
    }

    // Seleccionados
    if (!empty($selected_ids)) {
      $remaining = max(0, $number_posts - count($posts));
      if ($remaining > 0) {
        $ids = array_values(array_diff($selected_ids, [$featured_id]));
        $sel = get_posts([
          'post__in' => array_slice($ids, 0, $remaining),
          'orderby' => 'post__in',
          'posts_per_page' => $remaining,
          'post_status' => 'publish'
        ]);
        $posts = array_merge($posts, $sel);
      }
    }

    // Rellenar con últimos
    if (count($posts) < $number_posts && $force_latest) {
      $need = $number_posts - count($posts);
      $exclude = wp_list_pluck($posts, 'ID');
      $latest = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $need,
        'post__not_in' => $exclude
      ]);
      $posts = array_merge($posts, $latest);
    }

    while (count($posts) < $number_posts) $posts[] = null;

    ?>
    <section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(trim($class.' '.$align)); ?>">
      <div class="travel-lp__header">
        <h3 class="travel-lp__title"><?php echo esc_html($title); ?></h3>
      </div>

      <div class="travel-lp__grid swiper">
        <div class="swiper-wrapper">
          <?php foreach ($posts as $p): ?>
            <div class="travel-lp__card swiper-slide">
              <div class="travel-lp__card-inner">

                <!-- Imagen y corazón -->
                <div class="travel-lp__image">
                  <?php if ($p): ?>
                    <?php echo get_the_post_thumbnail($p, 'large'); ?>
                  <?php else: ?>
                    <div class="travel-lp__placeholder"></div>
                  <?php endif; ?>

                  <button class="travel-lp__fav" aria-label="<?php esc_attr_e('Favorite', 'travel-lp'); ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                      <path d="M12 21s-6.7-4.35-9.33-7C-0.22 11.48 1.38 6.5 6 6.5c2.06 0 3.4 1.05 4 2.06.6-1.01 1.94-2.06 4-2.06 4.62 0 6.22 4.98 3.33 7.5C18.7 16.65 12 21 12 21z"
                            fill="currentColor"/>
                    </svg>
                  </button>
                </div>

                <!-- Contenido -->
                <?php if ($p): ?>
                  <div class="travel-lp__content">
                    <h4 class="travel-lp__post-title">
                      <a href="<?php echo esc_url(get_permalink($p)); ?>">
                        <?php echo esc_html(get_the_title($p)); ?>
                      </a>
                    </h4>
                    <p class="travel-lp__excerpt">
                      <?php echo esc_html(wp_trim_words(get_the_excerpt($p), 22)); ?>
                    </p>
                    <a class="travel-lp__button" href="<?php echo esc_url(get_permalink($p)); ?>">
                      <?php echo esc_html($button_text); ?>
                    </a>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="travel-lp__pagination"></div>
      </div>
    </section>
    <?php
  }
}
