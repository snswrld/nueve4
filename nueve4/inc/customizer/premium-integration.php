<?php
/**
 * Premium Customizer Integration
 * Integrates premium customizer with existing Nueve4 structure
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize premium customizer integration
 */
function nueve4_init_premium_integration() {
	// Load premium customizer
	require_once get_template_directory() . '/inc/customizer/premium-customizer.php';
	
	// Hook into existing customizer loader
	add_action( 'customize_register', 'nueve4_add_premium_controls', 5 );
	add_action( 'wp_head', 'nueve4_output_premium_css', 15 );
	add_action( 'customize_preview_init', 'nueve4_enqueue_premium_preview', 15 );
}

/**
 * Add premium controls to existing customizer
 */
function nueve4_add_premium_controls( $wp_customize ) {
	// Initialize premium customizer
	$premium_customizer = new Nueve4_Premium_Customizer();
	
	// Remove conflicting default sections
	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'background_image' );
	
	// Let premium customizer register its controls
	$premium_customizer->register_customizer( $wp_customize );
}

/**
 * Output premium CSS
 */
function nueve4_output_premium_css() {
	$premium_customizer = new Nueve4_Premium_Customizer();
	$premium_customizer->output_css();
}

/**
 * Enqueue premium preview scripts
 */
function nueve4_enqueue_premium_preview() {
	wp_enqueue_script( 
		'nueve4-premium-preview', 
		get_template_directory_uri() . '/assets/js/customizer-preview.js', 
		[ 'customize-preview', 'jquery' ], 
		'1.0.0', 
		true 
	);
}

// Initialize premium integration
add_action( 'after_setup_theme', 'nueve4_init_premium_integration', 5 );

/**
 * Add premium theme support
 */
function nueve4_add_premium_theme_support() {
	// Enhanced logo support
	add_theme_support( 'custom-logo', [
		'height'      => 250,
		'width'       => 500,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => [ 'site-title', 'site-description' ],
		'unlink-homepage-logo' => true,
	]);
	
	// Enhanced background support
	add_theme_support( 'custom-background', [
		'default-color'      => 'ffffff',
		'default-image'      => '',
		'default-preset'     => 'default',
		'default-position-x' => 'left',
		'default-position-y' => 'top',
		'default-size'       => 'auto',
		'default-repeat'     => 'repeat',
		'default-attachment' => 'scroll',
	]);
	
	// Block editor features
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	
	// Custom color palette for editor
	add_theme_support( 'editor-color-palette', [
		[
			'name'  => __( 'Primary', 'nueve4' ),
			'slug'  => 'primary',
			'color' => get_theme_mod( 'nueve4_primary_color', '#0073aa' ),
		],
		[
			'name'  => __( 'Secondary', 'nueve4' ),
			'slug'  => 'secondary', 
			'color' => get_theme_mod( 'nueve4_secondary_color', '#666666' ),
		],
		[
			'name'  => __( 'Accent', 'nueve4' ),
			'slug'  => 'accent',
			'color' => get_theme_mod( 'nueve4_accent_color', '#ff6b35' ),
		],
		[
			'name'  => __( 'Text', 'nueve4' ),
			'slug'  => 'text',
			'color' => get_theme_mod( 'nueve4_text_color', '#333333' ),
		],
		[
			'name'  => __( 'Background', 'nueve4' ),
			'slug'  => 'background',
			'color' => get_theme_mod( 'nueve4_background_color', '#ffffff' ),
		],
	]);
	
	// Custom font sizes for editor
	add_theme_support( 'editor-font-sizes', [
		[
			'name' => __( 'Small', 'nueve4' ),
			'size' => 14,
			'slug' => 'small'
		],
		[
			'name' => __( 'Normal', 'nueve4' ),
			'size' => 16,
			'slug' => 'normal'
		],
		[
			'name' => __( 'Medium', 'nueve4' ),
			'size' => 20,
			'slug' => 'medium'
		],
		[
			'name' => __( 'Large', 'nueve4' ),
			'size' => 24,
			'slug' => 'large'
		],
		[
			'name' => __( 'Extra Large', 'nueve4' ),
			'size' => 32,
			'slug' => 'extra-large'
		],
		[
			'name' => __( 'Huge', 'nueve4' ),
			'size' => 48,
			'slug' => 'huge'
		]
	]);
}

add_action( 'after_setup_theme', 'nueve4_add_premium_theme_support' );

/**
 * Register premium widget areas
 */
function nueve4_register_premium_widgets() {
	// Enhanced sidebar
	register_sidebar( [
		'name'          => __( 'Primary Sidebar', 'nueve4' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'nueve4' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	]);
	
	// Shop sidebar for WooCommerce
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( [
			'name'          => __( 'Shop Sidebar', 'nueve4' ),
			'id'            => 'shop-sidebar',
			'description'   => __( 'Add widgets here to appear in your shop sidebar.', 'nueve4' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]);
	}
	
	// Dynamic footer widget areas
	$footer_columns = get_theme_mod( 'nueve4_footer_columns', 4 );
	for ( $i = 1; $i <= $footer_columns; $i++ ) {
		register_sidebar( [
			'name'          => sprintf( __( 'Footer Column %d', 'nueve4' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => sprintf( __( 'Add widgets here to appear in footer column %d.', 'nueve4' ), $i ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]);
	}
	
	// Header widget area
	register_sidebar( [
		'name'          => __( 'Header Widget Area', 'nueve4' ),
		'id'            => 'header-widget',
		'description'   => __( 'Add widgets here to appear in your header.', 'nueve4' ),
		'before_widget' => '<div id="%1$s" class="header-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="header-widget-title">',
		'after_title'   => '</h4>',
	]);
}

add_action( 'widgets_init', 'nueve4_register_premium_widgets' );

/**
 * Enqueue premium assets
 */
function nueve4_enqueue_premium_assets() {
	// Premium stylesheet
	wp_enqueue_style( 
		'nueve4-premium-style', 
		get_template_directory_uri() . '/assets/css/premium-style.css', 
		[], 
		'1.0.0' 
	);
	
	// Premium JavaScript
	wp_enqueue_script( 
		'nueve4-premium-script', 
		get_template_directory_uri() . '/assets/js/premium-script.js', 
		[ 'jquery' ], 
		'1.0.0', 
		true 
	);
	
	// Localize premium script
	wp_localize_script( 'nueve4-premium-script', 'nueve4Premium', [
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'nonce'          => wp_create_nonce( 'nueve4_premium_nonce' ),
		'darkMode'       => get_theme_mod( 'nueve4_enable_dark_mode', false ),
		'stickyHeader'   => get_theme_mod( 'nueve4_sticky_header', true ),
		'transparentHeader' => get_theme_mod( 'nueve4_transparent_header', false ),
		'lazyLoad'       => get_theme_mod( 'nueve4_lazy_load', true ),
		'smoothScroll'   => get_theme_mod( 'nueve4_smooth_scroll', false ),
	]);
}

add_action( 'wp_enqueue_scripts', 'nueve4_enqueue_premium_assets' );

/**
 * Add premium body classes
 */
function nueve4_premium_body_classes( $classes ) {
	// Header layout
	$header_layout = get_theme_mod( 'nueve4_header_layout', 'default' );
	$classes[] = 'header-layout-' . $header_layout;
	
	// Sidebar position
	$sidebar_position = get_theme_mod( 'nueve4_sidebar_position', 'right' );
	if ( $sidebar_position !== 'none' && is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'has-sidebar';
		$classes[] = 'sidebar-' . $sidebar_position;
	}
	
	// Blog layout
	if ( is_home() || is_archive() ) {
		$blog_layout = get_theme_mod( 'nueve4_blog_layout_type', 'grid' );
		$classes[] = 'blog-layout-' . $blog_layout;
		
		$blog_columns = get_theme_mod( 'nueve4_blog_columns', 2 );
		$classes[] = 'blog-columns-' . $blog_columns;
	}
	
	// Header features
	if ( get_theme_mod( 'nueve4_sticky_header', true ) ) {
		$classes[] = 'sticky-header';
	}
	
	if ( get_theme_mod( 'nueve4_transparent_header', false ) ) {
		$classes[] = 'transparent-header';
	}
	
	// Dark mode
	if ( get_theme_mod( 'nueve4_enable_dark_mode', false ) ) {
		$classes[] = 'dark-mode-enabled';
	}
	
	// Performance features
	if ( get_theme_mod( 'nueve4_lazy_load', true ) ) {
		$classes[] = 'lazy-load-enabled';
	}
	
	return $classes;
}

add_filter( 'body_class', 'nueve4_premium_body_classes' );

/**
 * Customize WooCommerce based on theme settings
 */
function nueve4_customize_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	
	// Shop columns
	add_filter( 'loop_shop_columns', function() {
		return get_theme_mod( 'nueve4_shop_columns', 3 );
	});
	
	// Products per page
	add_filter( 'loop_shop_per_page', function() {
		return get_theme_mod( 'nueve4_products_per_page', 12 );
	});
	
	// Related products
	add_filter( 'woocommerce_output_related_products_args', function( $args ) {
		$args['posts_per_page'] = get_theme_mod( 'nueve4_related_products_count', 4 );
		$args['columns'] = get_theme_mod( 'nueve4_shop_columns', 3 );
		return $args;
	});
	
	// Gallery features
	if ( get_theme_mod( 'nueve4_product_gallery_zoom', true ) ) {
		add_theme_support( 'wc-product-gallery-zoom' );
	}
	
	if ( get_theme_mod( 'nueve4_product_gallery_lightbox', true ) ) {
		add_theme_support( 'wc-product-gallery-lightbox' );
	}
	
	add_theme_support( 'wc-product-gallery-slider' );
}

add_action( 'after_setup_theme', 'nueve4_customize_woocommerce' );

/**
 * Add schema markup
 */
function nueve4_add_schema_markup() {
	if ( ! get_theme_mod( 'nueve4_schema_markup', true ) ) {
		return;
	}
	
	if ( is_single() && ! is_attachment() ) {
		$schema = [
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title(),
			'author' => [
				'@type' => 'Person',
				'name' => get_the_author(),
				'url' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			],
			'datePublished' => get_the_date( 'c' ),
			'dateModified' => get_the_modified_date( 'c' ),
			'publisher' => [
				'@type' => 'Organization',
				'name' => get_bloginfo( 'name' ),
				'url' => home_url(),
			],
		];
		
		if ( has_post_thumbnail() ) {
			$schema['image'] = get_the_post_thumbnail_url( null, 'large' );
		}
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
	}
}

add_action( 'wp_head', 'nueve4_add_schema_markup' );

/**
 * Performance optimizations
 */
function nueve4_performance_optimizations() {
	// Preload Google Fonts
	if ( get_theme_mod( 'nueve4_preload_fonts', true ) ) {
		add_action( 'wp_head', function() {
			$fonts_to_preload = [];
			
			$body_font = get_theme_mod( 'nueve4_body_font_family', 'system-ui' );
			if ( $body_font !== 'system-ui' && $body_font !== 'inherit' ) {
				$fonts_to_preload[] = $body_font;
			}
			
			$heading_font = get_theme_mod( 'nueve4_headings_font_family', 'inherit' );
			if ( $heading_font !== 'inherit' && $heading_font !== $body_font ) {
				$fonts_to_preload[] = $heading_font;
			}
			
			foreach ( array_unique( $fonts_to_preload ) as $font ) {
				$font_url = 'https://fonts.googleapis.com/css2?family=' . str_replace( ' ', '+', $font ) . ':wght@100;200;300;400;500;600;700;800;900&display=swap';
				echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
				echo '<noscript><link rel="stylesheet" href="' . esc_url( $font_url ) . '"></noscript>';
			}
		}, 1 );
	}
	
	// Enable lazy loading
	if ( get_theme_mod( 'nueve4_lazy_load', true ) ) {
		add_filter( 'wp_lazy_loading_enabled', '__return_true' );
	}
}

add_action( 'init', 'nueve4_performance_optimizations' );