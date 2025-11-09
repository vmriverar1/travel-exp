<?php
$rows   = get_field('layout_rows') ?: '1';
$slides = get_field('slides') ?: [];
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_');
$cta_text = get_field('bottom_cta');
$cta_link = get_field('bottom_link');
?>

<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block travel-swiper--sustain <?php echo esc_attr($rows_class); ?>">
    <div class="tsb-swiper swiper">
        <div class="swiper-wrapper">
            <?php if ($slides): ?>
                <?php foreach ($slides as $index => $s): ?>
                    <div class="swiper-slide sustain-slide <?php echo $index === 1 ? 'sustain-slide--middle' : ''; ?>">
                        <article class="sustain-item">
                            <?php if (!empty($s['icon'])): ?>
                                <div class="sustain-icon">
                                    <?php echo wp_get_attachment_image($s['icon'], 'thumbnail', false, ['alt' => 'Icon']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($s['title'])): ?>
                                <h3 class="sustain-title"><?php echo esc_html($s['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($s['description'])): ?>
                                <div class="sustain-desc"><?php echo wp_kses_post($s['description']); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($s['image'])): ?>
                                <div class="sustain-img">
                                    <?php echo wp_get_attachment_image($s['image'], 'large', false); ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <!-- Demo -->
                <?php
                $demo = [
                    [
                        'title' => 'People & Communities',
                        'description' => 'Porters, cooks, and guides from local villages are at the heart of every journey. We ensure fair wages, training, and long-term opportunities that strengthen families and preserve traditions.',
                        'image' => 'https://via.placeholder.com/500x300?text=People',
                    ],
                    [
                        'title' => 'Culture & Heritage',
                        'description' => 'Travel sustains more than landscapes. By working with indigenous communities, we help preserve ancestral knowledge, crafts, and celebrations.',
                        'image' => 'https://via.placeholder.com/500x300?text=Culture',
                    ],
                    [
                        'title' => 'Nature & Environment',
                        'description' => 'Our treks respect the land. We reduce waste, promote low-impact travel, and ensure Andean ecosystems thrive for future generations.',
                        'image' => 'https://via.placeholder.com/500x300?text=Nature',
                    ],
                ];
                foreach ($demo as $index => $item): ?>
                    <div class="swiper-slide sustain-slide <?php echo $index === 1 ? 'sustain-slide--middle' : ''; ?>">
                        <article class="sustain-item">
                            <h3 class="sustain-title"><?php echo esc_html($item['title']); ?></h3>
                            <p class="sustain-desc"><?php echo esc_html($item['description']); ?></p>
                            <div class="sustain-img">
                                <img src="<?php echo esc_url($item['image']); ?>" alt="">
                            </div>

                            <?php if ($index === 1 && $cta_text): ?>
                                <div class="sustain-cta-container desktop-only">
                                    <a href="<?php echo esc_url($cta_link ?: '#'); ?>" class="sustain-cta-btn">
                                        <?php echo esc_html($cta_text); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Botón arriba del slider -->
        <?php if ($cta_text): ?>
            <div class="sustain-cta-container">
                <a href="<?php echo esc_url($cta_link ?: '#'); ?>" class="sustain-cta-btn">
                    <?php echo esc_html($cta_text); ?>
                </a>
            </div>
        <?php endif; ?>

        <!-- Controles -->
        <div class="swiper-controls">
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination__mobile"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
    </div>
</section>