<?php
$rows   = get_field('layout_rows') ?: '1';
$items  = get_field('items') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_');
?>

<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block travel-swiper--checklist <?php echo esc_attr($rows_class); ?>">
    <div class="tsb-swiper swiper">
        <div class="swiper-wrapper">
            <?php if ($items): ?>
                <?php foreach ($items as $s): ?>
                    <div class="swiper-slide">
                        <article class="check-item">
                            <div class="check-item__icon">
                                <svg width="43" height="26" viewBox="0 0 43 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M42.7192 14.0142L42.1446 14.2463C33.946 17.5588 25.7621 20.9074 17.543 24.1666C13.9199 25.6045 10.255 25.5706 6.748 23.71C4.88007 22.718 3.28709 21.3579 2.16836 19.5148C1.7666 18.8513 1.26549 18.2246 0.989938 17.5079C0.0674637 15.1127 -0.268337 12.638 0.231677 10.0869C0.864861 6.85147 2.52911 4.27905 5.17173 2.32721C6.46771 1.38713 7.93864 0.716127 9.49801 0.353655C12.7376 -0.432732 15.8114 0.115291 18.7263 1.76169C20.4641 2.74369 22.2715 3.5993 24.0574 4.50032C26.4631 5.71877 28.8758 6.92251 31.281 8.14226C35.0371 10.0483 38.7912 11.9584 42.5434 13.8725C42.6051 13.9157 42.6639 13.963 42.7192 14.0142Z" fill="#F3CE72" />
                                </svg>
                            </div>
                            <div class="check-item__content">
                                <?php if (!empty($s['title'])): ?>
                                    <h4 class="check-title"><?php echo esc_html($s['title']); ?></h4>
                                <?php endif; ?>

                                <?php if (!empty($s['description'])): ?>
                                    <p class="check-desc"><?php echo esc_html($s['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 👇 Demo por defecto (5 items) -->
                <?php
                $demo = [
                    ['title' => 'Do I need a permit for the Inca Trail?', 'text' => 'Yes. This trail is regulated, and permits are limited to 500 per day. Booking months in advance is highly recommended.'],
                    ['title' => 'What should I pack?', 'text' => 'Lightweight clothing, hiking boots, a rain jacket, sunscreen, insect repellent, and reusable water bottles are essential.'],
                    ['title' => 'When is the best time to hike?', 'text' => 'May to September is the dry season — ideal for hiking. The trail closes in February for maintenance.'],
                    ['title' => 'How difficult is the trek?', 'text' => 'Moderate difficulty due to altitude and long distances. Good preparation and acclimatization are key.'],
                    ['title' => 'What is included in the tour?', 'text' => 'All packages include guides, porters, meals, camping equipment, and transport to Machu Picchu.'],
                    ['title' => 'What is included in the tour?', 'text' => 'All packages include guides, porters, meals, camping equipment, and transport to Machu Picchu.'],
                    ['title' => 'What is included in the tour?', 'text' => 'All packages include guides, porters, meals, camping equipment, and transport to Machu Picchu.'],
                    ['title' => 'What is included in the tour?', 'text' => 'All packages include guides, porters, meals, camping equipment, and transport to Machu Picchu.'],

                ];
                foreach ($demo as $item): ?>
                    <div class="swiper-slide">
                        <article class="check-item">
                            <div class="check-item__icon">
                                <svg width="43" height="26" viewBox="0 0 43 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M42.7192 14.0142L42.1446 14.2463C33.946 17.5588 25.7621 20.9074 17.543 24.1666C13.9199 25.6045 10.255 25.5706 6.748 23.71C4.88007 22.718 3.28709 21.3579 2.16836 19.5148C1.7666 18.8513 1.26549 18.2246 0.989938 17.5079C0.0674637 15.1127 -0.268337 12.638 0.231677 10.0869C0.864861 6.85147 2.52911 4.27905 5.17173 2.32721C6.46771 1.38713 7.93864 0.716127 9.49801 0.353655C12.7376 -0.432732 15.8114 0.115291 18.7263 1.76169C20.4641 2.74369 22.2715 3.5993 24.0574 4.50032C26.4631 5.71877 28.8758 6.92251 31.281 8.14226C35.0371 10.0483 38.7912 11.9584 42.5434 13.8725C42.6051 13.9157 42.6639 13.963 42.7192 14.0142Z" fill="#F3CE72" />
                                </svg>
                            </div>
                            <div class="check-item__content">
                                <h4 class="check-title"><?php echo esc_html($item['title']); ?></h4>
                                <p class="check-desc"><?php echo esc_html($item['text']); ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Solo visible en desktop (parte del grid) -->
            <div class="swiper-slide checklist-bottom desktop-only">
                <?php if ($img_id = get_field('bottom_image')): ?>
                    <?php echo wp_get_attachment_image($img_id, 'medium', false, ['class' => 'checklist-img']); ?>
                <?php else: ?>
                    <img src="https://via.placeholder.com/220x160?text=Checklist+Demo" alt="Checklist Demo">
                <?php endif; ?>

                <?php
                $cta_text = get_field('bottom_cta') ?: 'Check out this checklist.';
                $cta_link = get_field('bottom_link') ?: '#';
                ?>
                <a href="<?php echo esc_url($cta_link); ?>" class="checklist-button"><?php echo esc_html($cta_text); ?></a>
            </div>
        </div>

        <!-- Controles -->
        <div class="swiper-controls">
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination__mobile"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    <!-- Solo visible en mobile (fuera del swiper) -->
    <div class="checklist-bottom mobile-only">
        <?php if ($img_id = get_field('bottom_image')): ?>
            <?php echo wp_get_attachment_image($img_id, 'medium', false, ['class' => 'checklist-img']); ?>
        <?php else: ?>
            <img src="https://via.placeholder.com/220x160?text=Checklist+Demo" alt="Checklist Demo">
        <?php endif; ?>
        <a href="<?php echo esc_url($cta_link); ?>" class="checklist-button"><?php echo esc_html($cta_text); ?></a>
    </div>
</section>