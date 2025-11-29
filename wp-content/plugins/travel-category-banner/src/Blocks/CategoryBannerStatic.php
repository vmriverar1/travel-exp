<?php
namespace Travel\CategoryBanner\Blocks;

use Travel\CategoryBanner\Render\BannerRendererStatic;

class CategoryBannerStatic {
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void {
        $use_dynamic = function_exists('get_field') ? get_field('use_dynamic') : false;

        // === Modo dinámico dentro del bloque estático ===
        if ($use_dynamic && is_tax()) {
            $term = get_queried_object();
            if (!$term || !isset($term->taxonomy)) {
                echo '<p style="padding:16px;background:#fff3cd;border:1px solid #ffeeba;">
                    ⚠️ No se detectó una categoría válida para mostrar dinámicamente.
                </p>';
                return;
            }

            $title       = $term->name ?? '';
            $description = term_description($term) ?: '';
            $bg          = function_exists('get_field') ? get_field('background_image', "{$term->taxonomy}_{$term->term_id}") : null;
            $bg_url      = is_array($bg) && !empty($bg['url']) ? $bg['url'] : '';
            $text_link   = function_exists('get_field') ? get_field('text_link', "{$term->taxonomy}_{$term->term_id}") : null;

            if (!is_array($text_link)) {
                $text_link = [
                    'title' => $title ?: __('Ver más paquetes', 'travel-category-banner'),
                    'url'   => get_term_link($term),
                ];
            }

            echo BannerRendererStatic::render([
                'id'          => $block['id'] ?? wp_unique_id('tcb-'),
                'align'       => $block['align'] ?? 'full',
                'title'       => $title,
                'description' => wp_strip_all_tags($description),
                'background'  => $bg_url,
                'text_link'   => $text_link,
                'packages'    => [],
            ]);
            return;
        }

        // === Modo manual ===
        $title       = function_exists('get_field') ? get_field('title') : '';
        $description = function_exists('get_field') ? get_field('description') : '';
        $button_text = function_exists('get_field') ? (get_field('button_text') ?: __('Ver más paquetes', 'travel-category-banner')) : __('Ver más paquetes', 'travel-category-banner');
        $bg          = function_exists('get_field') ? get_field('background_image') : null;
        $bg_url      = is_array($bg) && !empty($bg['url']) ? $bg['url'] : '';

        $packages_source = function_exists('get_field') ? get_field('packages_source') : 'offers_all';
        $packages = [];

        if ($packages_source === 'manual') {
            $rows = function_exists('get_field') ? get_field('packages') : [];
            if ($rows) {
                foreach ($rows as $row) {
                    $img  = $row['image'] ?? null;
                    $link = $row['link'] ?? null;
                    $packages[] = [
                        'title' => $row['title'] ?? '',
                        'image' => is_array($img) ? ($img['url'] ?? '') : '',
                        'link'  => is_array($link) ? ($link['url'] ?? '#') : '#',
                    ];
                }
            }
        } else {
            // === Modo global ===
            $q = new \WP_Query([
                'post_type'      => 'package',
                'posts_per_page' => 6,
            ]);
            if ($q->have_posts()) {
                while ($q->have_posts()) {
                    $q->the_post();
                    $packages[] = [
                        'title' => get_the_title(),
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                        'link'  => get_permalink(),
                    ];
                }
                wp_reset_postdata();
            }
        }

        echo BannerRendererStatic::render([
            'id'          => $block['id'] ?? wp_unique_id('tcb-'),
            'align'       => $block['align'] ?? 'full',
            'title'       => $title,
            'description' => $description,
            'background'  => $bg_url,
            'button_text' => $button_text,
            'packages'    => $packages,
        ]);
    }
}
