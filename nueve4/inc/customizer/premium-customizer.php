<?php
/**
 * Premium WordPress Customizer Implementation
 * World-class customizer with all premium features included
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nueve4_Premium_Customizer {
	
	public function __construct() {
		add_action( 'customize_register', [ $this, 'register_customizer' ] );
		add_action( 'wp_head', [ $this, 'output_css' ] );
		add_action( 'customize_preview_init', [ $this, 'preview_scripts' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'control_scripts' ] );
	}
	
	/**
	 * Register all customizer sections and controls
	 */
	public function register_customizer( $wp_customize ) {
		// Remove default sections we don't need
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'background_image' );
		
		// Add premium panels and sections
		$this->add_global_panel( $wp_customize );
		$this->add_header_panel( $wp_customize );
		$this->add_layout_panel( $wp_customize );
		$this->add_typography_panel( $wp_customize );
		$this->add_colors_panel( $wp_customize );
		$this->add_blog_panel( $wp_customize );
		$this->add_woocommerce_panel( $wp_customize );
		$this->add_performance_panel( $wp_customize );
	}
	
	/**
	 * Global Settings Panel
	 */
	private function add_global_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_global', [
			'title' => __( 'Global Settings', 'nueve4' ),
			'priority' => 10,
		]);
		
		// Container Settings
		$wp_customize->add_section( 'nueve4_container', [
			'title' => __( 'Container', 'nueve4' ),
			'panel' => 'nueve4_global',
		]);
		
		$wp_customize->add_setting( 'nueve4_container_width', [
			'default' => 1170,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_container_width', [
			'label' => __( 'Container Width (px)', 'nueve4' ),
			'section' => 'nueve4_container',
			'type' => 'range',
			'input_attrs' => [ 'min' => 700, 'max' => 1920, 'step' => 10 ],
		]);
		
		$wp_customize->add_setting( 'nueve4_container_padding', [
			'default' => 15,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_container_padding', [
			'label' => __( 'Container Padding (px)', 'nueve4' ),
			'section' => 'nueve4_container',
			'type' => 'range',
			'input_attrs' => [ 'min' => 0, 'max' => 50, 'step' => 1 ],
		]);
		
		// Responsive Breakpoints
		$wp_customize->add_section( 'nueve4_responsive', [
			'title' => __( 'Responsive Breakpoints', 'nueve4' ),
			'panel' => 'nueve4_global',
		]);
		
		$breakpoints = [ 'tablet' => 768, 'mobile' => 480 ];
		foreach ( $breakpoints as $device => $default ) {
			$wp_customize->add_setting( "nueve4_{$device}_breakpoint", [
				'default' => $default,
				'sanitize_callback' => 'absint',
			]);
			$wp_customize->add_control( "nueve4_{$device}_breakpoint", [
				'label' => sprintf( __( '%s Breakpoint (px)', 'nueve4' ), ucfirst( $device ) ),
				'section' => 'nueve4_responsive',
				'type' => 'number',
				'input_attrs' => [ 'min' => 320, 'max' => 1200 ],
			]);
		}
	}
	
	/**
	 * Header Panel with Advanced Controls
	 */
	private function add_header_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_header', [
			'title' => __( 'Header Builder', 'nueve4' ),
			'priority' => 20,
		]);
		
		// Header Layout
		$wp_customize->add_section( 'nueve4_header_layout', [
			'title' => __( 'Header Layout', 'nueve4' ),
			'panel' => 'nueve4_header',
		]);
		
		$wp_customize->add_setting( 'nueve4_header_layout', [
			'default' => 'default',
			'sanitize_callback' => 'sanitize_text_field',
		]);
		$wp_customize->add_control( 'nueve4_header_layout', [
			'label' => __( 'Header Layout', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'select',
			'choices' => [
				'default' => __( 'Default', 'nueve4' ),
				'centered' => __( 'Centered', 'nueve4' ),
				'split' => __( 'Split Menu', 'nueve4' ),
				'minimal' => __( 'Minimal', 'nueve4' ),
				'creative' => __( 'Creative', 'nueve4' ),
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_header_height', [
			'default' => 70,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_header_height', [
			'label' => __( 'Header Height (px)', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'range',
			'input_attrs' => [ 'min' => 50, 'max' => 200, 'step' => 5 ],
		]);
		
		$wp_customize->add_setting( 'nueve4_sticky_header', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_sticky_header', [
			'label' => __( 'Sticky Header', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_transparent_header', [
			'default' => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_transparent_header', [
			'label' => __( 'Transparent Header', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'checkbox',
		]);
		
		// Header Colors
		$wp_customize->add_section( 'nueve4_header_colors', [
			'title' => __( 'Header Colors', 'nueve4' ),
			'panel' => 'nueve4_header',
		]);
		
		$header_colors = [
			'background' => [ 'default' => '#ffffff', 'label' => 'Background Color' ],
			'text' => [ 'default' => '#333333', 'label' => 'Text Color' ],
			'link' => [ 'default' => '#333333', 'label' => 'Link Color' ],
			'link_hover' => [ 'default' => '#0073aa', 'label' => 'Link Hover Color' ],
			'border' => [ 'default' => '#e5e5e5', 'label' => 'Border Color' ],
		];
		
		foreach ( $header_colors as $key => $color ) {
			$wp_customize->add_setting( "nueve4_header_{$key}_color", [
				'default' => $color['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "nueve4_header_{$key}_color", [
				'label' => __( $color['label'], 'nueve4' ),
				'section' => 'nueve4_header_colors',
			]));
		}
		
		// Logo Settings
		$wp_customize->add_section( 'nueve4_logo', [
			'title' => __( 'Logo & Site Identity', 'nueve4' ),
			'panel' => 'nueve4_header',
		]);
		
		$wp_customize->add_setting( 'nueve4_logo_width', [
			'default' => 120,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_logo_width', [
			'label' => __( 'Logo Width (px)', 'nueve4' ),
			'section' => 'nueve4_logo',
			'type' => 'range',
			'input_attrs' => [ 'min' => 50, 'max' => 300, 'step' => 5 ],
		]);
		
		$wp_customize->add_setting( 'nueve4_retina_logo', [
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'nueve4_retina_logo', [
			'label' => __( 'Retina Logo (2x)', 'nueve4' ),
			'section' => 'nueve4_logo',
			'mime_type' => 'image',
		]));
		
		$wp_customize->add_setting( 'nueve4_mobile_logo', [
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'nueve4_mobile_logo', [
			'label' => __( 'Mobile Logo', 'nueve4' ),
			'section' => 'nueve4_logo',
			'mime_type' => 'image',
		]));
	}
	
	/**
	 * Typography Panel with Google Fonts
	 */
	private function add_typography_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_typography', [
			'title' => __( 'Typography', 'nueve4' ),
			'priority' => 30,
		]);
		
		$typography_elements = [
			'body' => [ 'label' => 'Body Text', 'default_family' => 'system-ui', 'default_size' => 16 ],
			'headings' => [ 'label' => 'Headings', 'default_family' => 'inherit', 'default_size' => 32 ],
			'h1' => [ 'label' => 'H1 Heading', 'default_family' => 'inherit', 'default_size' => 36 ],
			'h2' => [ 'label' => 'H2 Heading', 'default_family' => 'inherit', 'default_size' => 30 ],
			'h3' => [ 'label' => 'H3 Heading', 'default_family' => 'inherit', 'default_size' => 24 ],
			'menu' => [ 'label' => 'Menu Items', 'default_family' => 'inherit', 'default_size' => 16 ],
			'buttons' => [ 'label' => 'Buttons', 'default_family' => 'inherit', 'default_size' => 16 ],
		];
		
		foreach ( $typography_elements as $element => $config ) {
			$wp_customize->add_section( "nueve4_typography_{$element}", [
				'title' => __( $config['label'], 'nueve4' ),
				'panel' => 'nueve4_typography',
			]);
			
			// Font Family
			$wp_customize->add_setting( "nueve4_{$element}_font_family", [
				'default' => $config['default_family'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_font_family", [
				'label' => __( 'Font Family', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'select',
				'choices' => $this->get_google_fonts(),
			]);
			
			// Font Size
			$wp_customize->add_setting( "nueve4_{$element}_font_size", [
				'default' => $config['default_size'],
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_font_size", [
				'label' => __( 'Font Size (px)', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'range',
				'input_attrs' => [ 'min' => 10, 'max' => 72, 'step' => 1 ],
			]);
			
			// Font Weight
			$wp_customize->add_setting( "nueve4_{$element}_font_weight", [
				'default' => 400,
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_font_weight", [
				'label' => __( 'Font Weight', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'select',
				'choices' => [
					100 => '100 - Thin',
					200 => '200 - Extra Light',
					300 => '300 - Light',
					400 => '400 - Normal',
					500 => '500 - Medium',
					600 => '600 - Semi Bold',
					700 => '700 - Bold',
					800 => '800 - Extra Bold',
					900 => '900 - Black',
				],
			]);
			
			// Line Height
			$wp_customize->add_setting( "nueve4_{$element}_line_height", [
				'default' => 1.6,
				'sanitize_callback' => [ $this, 'sanitize_float' ],
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_line_height", [
				'label' => __( 'Line Height', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'number',
				'input_attrs' => [ 'min' => 1, 'max' => 3, 'step' => 0.1 ],
			]);
			
			// Letter Spacing
			$wp_customize->add_setting( "nueve4_{$element}_letter_spacing", [
				'default' => 0,
				'sanitize_callback' => [ $this, 'sanitize_float' ],
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_letter_spacing", [
				'label' => __( 'Letter Spacing (px)', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'number',
				'input_attrs' => [ 'min' => -5, 'max' => 10, 'step' => 0.1 ],
			]);
			
			// Text Transform
			$wp_customize->add_setting( "nueve4_{$element}_text_transform", [
				'default' => 'none',
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( "nueve4_{$element}_text_transform", [
				'label' => __( 'Text Transform', 'nueve4' ),
				'section' => "nueve4_typography_{$element}",
				'type' => 'select',
				'choices' => [
					'none' => 'None',
					'uppercase' => 'UPPERCASE',
					'lowercase' => 'lowercase',
					'capitalize' => 'Capitalize',
				],
			]);
		}
	}
	
	/**
	 * Advanced Colors Panel
	 */
	private function add_colors_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_colors', [
			'title' => __( 'Colors & Styling', 'nueve4' ),
			'priority' => 40,
		]);
		
		// Global Colors
		$wp_customize->add_section( 'nueve4_global_colors', [
			'title' => __( 'Global Colors', 'nueve4' ),
			'panel' => 'nueve4_colors',
		]);
		
		$global_colors = [
			'primary' => [ 'default' => '#0073aa', 'label' => 'Primary Color' ],
			'secondary' => [ 'default' => '#666666', 'label' => 'Secondary Color' ],
			'accent' => [ 'default' => '#ff6b35', 'label' => 'Accent Color' ],
			'text' => [ 'default' => '#333333', 'label' => 'Text Color' ],
			'text_light' => [ 'default' => '#666666', 'label' => 'Light Text Color' ],
			'background' => [ 'default' => '#ffffff', 'label' => 'Background Color' ],
			'border' => [ 'default' => '#e5e5e5', 'label' => 'Border Color' ],
			'success' => [ 'default' => '#28a745', 'label' => 'Success Color' ],
			'warning' => [ 'default' => '#ffc107', 'label' => 'Warning Color' ],
			'error' => [ 'default' => '#dc3545', 'label' => 'Error Color' ],
		];
		
		foreach ( $global_colors as $key => $color ) {
			$wp_customize->add_setting( "nueve4_{$key}_color", [
				'default' => $color['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "nueve4_{$key}_color", [
				'label' => __( $color['label'], 'nueve4' ),
				'section' => 'nueve4_global_colors',
			]));
		}
		
		// Dark Mode
		$wp_customize->add_section( 'nueve4_dark_mode', [
			'title' => __( 'Dark Mode', 'nueve4' ),
			'panel' => 'nueve4_colors',
		]);
		
		$wp_customize->add_setting( 'nueve4_enable_dark_mode', [
			'default' => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_enable_dark_mode', [
			'label' => __( 'Enable Dark Mode Toggle', 'nueve4' ),
			'section' => 'nueve4_dark_mode',
			'type' => 'checkbox',
		]);
		
		$dark_colors = [
			'background' => [ 'default' => '#1a1a1a', 'label' => 'Dark Background' ],
			'text' => [ 'default' => '#ffffff', 'label' => 'Dark Text Color' ],
			'border' => [ 'default' => '#333333', 'label' => 'Dark Border Color' ],
		];
		
		foreach ( $dark_colors as $key => $color ) {
			$wp_customize->add_setting( "nueve4_dark_{$key}_color", [
				'default' => $color['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			]);
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "nueve4_dark_{$key}_color", [
				'label' => __( $color['label'], 'nueve4' ),
				'section' => 'nueve4_dark_mode',
			]));
		}
	}
	
	/**
	 * Layout Panel
	 */
	private function add_layout_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_layout', [
			'title' => __( 'Layout & Structure', 'nueve4' ),
			'priority' => 50,
		]);
		
		// Sidebar Settings
		$wp_customize->add_section( 'nueve4_sidebar', [
			'title' => __( 'Sidebar', 'nueve4' ),
			'panel' => 'nueve4_layout',
		]);
		
		$wp_customize->add_setting( 'nueve4_sidebar_position', [
			'default' => 'right',
			'sanitize_callback' => 'sanitize_text_field',
		]);
		$wp_customize->add_control( 'nueve4_sidebar_position', [
			'label' => __( 'Default Sidebar Position', 'nueve4' ),
			'section' => 'nueve4_sidebar',
			'type' => 'radio',
			'choices' => [
				'none' => __( 'No Sidebar', 'nueve4' ),
				'left' => __( 'Left Sidebar', 'nueve4' ),
				'right' => __( 'Right Sidebar', 'nueve4' ),
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_sidebar_width', [
			'default' => 25,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_sidebar_width', [
			'label' => __( 'Sidebar Width (%)', 'nueve4' ),
			'section' => 'nueve4_sidebar',
			'type' => 'range',
			'input_attrs' => [ 'min' => 20, 'max' => 40, 'step' => 1 ],
		]);
		
		// Footer Settings
		$wp_customize->add_section( 'nueve4_footer', [
			'title' => __( 'Footer', 'nueve4' ),
			'panel' => 'nueve4_layout',
		]);
		
		$wp_customize->add_setting( 'nueve4_footer_columns', [
			'default' => 4,
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_footer_columns', [
			'label' => __( 'Footer Widget Columns', 'nueve4' ),
			'section' => 'nueve4_footer',
			'type' => 'select',
			'choices' => [
				1 => '1 Column',
				2 => '2 Columns',
				3 => '3 Columns',
				4 => '4 Columns',
				5 => '5 Columns',
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_footer_copyright', [
			'default' => sprintf( __( '© %s %s. All rights reserved.', 'nueve4' ), date('Y'), get_bloginfo('name') ),
			'sanitize_callback' => 'wp_kses_post',
			'transport' => 'postMessage',
		]);
		$wp_customize->add_control( 'nueve4_footer_copyright', [
			'label' => __( 'Copyright Text', 'nueve4' ),
			'section' => 'nueve4_footer',
			'type' => 'textarea',
		]);
		
		$wp_customize->add_setting( 'nueve4_sticky_footer', [
			'default' => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_sticky_footer', [
			'label' => __( 'Sticky Footer', 'nueve4' ),
			'section' => 'nueve4_footer',
			'type' => 'checkbox',
		]);
	}
	
	/**
	 * Blog Panel
	 */
	private function add_blog_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_blog', [
			'title' => __( 'Blog & Archives', 'nueve4' ),
			'priority' => 60,
		]);
		
		// Blog Layout
		$wp_customize->add_section( 'nueve4_blog_layout', [
			'title' => __( 'Blog Layout', 'nueve4' ),
			'panel' => 'nueve4_blog',
		]);
		
		$wp_customize->add_setting( 'nueve4_blog_layout_type', [
			'default' => 'grid',
			'sanitize_callback' => 'sanitize_text_field',
		]);
		$wp_customize->add_control( 'nueve4_blog_layout_type', [
			'label' => __( 'Blog Layout', 'nueve4' ),
			'section' => 'nueve4_blog_layout',
			'type' => 'radio',
			'choices' => [
				'list' => __( 'List View', 'nueve4' ),
				'grid' => __( 'Grid View', 'nueve4' ),
				'masonry' => __( 'Masonry', 'nueve4' ),
				'cards' => __( 'Cards', 'nueve4' ),
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_blog_columns', [
			'default' => 2,
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_blog_columns', [
			'label' => __( 'Grid Columns', 'nueve4' ),
			'section' => 'nueve4_blog_layout',
			'type' => 'select',
			'choices' => [
				1 => '1 Column',
				2 => '2 Columns',
				3 => '3 Columns',
				4 => '4 Columns',
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_posts_per_page', [
			'default' => get_option( 'posts_per_page' ),
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_posts_per_page', [
			'label' => __( 'Posts Per Page', 'nueve4' ),
			'section' => 'nueve4_blog_layout',
			'type' => 'number',
			'input_attrs' => [ 'min' => 1, 'max' => 50 ],
		]);
		
		// Post Meta
		$wp_customize->add_section( 'nueve4_post_meta', [
			'title' => __( 'Post Meta', 'nueve4' ),
			'panel' => 'nueve4_blog',
		]);
		
		$meta_options = [
			'author' => __( 'Show Author', 'nueve4' ),
			'date' => __( 'Show Date', 'nueve4' ),
			'categories' => __( 'Show Categories', 'nueve4' ),
			'tags' => __( 'Show Tags', 'nueve4' ),
			'comments' => __( 'Show Comment Count', 'nueve4' ),
			'reading_time' => __( 'Show Reading Time', 'nueve4' ),
		];
		
		foreach ( $meta_options as $key => $label ) {
			$wp_customize->add_setting( "nueve4_show_{$key}", [
				'default' => true,
				'sanitize_callback' => 'wp_validate_boolean',
			]);
			$wp_customize->add_control( "nueve4_show_{$key}", [
				'label' => $label,
				'section' => 'nueve4_post_meta',
				'type' => 'checkbox',
			]);
		}
	}
	
	/**
	 * WooCommerce Panel
	 */
	private function add_woocommerce_panel( $wp_customize ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		
		$wp_customize->add_panel( 'nueve4_woocommerce', [
			'title' => __( 'WooCommerce', 'nueve4' ),
			'priority' => 70,
		]);
		
		// Shop Layout
		$wp_customize->add_section( 'nueve4_shop_layout', [
			'title' => __( 'Shop Layout', 'nueve4' ),
			'panel' => 'nueve4_woocommerce',
		]);
		
		$wp_customize->add_setting( 'nueve4_shop_columns', [
			'default' => 3,
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_shop_columns', [
			'label' => __( 'Shop Columns', 'nueve4' ),
			'section' => 'nueve4_shop_layout',
			'type' => 'select',
			'choices' => [
				2 => '2 Columns',
				3 => '3 Columns',
				4 => '4 Columns',
				5 => '5 Columns',
			],
		]);
		
		$wp_customize->add_setting( 'nueve4_products_per_page', [
			'default' => 12,
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_products_per_page', [
			'label' => __( 'Products Per Page', 'nueve4' ),
			'section' => 'nueve4_shop_layout',
			'type' => 'number',
			'input_attrs' => [ 'min' => 1, 'max' => 100 ],
		]);
		
		// Product Page
		$wp_customize->add_section( 'nueve4_product_page', [
			'title' => __( 'Single Product', 'nueve4' ),
			'panel' => 'nueve4_woocommerce',
		]);
		
		$wp_customize->add_setting( 'nueve4_product_gallery_zoom', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_product_gallery_zoom', [
			'label' => __( 'Enable Gallery Zoom', 'nueve4' ),
			'section' => 'nueve4_product_page',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_product_gallery_lightbox', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_product_gallery_lightbox', [
			'label' => __( 'Enable Gallery Lightbox', 'nueve4' ),
			'section' => 'nueve4_product_page',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_related_products_count', [
			'default' => 4,
			'sanitize_callback' => 'absint',
		]);
		$wp_customize->add_control( 'nueve4_related_products_count', [
			'label' => __( 'Related Products Count', 'nueve4' ),
			'section' => 'nueve4_product_page',
			'type' => 'number',
			'input_attrs' => [ 'min' => 0, 'max' => 20 ],
		]);
	}
	
	/**
	 * Performance Panel
	 */
	private function add_performance_panel( $wp_customize ) {
		$wp_customize->add_panel( 'nueve4_performance', [
			'title' => __( 'Performance & SEO', 'nueve4' ),
			'priority' => 80,
		]);
		
		// Performance Settings
		$wp_customize->add_section( 'nueve4_performance_settings', [
			'title' => __( 'Performance', 'nueve4' ),
			'panel' => 'nueve4_performance',
		]);
		
		$wp_customize->add_setting( 'nueve4_minify_css', [
			'default' => false,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_minify_css', [
			'label' => __( 'Minify CSS', 'nueve4' ),
			'section' => 'nueve4_performance_settings',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_lazy_load', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_lazy_load', [
			'label' => __( 'Enable Lazy Loading', 'nueve4' ),
			'section' => 'nueve4_performance_settings',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_preload_fonts', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_preload_fonts', [
			'label' => __( 'Preload Google Fonts', 'nueve4' ),
			'section' => 'nueve4_performance_settings',
			'type' => 'checkbox',
		]);
		
		// SEO Settings
		$wp_customize->add_section( 'nueve4_seo', [
			'title' => __( 'SEO', 'nueve4' ),
			'panel' => 'nueve4_performance',
		]);
		
		$wp_customize->add_setting( 'nueve4_schema_markup', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_schema_markup', [
			'label' => __( 'Enable Schema Markup', 'nueve4' ),
			'section' => 'nueve4_seo',
			'type' => 'checkbox',
		]);
		
		$wp_customize->add_setting( 'nueve4_breadcrumbs', [
			'default' => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]);
		$wp_customize->add_control( 'nueve4_breadcrumbs', [
			'label' => __( 'Enable Breadcrumbs', 'nueve4' ),
			'section' => 'nueve4_seo',
			'type' => 'checkbox',
		]);
	}
	
	/**
	 * Get Google Fonts list
	 */
	private function get_google_fonts() {
		return [
			'inherit' => 'Inherit',
			'system-ui' => 'System Font',
			'Arial' => 'Arial',
			'Helvetica' => 'Helvetica',
			'Georgia' => 'Georgia',
			'Times' => 'Times',
			'Roboto' => 'Roboto',
			'Open Sans' => 'Open Sans',
			'Lato' => 'Lato',
			'Montserrat' => 'Montserrat',
			'Source Sans Pro' => 'Source Sans Pro',
			'Oswald' => 'Oswald',
			'Raleway' => 'Raleway',
			'PT Sans' => 'PT Sans',
			'Ubuntu' => 'Ubuntu',
			'Nunito' => 'Nunito',
			'Poppins' => 'Poppins',
			'Playfair Display' => 'Playfair Display',
			'Merriweather' => 'Merriweather',
			'Inter' => 'Inter',
		];
	}
	
	/**
	 * Sanitize float values
	 */
	public function sanitize_float( $value ) {
		return floatval( $value );
	}
	
	/**
	 * Output dynamic CSS
	 */
	public function output_css() {
		$css = $this->generate_css();
		if ( ! empty( $css ) ) {
			echo '<style id="nueve4-customizer-css">' . $css . '</style>';
		}
	}
	
	/**
	 * Generate CSS from customizer settings
	 */
	private function generate_css() {
		$css = '';
		
		// Container
		$container_width = get_theme_mod( 'nueve4_container_width', 1170 );
		$container_padding = get_theme_mod( 'nueve4_container_padding', 15 );
		$css .= ".container, .nv-container { max-width: {$container_width}px; padding-left: {$container_padding}px; padding-right: {$container_padding}px; }";
		
		// Colors
		$primary_color = get_theme_mod( 'nueve4_primary_color', '#0073aa' );
		$text_color = get_theme_mod( 'nueve4_text_color', '#333333' );
		$background_color = get_theme_mod( 'nueve4_background_color', '#ffffff' );
		
		$css .= "body { color: {$text_color}; background-color: {$background_color}; }";
		$css .= ".btn-primary, button[type='submit'], input[type='submit'] { background-color: {$primary_color}; border-color: {$primary_color}; }";
		$css .= "a { color: {$primary_color}; }";
		
		// Typography
		$body_font = get_theme_mod( 'nueve4_body_font_family', 'system-ui' );
		$body_size = get_theme_mod( 'nueve4_body_font_size', 16 );
		$body_weight = get_theme_mod( 'nueve4_body_font_weight', 400 );
		$body_line_height = get_theme_mod( 'nueve4_body_line_height', 1.6 );
		
		if ( $body_font !== 'inherit' && $body_font !== 'system-ui' ) {
			$css .= "@import url('https://fonts.googleapis.com/css2?family=" . str_replace( ' ', '+', $body_font ) . ":wght@100;200;300;400;500;600;700;800;900&display=swap');";
		}
		
		$font_family = $body_font === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : $body_font . ', sans-serif';
		$css .= "body { font-family: {$font_family}; font-size: {$body_size}px; font-weight: {$body_weight}; line-height: {$body_line_height}; }";
		
		// Header
		$header_height = get_theme_mod( 'nueve4_header_height', 70 );
		$header_bg = get_theme_mod( 'nueve4_header_background_color', '#ffffff' );
		$sticky_header = get_theme_mod( 'nueve4_sticky_header', true );
		
		$css .= ".site-header { min-height: {$header_height}px; background-color: {$header_bg}; }";
		if ( $sticky_header ) {
			$css .= ".site-header { position: sticky; top: 0; z-index: 999; }";
		}
		
		// Logo
		$logo_width = get_theme_mod( 'nueve4_logo_width', 120 );
		$css .= ".custom-logo { max-width: {$logo_width}px; height: auto; }";
		
		// Sidebar
		$sidebar_width = get_theme_mod( 'nueve4_sidebar_width', 25 );
		$content_width = 100 - $sidebar_width;
		$css .= ".has-sidebar .content-area { width: {$content_width}%; }";
		$css .= ".has-sidebar .widget-area { width: {$sidebar_width}%; }";
		
		// Dark mode
		if ( get_theme_mod( 'nueve4_enable_dark_mode', false ) ) {
			$dark_bg = get_theme_mod( 'nueve4_dark_background_color', '#1a1a1a' );
			$dark_text = get_theme_mod( 'nueve4_dark_text_color', '#ffffff' );
			$dark_border = get_theme_mod( 'nueve4_dark_border_color', '#333333' );
			
			$css .= "@media (prefers-color-scheme: dark) {";
			$css .= "body.dark-mode { background-color: {$dark_bg}; color: {$dark_text}; }";
			$css .= "body.dark-mode .site-header { background-color: {$dark_bg}; border-color: {$dark_border}; }";
			$css .= "}";
		}
		
		// Responsive
		$tablet_breakpoint = get_theme_mod( 'nueve4_tablet_breakpoint', 768 );
		$mobile_breakpoint = get_theme_mod( 'nueve4_mobile_breakpoint', 480 );
		
		$css .= "@media (max-width: {$tablet_breakpoint}px) {";
		$css .= ".container { padding-left: 20px; padding-right: 20px; }";
		$css .= ".has-sidebar .content-area, .has-sidebar .widget-area { width: 100%; }";
		$css .= "}";
		
		$css .= "@media (max-width: {$mobile_breakpoint}px) {";
		$css .= ".container { padding-left: 15px; padding-right: 15px; }";
		$css .= "body { font-size: " . max( 14, $body_size - 2 ) . "px; }";
		$css .= "}";
		
		return $css;
	}
	
	/**
	 * Enqueue preview scripts
	 */
	public function preview_scripts() {
		wp_enqueue_script( 'nueve4-customizer-preview', get_template_directory_uri() . '/assets/js/customizer-preview.js', [ 'customize-preview' ], '1.0.0', true );
	}
	
	/**
	 * Enqueue control scripts
	 */
	public function control_scripts() {
		wp_enqueue_script( 'nueve4-customizer-controls', get_template_directory_uri() . '/assets/js/customizer-controls.js', [ 'customize-controls' ], '1.0.0', true );
		wp_enqueue_style( 'nueve4-customizer-controls', get_template_directory_uri() . '/assets/css/customizer-controls.css', [], '1.0.0' );
	}
}

// Initialize the customizer
new Nueve4_Premium_Customizer();