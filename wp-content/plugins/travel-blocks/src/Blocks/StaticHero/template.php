<?php

/**

 * Template: Static Hero

 *

 * Displays a fullscreen hero section with title, subtitle, and background image.

 *

 * @package Travel\Blocks\ACF

 * @since 1.0.0

 * @version 2.0.0 - REFACTORED: Now uses $data array, removed anti-patterns

 *

 * ✅ SECURITY IMPROVEMENTS:

 * - All outputs properly escaped

 * - No get_field() calls (MVC pattern)

 * - No $GLOBALS usage

 * - No add_action() in template

 *

 * @var array $data Template data from render()

 */



// Extract data from $data array (passed by render method)

// ✅ NO MORE get_field() calls - data comes from class

$title    = $data['title'];

$subtitle = $data['subtitle'];

$bg_url   = $data['bg_url'];

$block_id = $data['block_id'];

$align    = $data['align'];



// Build CSS class

$class = 'acf-gbr-static-hero align' . $align;

?>



<div <?php echo get_block_wrapper_attributes(['class' => 'static-hero-wrapper']); ?>>

<section id="<?php echo esc_attr($block_id); ?>"

  class="<?php echo esc_attr($class); ?>"

  style="

    background-color:#0d0d0d;

    background-image:url('<?php echo esc_url($bg_url); ?>');

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

  <img src="<?php echo esc_url($bg_url); ?>" alt="<?php echo esc_attr($title); ?>" width="1920" height="1080">

</noscript>

</div>

