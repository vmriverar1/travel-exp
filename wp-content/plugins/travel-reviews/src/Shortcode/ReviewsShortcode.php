<?php

namespace Travel\Reviews\Shortcode;

class ReviewsShortcode
{
  public function register()
  {
    add_shortcode('vtc_reviews', [$this, 'render']);
  }

  public function render()
  {
    $cache_key = 'vtc_reviews_cache_travel';
    $cached = get_transient($cache_key);
    if ($cached !== false) return $cached;

    // 🔹 Ruta actualizada hacia /themes/reviews/img/
    $plugin_url = get_stylesheet_directory_uri() . '/reviews/img/';

    $networks = [
      'trip-advisor' => [
        'name' => 'TripAdvisor',
        'logo' => $plugin_url . 'tripadvisor-reviews.svg',
        'url'  => 'https://www.tripadvisor.com.pe/Attraction_Review-g294314-d2469804-Reviews-Valencia_Travel_Cusco-Cusco_Cusco_Region.html'
      ],
      'google' => [
        'name' => 'Google',
        'logo' => $plugin_url . 'google-reviews.svg',
      ],
      'facebook' => [
        'name' => 'Facebook',
        'logo' => $plugin_url . 'facebook-reviews.svg',
      ],
    ];

    $grouped = [];

    // 🔹 Cargar reseñas con cache individual por red
    foreach ($networks as $slug => $data) {
      $cache_network = "vtc_reviews_group_$slug";
      $grouped_cache = get_transient($cache_network);

      if ($grouped_cache !== false) {
        $grouped[$slug] = $grouped_cache;
        continue;
      }

      $url = "https://cms.valenciatravelcusco.com/reviews/social-media?supplier={$slug}";
      $response = wp_remote_get($url);

      if (is_wp_error($response)) continue;

      $body = json_decode(wp_remote_retrieve_body($response), true);
      $grouped[$slug] = $body['results'] ?? [];

      // Guardar cache individual 6h
      set_transient($cache_network, $grouped[$slug], 6 * HOUR_IN_SECONDS);
    }

    ob_start(); ?>
    <div id="vtc-reviews" class="vtc-reviews">

      <!-- 🔹 TABS -->
      <div class="vtc-tabs">
        <?php foreach ($networks as $slug => $data): ?>
          <button
            data-tab="<?php echo esc_attr($slug); ?>"
            class="<?php echo $slug === 'trip-advisor' ? 'active' : ''; ?>">
            <div class="vtc-tabs__image">
              <img src="<?php echo esc_url($data['logo']); ?>"
                alt="<?php echo esc_attr($data['name']); ?> logo"
                width="100"
                height="auto"
                style="vertical-align: middle;">
            </div>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- 🔹 HEADER SOLO PARA TRIPADVISOR -->
      <div class="vtc-reviews-header" data-header-trip style="display:flex;align-items:center;gap:12px;">
        <div class="rating trip-summary">
          <strong>5.0</strong>
          <?php echo str_repeat('<span class="circle filled"></span>', 5); ?>
          <span class="count">1982 reviews</span>
        </div>
        <a href="<?php echo esc_url($networks['trip-advisor']['url']); ?>"
          target="_blank" class="review-link">Write a review</a>
      </div>

      <!-- 🔹 CONTENIDO DE LAS REDES -->
      <?php foreach ($networks as $slug => $data): ?>
        <div class="vtc-tab-content"
          data-tab-content="<?php echo esc_attr($slug); ?>"
          style="<?php echo $slug === 'trip-advisor' ? 'display:block;' : 'display:none;'; ?>">

          <?php if (!empty($grouped[$slug])): ?>
            <div class="swiper vtc-swiper">
              <div class="swiper-wrapper">
                <?php foreach ($grouped[$slug] as $r): ?>
                  <div class="swiper-slide">
                    <div class="vtc-card">
                      <!-- Nombre -->
                      <strong class="reviewer"><?php echo esc_html($r['reviewerName'] ?? '(Anónimo)'); ?></strong>

                      <!-- Fecha -->
                      <?php if (!empty($r['publishedAt'])):
                        $timestamp = strtotime($r['publishedAt']);
                        $formatted_date = $timestamp ? date('F d', $timestamp) : $r['publishedAt'];
                      ?>
                        <span class="date"><?php echo esc_html($formatted_date); ?></span>
                      <?php endif; ?>

                      <!-- ⭐ Puntitos -->
                      <div class="rating <?php echo esc_attr($slug); ?>">
                        <?php
                        if ($slug === 'trip-advisor') {
                          $rating = (int)($r['rating'] ?? 0);
                        } else {
                          $rating = 5; // fijo para Google y Facebook
                        }

                        echo str_repeat('<span class="star filled"></span>', $rating);
                        echo str_repeat('<span class="star empty"></span>', 5 - $rating);
                        ?>
                      </div>


                      <!-- Título solo TripAdvisor -->
                      <?php if ($slug === 'trip-advisor' && !empty($r['title'])): ?>
                        <h4><?php echo esc_html($r['title']); ?></h4>
                      <?php endif; ?>

                      <!-- Texto + botón Show more -->
                      <?php
                      $text = trim($r['text'] ?? '');
                      $hasMore = strlen($text) > 0; // si tiene texto, habilita show more
                      ?>
                      <p class="review-text<?php echo $hasMore ? ' truncated' : ''; ?>">
                        <?php echo esc_html($text); ?>
                      </p>
                      <?php if ($hasMore): ?>
                        <button class="show-more">Show more</button>
                      <?php endif; ?>


                      <!-- Logo -->
                      <div class="logo-text">
                        <img src="<?php echo esc_url($data['logo']); ?>"
                          alt="<?php echo esc_attr($data['name']); ?>"
                          width="90">
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- 🔹 Controles -->
              <div class="swiper-controls">
                <button class="swiper-prev" aria-label="Anterior">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6" />
                  </svg>
                </button>

                <div class="swiper-pagination"></div>

                <button class="swiper-nex" aria-label="Siguiente">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 6l6 6-6 6" />
                  </svg>
                </button>
              </div>
            </div>
          <?php else: ?>
            <p>No hay reseñas disponibles para <b><?php echo esc_html($data['name']); ?></b>.</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
<?php

    // 🔹 Cache general del HTML completo
    $output = ob_get_clean();
    set_transient($cache_key, $output, 6 * HOUR_IN_SECONDS);

    return $output;
  }
}
