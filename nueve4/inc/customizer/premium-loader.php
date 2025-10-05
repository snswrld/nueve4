<?php
/**
 * Premium Customizer Loader
 * Loads the premium customizer implementation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the premium customizer
require_once get_template_directory() . '/inc/customizer/premium-customizer.php';

/**
 * Initialize premium customizer features
 */
function nueve4_init_premium_customizer() {
	// Ensure assets directory exists
	$assets_dir = get_template_directory() . '/assets';
	$js_dir = $assets_dir . '/js';
	$css_dir = $assets_dir . '/css';
	
	if ( ! file_exists( $assets_dir ) ) {
		wp_mkdir_p( $assets_dir );
	}
	if ( ! file_exists( $js_dir ) ) {
		wp_mkdir_p( $js_dir );
	}
	if ( ! file_exists( $css_dir ) ) {
		wp_mkdir_p( $css_dir );
	}
	
	// Initialize the premium customizer
	new Nueve4_Premium_Customizer();
}

add_action( 'after_setup_theme', 'nueve4_init_premium_customizer' );

/**
 * Add theme support for premium features
 */
function nueve4_premium_theme_support() {
	// Add theme support for custom logo
	add_theme_support( 'custom-logo', [
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => [ 'site-title', 'site-description' ],
	]);
	
	// Add theme support for custom background
	add_theme_support( 'custom-background', [
		'default-color' => 'ffffff',
		'default-image' => '',
	]);
	
	// Add theme support for post thumbnails
	add_theme_support( 'post-thumbnails' );
	
	// Add theme support for HTML5
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	]);
	
	// Add theme support for selective refresh
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	// Add theme support for responsive embeds
	add_theme_support( 'responsive-embeds' );
	
	// Add theme support for editor styles
	add_theme_support( 'editor-styles' );
	
	// Add theme support for wide alignment
	add_theme_support( 'align-wide' );
	
	// Add theme support for block styles
	add_theme_support( 'wp-block-styles' );
}

add_action( 'after_setup_theme', 'nueve4_premium_theme_support' );

/**
 * Register widget areas
 */
function nueve4_premium_widgets_init() {
	// Primary sidebar
	register_sidebar( [
		'name'          => __( 'Primary Sidebar', 'nueve4' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'nueve4' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	]);
	
	// Footer widget areas
	$footer_columns = get_theme_mod( 'nueve4_footer_columns', 4 );
	for ( $i = 1; $i <= $footer_columns; $i++ ) {
		register_sidebar( [
			'name'          => sprintf( __( 'Footer %d', 'nueve4' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => sprintf( __( 'Add widgets here to appear in footer column %d.', 'nueve4' ), $i ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]);
	}
}

add_action( 'widgets_init', 'nueve4_premium_widgets_init' );

/**
 * Enqueue premium scripts and styles
 */
function nueve4_premium_scripts() {
	// Main theme stylesheet
	wp_enqueue_style( 'nueve4-style', get_stylesheet_uri(), [], '1.0.0' );
	
	// Premium features stylesheet
	wp_enqueue_style( 'nueve4-premium', get_template_directory_uri() . '/assets/css/premium.css', [ 'nueve4-style' ], '1.0.0' );
	
	// Main theme script
	wp_enqueue_script( 'nueve4-script', get_template_directory_uri() . '/assets/js/theme.js', [ 'jquery' ], '1.0.0', true );
	
	// Premium features script
	wp_enqueue_script( 'nueve4-premium', get_template_directory_uri() . '/assets/js/premium.js', [ 'nueve4-script' ], '1.0.0', true );
	
	// Localize script
	wp_localize_script( 'nueve4-premium', 'nueve4Ajax', [
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'nueve4_nonce' ),
	]);
	
	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'nueve4_premium_scripts' );

/**
 * Add editor styles
 */
function nueve4_premium_editor_styles() {
	add_editor_style( 'assets/css/editor-style.css' );
}

add_action( 'admin_init', 'nueve4_premium_editor_styles' );

/**
 * Custom template tags
 */
require_once get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require_once get_template_directory() . '/inc/template-functions.php';

/**
 * Load WooCommerce compatibility file
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Add custom CSS for premium features
 */
function nueve4_premium_custom_css() {
	$custom_css = '';
	
	// Add any additional custom CSS based on theme mods
	$primary_color = get_theme_mod( 'nueve4_primary_color', '#0073aa' );
	if ( $primary_color !== '#0073aa' ) {
		$custom_css .= "
		:root {
			--primary-color: {$primary_color};
		}
		";
	}
	
	// Add dark mode CSS variables if enabled
	if ( get_theme_mod( 'nueve4_enable_dark_mode', false ) ) {
		$dark_bg = get_theme_mod( 'nueve4_dark_background_color', '#1a1a1a' );
		$dark_text = get_theme_mod( 'nueve4_dark_text_color', '#ffffff' );
		
		$custom_css .= "
		:root {
			--dark-background-color: {$dark_bg};
			--dark-text-color: {$dark_text};
		}
		";
	}
	
	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'nueve4-style', $custom_css );
	}
}

add_action( 'wp_enqueue_scripts', 'nueve4_premium_custom_css' );

/**
 * Add body classes for premium features
 */
function nueve4_premium_body_classes( $classes ) {
	// Add sidebar class
	$sidebar_position = get_theme_mod( 'nueve4_sidebar_position', 'right' );
	if ( $sidebar_position !== 'none' && is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'has-sidebar';
		$classes[] = 'sidebar-' . $sidebar_position;
	}
	
	// Add header layout class
	$header_layout = get_theme_mod( 'nueve4_header_layout', 'default' );
	$classes[] = 'header-' . $header_layout;
	
	// Add blog layout class
	if ( is_home() || is_archive() ) {
		$blog_layout = get_theme_mod( 'nueve4_blog_layout_type', 'grid' );
		$classes[] = 'blog-' . $blog_layout;
	}
	
	// Add sticky header class
	if ( get_theme_mod( 'nueve4_sticky_header', true ) ) {
		$classes[] = 'sticky-header';
	}
	
	// Add transparent header class
	if ( get_theme_mod( 'nueve4_transparent_header', false ) ) {
		$classes[] = 'transparent-header';
	}
	
	// Add dark mode class
	if ( get_theme_mod( 'nueve4_enable_dark_mode', false ) ) {
		$classes[] = 'dark-mode-enabled';
	}
	
	return $classes;
}

add_filter( 'body_class', 'nueve4_premium_body_classes' );

/**
 * Modify WooCommerce settings based on customizer
 */
function nueve4_premium_woocommerce_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	
	// Set shop columns
	$shop_columns = get_theme_mod( 'nueve4_shop_columns', 3 );
	add_filter( 'loop_shop_columns', function() use ( $shop_columns ) {
		return $shop_columns;
	});
	
	// Set products per page
	$products_per_page = get_theme_mod( 'nueve4_products_per_page', 12 );
	add_filter( 'loop_shop_per_page', function() use ( $products_per_page ) {
		return $products_per_page;
	});
	
	// Related products count
	$related_count = get_theme_mod( 'nueve4_related_products_count', 4 );
	add_filter( 'woocommerce_output_related_products_args', function( $args ) use ( $related_count ) {
		$args['posts_per_page'] = $related_count;
		return $args;
	});
}

add_action( 'after_setup_theme', 'nueve4_premium_woocommerce_setup' );

/**
 * Add schema markup if enabled
 */
function nueve4_premium_schema_markup() {
	if ( ! get_theme_mod( 'nueve4_schema_markup', true ) ) {
		return;
	}
	
	// Add basic schema markup
	if ( is_single() ) {
		echo '<script type="application/ld+json">';
		echo json_encode([
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title(),
			'author' => [
				'@type' => 'Person',
				'name' => get_the_author(),
			],
			'datePublished' => get_the_date( 'c' ),
			'dateModified' => get_the_modified_date( 'c' ),
		]);
		echo '</script>';
	}
}

add_action( 'wp_head', 'nueve4_premium_schema_markup' );

/**
 * Add breadcrumbs if enabled
 */
function nueve4_premium_breadcrumbs() {
	if ( ! get_theme_mod( 'nueve4_breadcrumbs', true ) || is_front_page() ) {
		return;
	}
	
	echo '<nav class="breadcrumbs" aria-label="Breadcrumb">';
	echo '<a href="' . home_url() . '">Home</a>';
	
	if ( is_category() || is_single() ) {
		echo ' &raquo; ';
		the_category( ' &raquo; ' );
		if ( is_single() ) {
			echo ' &raquo; ';
			the_title();
		}
	} elseif ( is_page() ) {
		echo ' &raquo; ';
		the_title();
	}
	
	echo '</nav>';
}

/**
 * Performance optimizations
 */
function nueve4_premium_performance() {
	// Minify CSS if enabled
	if ( get_theme_mod( 'nueve4_minify_css', false ) ) {
		add_filter( 'style_loader_tag', function( $tag, $handle ) {
			if ( strpos( $handle, 'nueve4' ) !== false ) {
				return str_replace( '.css', '.min.css', $tag );
			}
			return $tag;
		}, 10, 2 );
	}
	
	// Preload fonts if enabled
	if ( get_theme_mod( 'nueve4_preload_fonts', true ) ) {
		add_action( 'wp_head', function() {
			$body_font = get_theme_mod( 'nueve4_body_font_family', 'system-ui' );
			if ( $body_font !== 'system-ui' && $body_font !== 'inherit' ) {
				$font_url = 'https://fonts.googleapis.com/css2?family=' . str_replace( ' ', '+', $body_font ) . ':wght@100;200;300;400;500;600;700;800;900&display=swap';
				echo '<link rel="preload" href="' . $font_url . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
			}
		}, 1 );
	}
	
	// Enable lazy loading if enabled
	if ( get_theme_mod( 'nueve4_lazy_load', true ) ) {
		add_filter( 'wp_lazy_loading_enabled', '__return_true' );
	}
}

add_action( 'init', 'nueve4_premium_performance' );