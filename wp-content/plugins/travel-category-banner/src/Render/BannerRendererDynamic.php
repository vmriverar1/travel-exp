<?php

namespace Travel\CategoryBanner\Render;

class BannerRendererDynamic
{
    public static function render(array $data): string
    {
        $id          = esc_attr($data['id'] ?? wp_unique_id('tcb-dyn-'));
        $align       = esc_attr($data['align'] ?? 'full');
        $title       = esc_html($data['title'] ?? '');
        $bg          = esc_url($data['background'] ?? '');
        $description = $data['description'] ?? '';
        $text_link   = $data['text_link'] ?? [];
        $category    = $data['category'] ?? [];

        // === Fallbacks ===
        if (empty($bg)) {
            // ruta corregida al fallback: sube dos niveles desde /Render/
            $bg = plugin_dir_url(__FILE__) . '../Assets/img/default-banner.jpg';
        }

        if (empty($text_link) || !is_array($text_link)) {
            $text_link = [
                'title' => __('Ver más destinos', 'travel-category-banner'),
                'url'   => '#',
            ];
        }

        ob_start(); ?>
        <section class="tcb-banner tcb-banner--dynamic align<?php echo $align; ?>" id="<?php echo $id; ?>">
            <div class="tcb-banner__bg" style="background-image:url('<?php echo $bg; ?>')"></div>
            <div class="tcb-banner__overlay"></div>

            <?php if (!empty($category)): ?>
                <div class="tcb-slider">
                    <button class="tcb-nav tcb-prev" aria-label="Anterior"></button>
                    <button class="tcb-nav tcb-next" aria-label="Siguiente"></button>

                    <div class="swiper tcb-swiper" data-slides="3.5" data-slides-mobile="1.2">
                        <div class="swiper-wrapper">
                            <?php foreach ($category as $cat):
                                $img = esc_url($cat['image'] ?? '');
                                $tt  = esc_html($cat['title'] ?? '');
                                $lnk = esc_url($cat['link'] ?? '#'); ?>
                                <div class="swiper-slide">
                                    <article class="tcb-card">
                                        <a href="<?php echo $lnk; ?>" class="tcb-card__link">
                                            <?php if ($img): ?>
                                                <img src="<?php echo $img; ?>" alt="<?php echo $tt; ?>" class="tcb-card__img" loading="lazy">
                                            <?php endif; ?>
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
                        <?php if ($title): ?>
                            <h2 class="tcb-banner__title"><?php echo $title; ?></h2>
                        <?php endif; ?>

                        <?php if ($description): ?>
                            <p class="tcb-banner__paragraph"><?php echo wp_kses_post($description); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($text_link['url'])): ?>
                        <a href="<?php echo esc_url($text_link['url']); ?>" class="tcb-btn">
                            <?php echo esc_html($text_link['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
