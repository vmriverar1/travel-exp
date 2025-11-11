<?php
/**
 * Travel Content Kit Theme functions and definitions
 *
 * @package Travel Content Kit
 * @since 1.0.0
 */

/**
 * Disable block-based templates (FSE) to use classic PHP templates
 */
// add_action('after_setup_theme', function() {
//     remove_theme_support('block-templates');
//     remove_theme_support('block-template-parts');
// }, 11);

/**
 * Disable page caching for development
 * Remove this in production if using cache
 */
add_action('send_headers', function() {
    if (!is_user_logged_in()) {
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Cache-Bypass: true');
    }
});

/**
 * Register Navigation Menus
 */
function travel_register_menus() {
    register_nav_menus([
        // Header menus
        'primary' => __('Primary Menu (Desktop)', 'travel'),
        'secondary' => __('Secondary Menu (Main Pages)', 'travel'),
        'aside' => __('Aside Menu (Mobile)', 'travel'),
        'aside-secondary' => __('Aside Secondary Links', 'travel'),

        // Footer menus
        'footer-top-experiences' => __('Footer - Top Experiences', 'travel'),
        'footer-treks-adventure' => __('Footer - Treks & Adventure', 'travel'),
        'footer-culture-history' => __('Footer - Culture & History', 'travel'),
        'footer-destinations' => __('Footer - Destinations', 'travel'),
        'footer-about' => __('Footer - About Machu Picchu Peru', 'travel'),
        'footer-extra-info' => __('Footer - Extra Information', 'travel'),
        'footer-legal' => __('Footer - Legal Links', 'travel'),
    ]);
}
add_action('after_setup_theme', 'travel_register_menus');

/**
 * Enqueue Styles and Scripts
 */
add_action( 'wp_enqueue_scripts', function() {
    // Force cache bypass with unique version per request
    $version = 'v' . date('YmdHis') . '-' . rand(1000, 9999);

    // Parent theme style
    wp_enqueue_style( 'travel-style', get_stylesheet_uri() );

    // Global CSS Variables
    wp_enqueue_style(
        'travel-global',
        get_template_directory_uri() . '/assets/css/global.css',
        [],
        $version
    );

    // Atoms - Individual files (bypass @import issue)
    wp_enqueue_style('travel-atoms-button-close', get_template_directory_uri() . '/assets/css/atoms/button-close.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-button-hamburger', get_template_directory_uri() . '/assets/css/atoms/button-hamburger.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-logo', get_template_directory_uri() . '/assets/css/atoms/logo.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-logo-footer', get_template_directory_uri() . '/assets/css/atoms/logo-footer.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-nav-link', get_template_directory_uri() . '/assets/css/atoms/nav-link.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-payment-icon', get_template_directory_uri() . '/assets/css/atoms/payment-icon.css', ['travel-global'], $version);
    wp_enqueue_style('travel-atoms-social-icon', get_template_directory_uri() . '/assets/css/atoms/social-icon.css', ['travel-global'], $version);

    // Molecules - Individual files (bypass @import issue)
    wp_enqueue_style('travel-molecules-contact-info', get_template_directory_uri() . '/assets/css/molecules/contact-info.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-footer-company-info', get_template_directory_uri() . '/assets/css/molecules/footer-company-info.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-footer-legal-bar', get_template_directory_uri() . '/assets/css/molecules/footer-legal-bar.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-footer-map', get_template_directory_uri() . '/assets/css/molecules/footer-map.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-nav-aside', get_template_directory_uri() . '/assets/css/molecules/nav-aside.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-nav-footer-column', get_template_directory_uri() . '/assets/css/molecules/nav-footer-column.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-nav-main', get_template_directory_uri() . '/assets/css/molecules/nav-main.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-nav-secondary', get_template_directory_uri() . '/assets/css/molecules/nav-secondary.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-payment-methods', get_template_directory_uri() . '/assets/css/molecules/payment-methods.css', ['travel-global'], $version);
    wp_enqueue_style('travel-molecules-social-media-bar', get_template_directory_uri() . '/assets/css/molecules/social-media-bar.css', ['travel-global'], $version);

    // Organisms - Individual files (bypass @import issue)
    wp_enqueue_style('travel-organisms-header', get_template_directory_uri() . '/assets/css/organisms/header.css', ['travel-global'], $version);
    wp_enqueue_style('travel-organisms-footer-main', get_template_directory_uri() . '/assets/css/organisms/footer-main.css', ['travel-global'], $version);

    // Utilities
    wp_enqueue_style(
        'travel-utilities',
        get_template_directory_uri() . '/assets/css/utilities.css',
        ['travel-global'],
        $version
    );

    // Package Layout (single-package.html template)
    if (is_singular('package')) {
        wp_enqueue_style(
            'travel-package-layout',
            get_template_directory_uri() . '/assets/css/package-layout.css',
            ['travel-global'],
            $version
        );
    }

    // JavaScript - Defer loading
    wp_enqueue_script(
        'travel-header',
        get_template_directory_uri() . '/assets/js/organisms/header.js',
        [],
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'travel-nav-aside',
        get_template_directory_uri() . '/assets/js/molecules/nav-aside.js',
        [],
        '1.0.0',
        true
    );

    // Footer JavaScript
    wp_enqueue_script(
        'travel-nav-footer-column',
        get_template_directory_uri() . '/assets/js/molecules/nav-footer-column.js',
        [],
        '1.0.0',
        true
    );
});


add_filter('script_loader_tag', function ($tag, $handle) {
    if (strpos($handle, 'acf-gbr-posts-list-advanced-loader') !== false) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);


/**
 * Convierte una imagen a WebP (si no existe) y devuelve su URL optimizada.
 */
function convert_to_webp_if_possible($image_url) {
    if (!$image_url) return '';

    $path = str_replace(home_url('/'), ABSPATH, $image_url);
    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);
    $webp_url  = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_url);

    // Si ya existe el .webp, usarlo directamente
    if (file_exists($webp_path)) {
        return $webp_url;
    }

    // Si no existe, intentar generarlo
    if (function_exists('imagewebp') && file_exists($path)) {
        $info = getimagesize($path);
        if ($info) {
            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $img = imagecreatefromjpeg($path);
                    break;
                case 'image/png':
                    $img = imagecreatefrompng($path);
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    break;
                default:
                    return $image_url;
            }
            // Guardar en WebP con calidad 80
            imagewebp($img, $webp_path, 80);
            imagedestroy($img);
            return $webp_url;
        }
    }

    // Fallback original
    return $image_url;
}

/**
 * Include ACF Options Pages
 * Load at 'init' to avoid textdomain warnings
 *
 * DISABLED: Header and Footer options now consolidated in Global Options (plugin)
 */
// add_action('init', function() {
//     require_once get_template_directory() . '/inc/acf-options.php';
//     require_once get_template_directory() . '/inc/acf-footer-options.php';
// }, 5);

/**
 * Helper function to get header option with fallback
 *
 * @param string $field_name ACF field name
 * @param string $fallback Fallback value if field is empty
 * @return string Field value or fallback
 */
function get_header_option($field_name, $fallback = '') {
    if (function_exists('get_field')) {
        $value = get_field($field_name, 'option');
        return !empty($value) ? $value : $fallback;
    }
    return $fallback;
}

/**
 * Get social media icon SVG by type
 *
 * @param string $type Icon type (facebook, instagram, etc.)
 * @return string SVG markup
 */
function get_social_icon_svg($type) {
    $icons = [
        'facebook' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'pinterest' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>',
        'youtube' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.10-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'twitter' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'linkedin' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        'whatsapp' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
    ];

    return $icons[$type] ?? '';
}

// === Legacy Twenty Twenty-Four Code (kept for compatibility) ===

/**
 * Register block styles.
 */
if ( ! function_exists( 'twentytwentyfour_block_styles' ) ) :
	function twentytwentyfour_block_styles() {
		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __( 'Arrow icon', 'twentytwentyfour' ),
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __( 'Pill', 'twentytwentyfour' ),
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfour' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __( 'With arrow', 'twentytwentyfour' ),
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
		register_block_style(
			'core/heading',
			array(
				'name'         => 'asterisk',
				'label'        => __( 'With asterisk', 'twentytwentyfour' ),
				'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}

				.is-style-asterisk:empty:before {
					content: none;
				}

				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}

				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}

				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}

				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_styles' );

/**
 * Enqueue block stylesheets.
 */
if ( ! function_exists( 'twentytwentyfour_block_stylesheets' ) ) :
	function twentytwentyfour_block_stylesheets() {
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'twentytwentyfour-button-style-outline',
				'src'    => get_template_directory_uri() . '/assets/css/button-outline.css',
				'ver'    => wp_get_theme( get_template() )->get( 'Version' ),
				'path'   => get_template_directory() . '/assets/css/button-outline.css',
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_stylesheets' );

/**
 * Register pattern categories.
 */
if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	function twentytwentyfour_pattern_categories() {
		register_block_pattern_category(
			'twentytwentyfour_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'twentytwentyfour' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfour' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_pattern_categories' );

// Load vendor autoload if exists
if (file_exists(get_template_directory() . '/vendor/autoload.php')) {
    require_once get_template_directory() . '/vendor/autoload.php';
    if (class_exists('\ValenciaTravel\Theme\Init')) {
        (new \ValenciaTravel\Theme\Init())->register();
    }
}


// === Shortcode para renderizar el HEADER completo ===
add_shortcode('mi_header', function() {
    ob_start();

    // Incluimos el header completo desde archivo o inline (según tu estructura)
    // Opción 1: Si tu header está en un archivo header.php (mejor práctica)
    get_template_part('header');

    // Opción 2 (si quieres usar el código completo inline como el tuyo)
    /*
    ?>
    <header class="header" id="header">
        <?php get_template_part('parts/atoms/logo'); ?>
        <?php get_template_part('parts/header/navigation'); ?>
        <?php get_template_part('parts/header/navigation-aside'); ?>
        <!-- aquí puedes copiar todo tu HTML -->
    </header>
    <?php
    */

    return ob_get_clean();
});


// === Shortcode para renderizar el FOOTER completo ===
add_shortcode('mi_footer', function() {
    ob_start();

    // Cerramos el <main> y cargamos el footer principal
    ?>
    </main><!-- #main -->

    <?php get_template_part('parts/organisms/footer-main'); ?>

    </div><!-- #page -->

    <?php wp_footer(); ?>
    </body>
    </html>
    <?php

    return ob_get_clean();
});

// add_action('init', function() {
//   register_block_pattern_category(
//     'travel-templates',
//     ['label' => __('Plantillas Travel', 'travel')]
//   );
// });

// add_action('save_post_post', function($post_id, $post, $update) {
//   if ($update || wp_is_post_revision($post_id)) return;

//   // Inserta el patrón automáticamente
//   $pattern = '<!-- wp:pattern {"slug":"travel/blog-base"} /-->';

//   wp_update_post([
//     'ID' => $post_id,
//     'post_content' => $pattern,
//   ]);
// }, 10, 3);

// Permitir subir SVGs
function attach_allow_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'attach_allow_svg_uploads');

// Asegurar previsualización segura en el admin
function attach_fix_svg_preview() {
    echo '<style>
        img[src$=".svg"] {
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'attach_fix_svg_preview');



/**
 * Auto background for Cover block from ACF taxonomy image
 */
add_filter('render_block', function ($block_content, $block) {

    // Solo para el bloque Cover
    if ($block['blockName'] !== 'core/cover') {
        return $block_content;
    }

    // Solo si estamos viendo una taxonomía "destinations"
    if (!is_tax('destinations')) {
        return $block_content;
    }

    $term = get_queried_object();
    if (!$term) {
        return $block_content;
    }

    // Obtener el campo ACF 'image' del término actual
    $image = get_field('image', $term);
    if (empty($image) || empty($image['url'])) {
        return $block_content;
    }

    // Reemplazar la URL del background del bloque
    $new_url = esc_url($image['url']);

    // Cambia la URL existente en el HTML del bloque
    $block_content = preg_replace(
        '/url\([^\)]+\)/',
        'url(' . $new_url . ')',
        $block_content
    );

    return $block_content;

}, 10, 2);
