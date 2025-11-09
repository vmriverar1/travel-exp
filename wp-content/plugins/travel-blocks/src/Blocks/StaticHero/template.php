<?php
$title = get_field('sh_title') ?: 'Título por defecto';
$subtitle = get_field('sh_subtitle') ?: 'Subtítulo por defecto';
$bg = get_field('sh_background');
$bg_url = is_array($bg) && isset($bg['url']) ? esc_url($bg['url']) : '';
$id = 'static-hero-' . ($block['id'] ?? uniqid());
$class = 'acf-gbr-static-hero align' . ($block['align'] ?? 'wide');
$block_wrapper_attributes = $GLOBALS['sh_block_wrapper_attributes'] ?? '';

// Precarga antes de todo (clave)
add_action('wp_head', function() use ($bg_url) {
  if ($bg_url) {
    echo '<link rel="preload" as="image" href="'.esc_url($bg_url).'" fetchpriority="high" importance="high">';
  }
}, 1);
?>

<div <?php echo $block_wrapper_attributes; ?>>
<section id="<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($class); ?>"
  style="
    background-color:#0d0d0d;
    background-image:url('<?php echo $bg_url; ?>');
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:#fff;
    position:relative;
    overflow:hidden;
  ">
  <div class="acf-gbr-static-hero__overlay" style="
    background:rgba(0,0,0,0.4);
    padding:6rem 2rem;
    z-index:2;
  ">
    <h2 style="font-size:clamp(2rem,5vw,3rem);margin-bottom:0.5rem;"><?php echo esc_html($title); ?></h2>
    <p style="font-size:clamp(1.2rem,3vw,1.6rem);"><?php echo esc_html($subtitle); ?></p>
  </div>
</section>

<noscript>
  <img src="<?php echo $bg_url; ?>" alt="<?php echo esc_attr($title); ?>" width="1920" height="1080">
</noscript>
</div>
