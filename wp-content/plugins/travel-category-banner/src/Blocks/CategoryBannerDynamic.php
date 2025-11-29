<?php
namespace Travel\CategoryBanner\Blocks;

use Travel\CategoryBanner\Render\BannerRendererDynamic;

class CategoryBannerDynamic {
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void {
        $term = get_queried_object();

        // Verifica que estemos en una taxonomía
        if (!$term || !isset($term->taxonomy) || $term->taxonomy !== 'destinations') {
            echo '<p style="padding:16px;background:#fff3cd;border:1px solid #ffeeba;">
                ⚠️ Este bloque dinámico debe usarse en una página de taxonomía de tipo "destinations".
            </p>';
            return;
        }

        // === Datos del término actual ===
        $title       = $term->name ?? '';
        $description = function_exists('get_field') ? get_field('content', "{$term->taxonomy}_{$term->term_id}") : '';
        $image       = function_exists('get_field') ? get_field('background_image', "{$term->taxonomy}_{$term->term_id}") : null;
        $bg_url      = is_array($image) && !empty($image['url']) ? $image['url'] : '';

        // === Campo link ACF personalizado (si existe globalmente) ===
        $text_link = function_exists('get_field') ? get_field('text_link', "{$term->taxonomy}_{$term->term_id}") : null;
        if (!is_array($text_link)) {
            $text_link = [
                'title' => $title ?: __('Ver más destinos', 'travel-category-banner'),
                'url'   => get_term_link($term),
            ];
        }

        // === Otras categorías relacionadas ===
        $related_terms = get_terms([
            'taxonomy'   => 'destinations',
            'hide_empty' => false,
            'exclude'    => [$term->term_id],
        ]);

        $cards = [];
        if (!is_wp_error($related_terms) && !empty($related_terms)) {
            foreach ($related_terms as $cat) {
                $thumb = function_exists('get_field') ? get_field('thumbnail', "destinations_{$cat->term_id}") : null;
                $thumb_url = is_array($thumb) && !empty($thumb['url'])
                    ? $thumb['url']
                    : plugin_dir_url(__FILE__) . '../../Assets/img/default-card.jpg';

                $cards[] = [
                    'title' => $cat->name,
                    'image' => $thumb_url,
                    'link'  => get_term_link($cat),
                ];
            }
        }

        // === Render final ===
        echo BannerRendererDynamic::render([
            'id'          => $block['id'] ?? wp_unique_id('tcb-dyn-'),
            'align'       => $block['align'] ?? 'full',
            'title'       => $title,
            'description' => wp_strip_all_tags($description),
            'background'  => $bg_url,
            'text_link'   => $text_link,
            'category'    => $cards,
        ]);
    }
}
