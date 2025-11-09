<?php
if (!defined('ABSPATH')) exit;

$permalink = urlencode(get_permalink());
$title     = urlencode(get_the_title());
$image     = urlencode(get_the_post_thumbnail_url(get_the_ID(), 'full'));
$label     = get_field('share_title') ?: 'Share it';

$show_facebook  = get_field('show_facebook');
$show_pinterest = get_field('show_pinterest');
$show_linkedin  = get_field('show_linkedin');

$instagram   = get_field('instagram');
$youtube     = get_field('youtube');
$tripadvisor = get_field('tripadvisor');
?>

<div class="tc-social-share">
  <span class="tc-ss-label"><?php echo esc_html($label); ?></span>
  <div class="tc-ss-icons">

    <?php if ($show_facebook): ?>
      <a aria-label="Share on Facebook"
         class="tc-ss-icon tc-ss-facebook"
         href="<?php echo esc_url("https://www.facebook.com/sharer/sharer.php?u={$permalink}"); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91V127.41c0-25.35 12.42-50.06 52.24-50.06H295V6.26S277.43 0 256.36 0c-73.22 0-121 44.38-121 124.72V195.3H89.33V288h46.05v224h92.58V288z"/></svg>
      </a>
    <?php endif; ?>

    <?php if (!empty($instagram['visible']) && !empty($instagram['url'])): ?>
      <a aria-label="Open Instagram"
         class="tc-ss-icon tc-ss-instagram"
         target="_blank" rel="noopener"
         href="<?php echo esc_url($instagram['url']); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9S160.5 370.9 224.1 370.9 339 319.6 339 255.9 287.7 141 224.1 141zm146.4-41c0 14.9-12 26.9-26.9 26.9s-26.9-12-26.9-26.9S328.7 73 343.6 73s26.9 12 26.9 26.9zM398.8 80c-7.8-20.6-22.9-36.7-43.5-44.5C334.2 25.2 279.9 24 224 24S113.8 25.2 92.7 35.5C72.1 43.3 57 59.4 49.2 80 39 101.1 38 145.6 38 224s1 122.9 11.2 144c7.8 20.6 22.9 36.7 43.5 44.5 21.1 10.3 75.4 11.5 131.3 11.5s110.2-1.2 131.3-11.5c20.6-7.8 35.7-23.9 43.5-44.5C409 346.9 410 302.4 410 224s-1-122.9-11.2-144z"/></svg>
      </a>
    <?php endif; ?>

    <?php if ($show_pinterest): ?>
      <a aria-label="Share on Pinterest"
         class="tc-ss-icon tc-ss-pinterest"
         href="<?php echo esc_url("https://pinterest.com/pin/create/button/?url={$permalink}&media={$image}&description={$title}"); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path fill="currentColor" d="M248 8C111 8 0 119 0 256c0 104.9 67.8 194.1 162.6 230.4-2.3-19.6-4.4-49.6.9-71 4.8-20.4 31-130 31-130s-7.9-15.7-7.9-38.9c0-36.4 21.1-63.6 47.5-63.6 22.4 0 33.3 16.8 33.3 37 0 22.6-14.4 56.4-21.9 87.7-6.2 26.1 13.2 47.4 39.2 47.4 47 0 78.6-60.3 78.6-131.6 0-54.3-36.6-94.9-103.2-94.9-75.2 0-122.1 56-122.1 118.4 0 21.5 6.4 36.6 16.5 48.3 4.6 5.4 5.3 7.6 3.6 13.8-1.2 4.6-4 15.8-5.2 20.3-1.7 6.5-6.8 8.8-12.5 6.4-35.1-14.3-51.5-52.7-51.5-95.8 0-71.4 60.2-156.8 179.8-156.8 96 0 159.3 69.5 159.3 144.2 0 98.6-54.7 172.5-135.3 172.5-27 0-52.4-14.6-61.1-31.3l-16.6 63.3c-6 22.9-22.3 51.6-33.2 69.2 24.9 7.4 51.3 11.4 78.6 11.4 137 0 248-111 248-248S385 8 248 8z"/></svg>
      </a>
    <?php endif; ?>

    <?php if ($show_linkedin): ?>
      <a aria-label="Share on LinkedIn"
         class="tc-ss-icon tc-ss-linkedin"
         href="<?php echo esc_url("https://www.linkedin.com/shareArticle?mini=true&url={$permalink}&title={$title}"); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M100.28 448H7.4V148.9h92.88zm-46.14-340.9a53.4 53.4 0 1153.4-53.4 53.4 53.4 0 01-53.4 53.4zM447.9 448h-92.7V302.4c0-34.7-.7-79.4-48.3-79.4-48.3 0-55.7 37.7-55.7 76.6V448h-92.7V148.9h89v40.8h1.3c12.4-23.5 42.6-48.3 87.8-48.3 94 0 111.3 61.9 111.3 142.3z"/></svg>
      </a>
    <?php endif; ?>

    <?php if (!empty($tripadvisor['visible']) && !empty($tripadvisor['url'])): ?>
      <a aria-label="Open TripAdvisor"
         class="tc-ss-icon tc-ss-tripadvisor"
         target="_blank" rel="noopener"
         href="<?php echo esc_url($tripadvisor['url']); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528.1 171.5a115.3 115.3 0 0 0-63.6-20.1H111.5a115.3 115.3 0 0 0-63.6 20.1L0 150.3v202.6l47.9-21.2A115.2 115.2 0 0 0 111.5 352h353a115.2 115.2 0 0 0 63.6-20.3L576 352.9V150.3ZM288 278.5a45.5 45.5 0 1 1 45.5-45.5 45.5 45.5 0 0 1-45.5 45.5z"/></svg>
      </a>
    <?php endif; ?>

    <?php if (!empty($youtube['visible']) && !empty($youtube['url'])): ?>
      <a aria-label="Open YouTube"
         class="tc-ss-icon tc-ss-youtube"
         target="_blank" rel="noopener"
         href="<?php echo esc_url($youtube['url']); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M549.7 124.1A68.6 68.6 0 0 0 512 88.3C465.4 80 288 80 288 80s-177.4 0-224 8.3a68.6 68.6 0 0 0-37.7 35.8C8 171.7 8 256 8 256s0 84.3 18.3 131.9a68.6 68.6 0 0 0 37.7 35.8c46.6 8.3 224 8.3 224 8.3s177.4 0 224-8.3a68.6 68.6 0 0 0 37.7-35.8C568 340.3 568 256 568 256s0-84.3-18.3-131.9zM232 336V176l142 80z"/></svg>
      </a>
    <?php endif; ?>

  </div>
</div>
