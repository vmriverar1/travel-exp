<?php
/**
 * Template: Posts List Advanced (Editorial Grid + Swiper Mobile)
 */

$posts_per_page = (int)(get_field('pla_posts_per_page') ?: 6);
$args = [
  'post_type'      => 'post',
  'posts_per_page' => $posts_per_page,
  'no_found_rows'  => true,
];
$q = new \WP_Query($args);
$block_id = 'pla-' . ($block['id'] ?? uniqid());
$block_wrapper_attributes = $GLOBALS['pla_block_wrapper_attributes'] ?? '';
?>

<div <?php echo $block_wrapper_attributes; ?>>
<section id="<?php echo esc_attr($block_id); ?>"
         class="acf-gbr-pla"
         data-pla-enable-mobile="<?php echo esc_attr((int)get_field('pla_enable_swiper_mobile')); ?>">
  <div class="pla-grid">
    <?php if ($q->have_posts()): ?>
      <div class="pla-row">
        <?php while ($q->have_posts()): $q->the_post();
          $thumb = has_post_thumbnail()
            ? get_the_post_thumbnail_url(get_the_ID(), 'large')
            : get_template_directory_uri() . '/assets/img/placeholder.webp';
          $category = get_the_category();
          $cat_name = !empty($category) ? esc_html($category[0]->name) : '';
        ?>
          <article class="pla-card" style="background-image:url('<?php echo esc_url($thumb); ?>');">
            <?php if ($cat_name): ?>
              <span class="pla-category"><?php echo $cat_name; ?></span>
            <?php endif; ?>
            <div class="pla-content">
              <div class="pla-text">
                <h3 class="pla-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="pla-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
              </div>
              <a href="<?php the_permalink(); ?>" class="pla-readmore">Read More</a>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    <?php else: ?>
      <p>No hay resultados.</p>
    <?php endif; ?>
  </div>
</section>
</div>
