<?php
namespace Travel\CategoryBanner\Blocks;

use Travel\CategoryBanner\Render\BannerRenderer;

class CategoryBannerStatic {
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void {
        $title       = function_exists('get_field') ? get_field('title') : '';
        $description = function_exists('get_field') ? get_field('description') : '';
        $button_text = function_exists('get_field') ? (get_field('button_text') ?: __('See more', 'travel-category-banner')) : __('See more', 'travel-category-banner');
        $bg          = function_exists('get_field') ? get_field('background_image') : null;
        $bg_url      = is_array($bg) && !empty($bg['url']) ? $bg['url'] : '';
        $logo        = function_exists('get_field') ? get_field('logo') : null;
        $logo_url    = is_array($logo) && !empty($logo['url']) ? $logo['url'] : '';

        $packages_source = function_exists('get_field') ? get_field('packages_source') : 'offers_all';
        $packages = [];

        if ($packages_source === 'manual') {
            $rows = function_exists('get_field') ? get_field('packages') : [];
            if ($rows) {
                foreach ($rows as $row) {
                    $img = $row['image'] ?? null;
                    $link = $row['link'] ?? null;
                    $packages[] = [
                        'title' => $row['title'] ?? '',
                        'image' => is_array($img) ? ($img['url'] ?? '') : '',
                        'link'  => is_array($link) ? ($link['url'] ?? '#') : '#',
                    ];
                }
            }
        } else {
            // Ofertas globales sin filtrar
            $q = new \WP_Query([
                'post_type'      => 'package',
                'posts_per_page' => 6,
                // 'meta_query'     => [
                //     [
                //         'key'     => 'featured_package',
                //         'value'   => true,
                //         'compare' => '=',
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
        }

        echo BannerRenderer::render([
            'id'          => $block['id'] ?? wp_unique_id('tcb-'),
            'align'       => $block['align'] ?? 'full',
            'title'       => $title,
            'description' => $description,
            'background'  => $bg_url,
            'logo'        => $logo_url,
            'button_text' => $button_text,
            'button_url'  => '#',
            'packages'    => $packages,
        ]);
    }
}
