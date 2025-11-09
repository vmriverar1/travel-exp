<?php
namespace Travel\CategoryBanner\Blocks;

use Travel\CategoryBanner\Render\BannerRenderer;

class CategoryBannerDynamic {
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void {
        $term = get_queried_object();
        if (!$term || !isset($term->taxonomy)) {
            echo '<p style="padding:16px;background:#fff3cd;border:1px solid #ffeeba;">Este bloque dinámico debe usarse en una página de taxonomía.</p>';
            return;
        }

        $title       = $term->name ?? '';
        $description = term_description($term) ?: '';
        $bg          = function_exists('get_field') ? get_field('image', "{$term->taxonomy}_{$term->term_id}") : null;
        $bg_url      = is_array($bg) && !empty($bg['url']) ? $bg['url'] : '';
        $logo        = function_exists('get_field') ? get_field('logo') : null;
        $logo_url    = is_array($logo) && !empty($logo['url']) ? $logo['url'] : '';

        // Query de paquetes: ofertas relacionadas al término mediante ACF 'destination' (LIKE con term_id)
        $packages = [];
        $q = new \WP_Query([
            'post_type'      => 'package',
            'posts_per_page' => -1,
            // 'meta_query'     => [
            //     [
            //         'key'     => 'featured_package',
            //         'value'   => true,
            //         'compare' => '=',
            //     ],
            //     [
            //         'key'     => 'destination',
            //         'value'   => '"' . $term->term_id . '"',
            //         'compare' => 'LIKE',
            //     ]
            // ]
        ]);
        if ($q->have_posts()) {
            while ($q->have_posts()) { $q->the_post();
                $packages[] = [
                    'title' => get_the_title(),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                    'link'  => get_permalink(),
                ];
            }
            wp_reset_postdata();
        }

        echo BannerRenderer::render([
            'id'          => $block['id'] ?? wp_unique_id('tcb-'),
            'align'       => $block['align'] ?? 'full',
            'title'       => $title,
            'description' => wp_strip_all_tags($description),
            'background'  => $bg_url,
            'logo'        => $logo_url,
            'button_text' => $title,
            'button_url'  => get_term_link($term),
            'packages'    => $packages,
        ]);
    }
}
