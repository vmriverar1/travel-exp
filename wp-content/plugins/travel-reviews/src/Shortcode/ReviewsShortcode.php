<?php

namespace Travel\Reviews\Shortcode;

class ReviewsShortcode
{
  private const CACHE_KEY = 'vtc_reviews_cache_v7';
  private const CACHE_DURATION = 6 * HOUR_IN_SECONDS;

  private array $networks = [];

  public function __construct()
  {
    $plugin_url = get_stylesheet_directory_uri() . '/reviews/img/';

    $this->networks = [
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
  }

  public function register(): void
  {
    add_shortcode('vtc_reviews', [$this, 'render']);

    // protección completa contra wpautop en taxonomías y contenido
    add_filter('the_content', [$this, 'fixShortcodeFormatting'], 0);
    add_filter('term_description', [$this, 'fixShortcodeFormatting'], 0);
    add_filter('the_archive_description', [$this, 'fixShortcodeFormatting'], 0);
    add_filter('category_description', [$this, 'fixShortcodeFormatting'], 0);
    add_filter('tag_description', [$this, 'fixShortcodeFormatting'], 0);
  }

  public function fixShortcodeFormatting(string $content): string
  {
    if (
      has_shortcode($content, 'vtc_reviews') ||
      strpos($content, '[vtc_reviews') !== false
    ) {
      remove_filter('the_content', 'wpautop');
      remove_filter('the_excerpt', 'wpautop');
      remove_filter('term_description', 'wpautop');
      remove_filter('category_description', 'wpautop');
      remove_filter('tag_description', 'wpautop');
      remove_filter('the_archive_description', 'wpautop');

      // Restore after our shortcode to not affect rest of site
      add_filter('the_content', 'wpautop', 99);
      add_filter('the_excerpt', 'wpautop', 99);
      add_filter('term_description', 'wpautop', 99);
      add_filter('category_description', 'wpautop', 99);
      add_filter('tag_description', 'wpautop', 99);
      add_filter('the_archive_description', 'wpautop', 99);
    }

    return $content;
  }


  public function render(): string
  {
    $cached = get_transient(self::CACHE_KEY);
    if ($cached !== false) {
      return $cached;
    }

    $grouped = $this->fetchAllReviews();

    ob_start();
    $this->renderHTML($grouped);
    $output = ob_get_clean();

    set_transient(self::CACHE_KEY, $output, self::CACHE_DURATION);

    return $output;
  }

  private function fetchAllReviews(): array
  {
    $grouped = [];

    foreach ($this->networks as $slug => $data) {
      $cache_key = "vtc_reviews_network_{$slug}";
      $cached = get_transient($cache_key);

      if ($cached !== false) {
        $grouped[$slug] = $cached;
        continue;
      }

      $url = "https://cms.valenciatravelcusco.com/reviews/social-media?supplier={$slug}";
      $response = wp_remote_get($url, ['timeout' => 10]);

      if (is_wp_error($response)) {
        $grouped[$slug] = [];
        continue;
      }

      $body = json_decode(wp_remote_retrieve_body($response), true);
      $grouped[$slug] = $body['results'] ?? [];

      set_transient($cache_key, $grouped[$slug], self::CACHE_DURATION);
    }

    return $grouped;
  }


  private function renderHTML(array $grouped): void
  {
?>
    <div id="vtc-reviews" class="vtc-reviews">
      <?php $this->renderTabs(); ?>
      <?php $this->renderHeader(); ?>
      <?php $this->renderTabContents($grouped); ?>
    </div>
<?php
  }


  private function renderTabs(): void
  {
?>
    <div class="vtc-tabs">
      <?php foreach ($this->networks as $slug => $data): ?>
        <button type="button"
          data-tab="<?php echo esc_attr($slug); ?>"
          class="<?php echo $slug === 'trip-advisor' ? 'active' : ''; ?>">
          <div class="vtc-tabs__image">
            <img src="<?php echo esc_url($data['logo']); ?>"
              alt="<?php echo esc_attr($data['name']); ?>"
              loading="lazy">
          </div>
        </button>
      <?php endforeach; ?>
    </div>
<?php
  }


  private function renderHeader(): void
  {
?>
    <div class="vtc-reviews-header" data-header-trip>
      <div class="rating trip-summary">
        <strong>5.0</strong>
        <?php echo str_repeat('<span class="circle filled"></span>', 5); ?>
        <span class="count">1982 reviews</span>
      </div>

      <a href="<?php echo esc_url($this->networks['trip-advisor']['url']); ?>"
        target="_blank"
        class="review-link">Write a review</a>
    </div>
<?php
  }


  private function renderTabContents(array $grouped): void
  {
    foreach ($this->networks as $slug => $data):
      $isFirst = $slug === 'trip-advisor';
      $reviews = $grouped[$slug] ?? [];
?>
      <div class="vtc-tab-content <?php echo $isFirst ? 'active' : ''; ?>"
        data-tab-content="<?php echo esc_attr($slug); ?>"
        style="<?php echo $isFirst ? 'display:block;' : 'display:none;'; ?>">

        <?php if (!empty($reviews)): ?>
          <?php $this->renderSwiper($reviews, $slug, $data); ?>
        <?php else: ?>
          <div>No hay reseñas disponibles para <strong><?php echo esc_html($data['name']); ?></strong>.</div>
        <?php endif; ?>

      </div>
<?php
    endforeach;
  }


  private function renderSwiper(array $reviews, string $slug, array $data): void
  {
?>
    <div class="swiper vtc-swiper">
      <div class="swiper-wrapper">
        <?php foreach ($reviews as $review): ?>
          <?php $this->renderCard($review, $slug, $data); ?>
        <?php endforeach; ?>
      </div>

      <div class="swiper-controls reviews-controls">
        <button class="swiper-prev">‹</button>
        <div class="swiper-pagination"></div>
        <button class="swiper-nex">›</button>
      </div>
    </div>
<?php
  }


  private function renderCard(array $review, string $slug, array $data): void
  {
    $reviewerName = $review['reviewerName'] ?? '(Anónimo)';
    $publishedAt  = $review['publishedAt'] ?? '';
    $title        = $review['title'] ?? '';
    $text         = trim($review['text'] ?? '');
    $rating       = $slug === 'trip-advisor' ? (int)($review['rating'] ?? 0) : 5;

    $formattedDate = '';
    if ($publishedAt) {
      $timestamp = strtotime($publishedAt);
      $formattedDate = $timestamp ? date('F d', $timestamp) : $publishedAt;
    }
?>

    <div class="swiper-slide">
      <div class="vtc-card">

        <div class="reviewer"><?php echo esc_html($reviewerName); ?></div>

        <?php if ($formattedDate): ?>
          <div class="date"><?php echo esc_html($formattedDate); ?></div>
        <?php endif; ?>

        <div class="rating <?php echo esc_attr($slug); ?>">
          <?php
          echo str_repeat('<span class="star filled"></span>', $rating);
          echo str_repeat('<span class="star empty"></span>', 5 - $rating);
          ?>
        </div>

        <?php if ($slug === 'trip-advisor' && $title): ?>
          <div class="title"><?php echo esc_html($title); ?></div>
        <?php endif; ?>

        <?php if ($text): ?>
          <div class="review-text truncated"><?php echo esc_html($text); ?></div>

          <div class="show-more-wrapper">
            <button type="button" class="show-more">Show more</button>
          </div>
        <?php endif; ?>

        <div class="logo-text">
          <img src="<?php echo esc_url($data['logo']); ?>"
            alt="<?php echo esc_attr($data['name']); ?>"
            loading="lazy">
        </div>

      </div>
    </div>

<?php
  }

}
