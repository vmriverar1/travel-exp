<?php
$rows = get_field('layout_rows') ?: '1';
$items = get_field('cards') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_');
?>

<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block travel-swiper--cards <?php echo esc_attr($rows_class); ?>">
    <div class="tsb-swiper swiper">
        <div class="swiper-wrapper">
            <?php if ($items): ?>
                <?php foreach ($items as $item):
                    $image = $item['image'] ?? '';
                    $link  = $item['link'] ?? null;
                ?>
                    <div class="swiper-slide">
                        <article class="card-item">
                            <?php if ($image): ?>
                                <div class="card-thumb">
                                    <?php echo wp_get_attachment_image($image, 'large', false, ['loading' => 'lazy']); ?>

                                    <?php if ($link && !empty($link['url'])): ?>
                                        <a href="<?php echo esc_url($link['url']); ?>"
                                            class="card-link"
                                            target="<?php echo esc_attr($link['target'] ?: '_self'); ?>">
                                            <?php echo esc_html($link['title'] ?: 'View more'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="swiper-slide">
                        <article class="card-item">
                            <div class="card-thumb">
                                <img src="https://via.placeholder.com/400x260?text=Card+<?php echo $i; ?>" alt="Demo <?php echo $i; ?>">
                                <a href="#" class="card-link">Travel Tips</a>
                            </div>
                        </article>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>

        <div class="swiper-controls">
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination__mobile"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>