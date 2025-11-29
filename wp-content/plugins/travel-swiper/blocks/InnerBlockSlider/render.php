<?php if (!defined('ABSPATH')) exit;

$rows   = get_field('layout_rows') ?: '1';
$rows_class = 'swiper-rows-' . intval($rows);
$uniq_id = uniqid('tsb_');

?>

<section id="<?php echo esc_attr($uniq_id); ?>" class="travel-swiper-block <?php echo esc_attr($rows_class); ?> travel-innerblocks-block">

  <div class="tsb-swiper swiper">

    <InnerBlocks
      template="<?php echo esc_attr(wp_json_encode([
                  [
                    'core/group',
                    [
                      'className' => 'swiper-wrapper',
                      'layout' => [
                        'type' => 'flex',
                        'flexWrap' => 'nowrap',
                        'orientation' => 'horizontal'
                      ]
                    ],
                    [
                      [
                        'core/group',
                        [
                          'className' => 'swiper-slide',
                        ],
                        []
                      ],
                      [
                        'core/group',
                        [
                          'className' => 'swiper-slide',
                        ],
                        []
                      ],
                      [
                        'core/group',
                        [
                          'className' => 'swiper-slide',
                        ],
                        []
                      ]
                    ]
                  ]
                ])); ?>"
      allowedBlocks="<?php echo esc_attr(wp_json_encode([
                        'core/group',
                        'core/columns',
                        'core/column',
                        'core/paragraph',
                        'core/image',
                        'core/heading',
                        'core/button'
                      ])); ?>"

      templateLock="false" />

    <div class="swiper-controls">
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination__mobile"></div>
      <div class="swiper-button-next"></div>
    </div>

  </div>

</section>