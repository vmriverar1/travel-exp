<?php

namespace Travel\CategoryBanner\Render;

class BannerRenderer
{
  public static function render(array $data): string
  {
    $id       = esc_attr($data['id'] ?? wp_unique_id('tcb-'));
    $align    = esc_attr($data['align'] ?? 'full');
    $title    = esc_html($data['title'] ?? '');
    $bg       = esc_url($data['background'] ?? '');
    $btn_text = esc_html($data['button_text'] ?? '');
    $btn_url  = esc_url($data['button_url'] ?? '#');
    $packages = $data['packages'] ?? [];

    ob_start(); ?>
    <section class="tcb-banner align<?php echo $align; ?>" id="<?php echo $id; ?>">
      <div class="tcb-banner__bg" style="<?php echo $bg ? 'background-image:url(' . $bg . ')' : ''; ?>"></div>
      <div class="tcb-banner__overlay"></div>
      <?php if (!empty($packages)): ?>
        <div class="tcb-slider">
          <button class="tcb-nav tcb-prev" aria-label="Previous"></button>
          <button class="tcb-nav tcb-next" aria-label="Next"></button>

          <div class="swiper tcb-swiper" data-slides="3.5" data-slides-mobile="1.2">
            <div class="swiper-wrapper">
              <?php foreach ($packages as $pkg):
                $img = esc_url($pkg['image'] ?? '');
                $tt  = esc_html($pkg['title'] ?? '');
                $lnk = esc_url($pkg['link'] ?? '#'); ?>
                <div class="swiper-slide">
                  <article class="tcb-card">
                    <a href="<?php echo $lnk; ?>" class="tcb-card__link">
                      <?php if ($img): ?><img src="<?php echo $img; ?>" alt="<?php echo $tt; ?>" class="tcb-card__img" loading="lazy"><?php endif; ?>
                      <span class="tcb-card__title"><?php echo $tt; ?></span>
                    </a>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="tcb-banner__inner">
        <div class="tcb-banner__content">
          <div>
            <?php if ($title): ?><h2 class="tcb-banner__title"><?php echo $title; ?></h2><?php endif; ?>
            <p class="tcb-banner__paragraph">The iconic path to Machu Picchu, with <br> permits managed and expert local guides</p>
          </div>
          <?php if ($btn_text): ?><a href="<?php echo $btn_url; ?>" class="tcb-btn"><?php echo $btn_text; ?></a><?php endif; ?>
        </div>
      </div>
    </section>
<?php
    return ob_get_clean();
  }
}
