<?php
namespace Travel\CategoryBanner\Render;

class BannerRendererStatic {
    public static function render(array $data): string {
        $id       = esc_attr($data['id'] ?? wp_unique_id('tcb-sta-'));
        $align    = esc_attr($data['align'] ?? 'full');
        $title    = esc_html($data['title'] ?? '');
        $description = esc_html($data['description'] ?? '');
        $bg       = esc_url($data['background'] ?? '');
        $btn_text = esc_html($data['button_text'] ?? '');
        $btn_url  = esc_url($data['button_url'] ?? '#');
        $packages = $data['packages'] ?? [];

        // Fallbacks
        if (empty($bg)) {
            $bg = plugin_dir_url(__FILE__) . '../../assets/img/default-banner.jpg';
        }
        if (empty($btn_text)) {
            $btn_text = __('Ver más paquetes', 'travel-category-banner');
            $btn_url  = '#';
        }

        ob_start(); ?>
        <section class="tcb-banner tcb-banner--static align<?php echo $align; ?>" id="<?php echo $id; ?>">
            <div class="tcb-banner__bg" style="background-image:url('<?php echo $bg; ?>')"></div>
            <div class="tcb-banner__overlay"></div>

            <?php if (!empty($packages)): ?>
                <div class="tcb-slider">
                    <button class="tcb-nav tcb-prev" aria-label="Anterior"></button>
                    <button class="tcb-nav tcb-next" aria-label="Siguiente"></button>
                    <div class="swiper tcb-swiper" data-slides="3.5" data-slides-mobile="1.2">
                        <div class="swiper-wrapper">
                            <?php foreach ($packages as $pkg):
                                $img = esc_url($pkg['image'] ?? '');
                                $tt  = esc_html($pkg['title'] ?? '');
                                $lnk = esc_url($pkg['link'] ?? '#');
                                if (empty($img)) $img = plugin_dir_url(__FILE__) . '../../assets/img/default-card.jpg'; ?>
                                <div class="swiper-slide">
                                    <article class="tcb-card">
                                        <a href="<?php echo $lnk; ?>" class="tcb-card__link">
                                            <img src="<?php echo $img; ?>" alt="<?php echo $tt; ?>" class="tcb-card__img" loading="lazy">
                                            <span class="tcb-card__title"><?php echo $tt ?: __('Sin título', 'travel-category-banner'); ?></span>
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
                    <?php if ($title): ?><h2 class="tcb-banner__title"><?php echo $title; ?></h2><?php endif; ?>
                    <?php if ($description): ?><p class="tcb-banner__paragraph"><?php echo nl2br($description); ?></p><?php endif; ?>
                    <a href="<?php echo $btn_url; ?>" class="tcb-btn"><?php echo $btn_text; ?></a>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
