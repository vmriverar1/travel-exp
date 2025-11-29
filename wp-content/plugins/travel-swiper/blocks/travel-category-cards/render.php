<?php
$cta_text = get_field('cta_text') ?: 'View Trip';
$terms = get_field('selected_categories') ?: [];
$rows = get_field('layout_rows') ?: '1';
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_');

if (empty($terms)) return;

error_log('=== Debug Travel Category Cards ===');
error_log('Cantidad de términos (categorías): ' . count($terms));
?>

<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block travel-swiper--category-cards <?php echo esc_attr($rows_class); ?>">
    <div class="tsb-swiper swiper">
        <div class="swiper-wrapper">

            <?php foreach ($terms as $term):
                $term_link = get_term_link($term->term_id, $term->taxonomy);
                if (is_wp_error($term_link)) continue;

                // Prefijo dinámico de la taxonomía
                $taxonomy_prefix = $term->taxonomy . '_' . $term->term_id;

                // Obtener imagen desde el campo ACF de la taxonomía
                $image = get_field('thumbnail', $taxonomy_prefix);
                $image_url = $image['url'] ?? 'https://via.placeholder.com/400x260?text=Destination';

                // Log de depuración
                error_log('Category Term: ' . $term->name . ' | Tax: ' . $term->taxonomy . ' | Field: image | Result: ' . print_r($image, true));
            ?>
                <div class="swiper-slide">
                    <a href="<?php echo esc_url($term_link); ?>" class="travel-card">
                        <div class="travel-card__image">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
                        </div>
                        <div class="travel-card__content">
                            <div class="travel-card__content__info">
                                <h4 class="travel-card__title"><?php echo esc_html($term->name); ?></h4>
                            </div>
                            <span class="travel-card__button"><?php echo esc_html($cta_text); ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- Controles -->
        <div class="swiper-controls">
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination__mobile"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>