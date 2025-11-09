<?php
$departments = get_field('departments') ?: [];
if (empty($departments)) return;
?>

<section class="vtc-departments-block">
  <div class="vtc-department__swiper swiper">
    <div class="swiper-wrapper">
      <?php foreach ($departments as $department): ?>
        <div class="swiper-slide vtc-department__slide"
             data-department="<?php echo esc_attr(strtolower($department['department_name'])); ?>">
          <?php echo wp_get_attachment_image($department['department_image'], 'large', false, ['loading' => 'lazy']); ?>
          <h3 class="vtc-department__title"><?php echo esc_html($department['department_name']); ?></h3>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="vtc-department__controls">
      <div class="swiper-button-prev vtc-dep-prev"></div>
      <div class="swiper-button-next vtc-dep-next"></div>
    </div>
  </div>
</section>
