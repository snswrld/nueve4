<?php
/**
 * Enhanced Modern Customizer
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

/**
 * Enhanced Customizer with modern controls
 */
class Enhanced_Customizer {
	
	/**
	 * Initialize enhanced customizer
	 */
	public function init() {
		add_action( 'customize_register', [ $this, 'register_enhanced_controls' ], 20 );
		add_action( 'customize_preview_init', [ $this, 'preview_scripts' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'control_scripts' ] );
	}
	
	/**
	 * Register enhanced controls
	 */
	public function register_enhanced_controls( $wp_customize ) {
		$this->add_color_controls( $wp_customize );
		$this->add_typography_controls( $wp_customize );
		$this->add_layout_controls( $wp_customize );
		$this->add_header_controls( $wp_customize );
	}
	
	/**
	 * Add modern color controls
	 */
	private function add_color_controls( $wp_customize ) {
		// Primary Colors Section
		$wp_customize->add_section( 'nueve4_colors_primary', [
			'title' => __( 'Primary Colors', 'nueve4' ),
			'panel' => 'colors',
			'priority' => 10,
		]);
		
		// Primary Color
		$wp_customize->add_setting( 'nueve4_primary_color', [
			'default' => '#0073aa',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nueve4_primary_color', [
			'label' => __( 'Primary Color', 'nueve4' ),
			'section' => 'nueve4_colors_primary',
			'settings' => 'nueve4_primary_color',
		]));
		
		// Secondary Color
		$wp_customize->add_setting( 'nueve4_secondary_color', [
			'default' => '#005177',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nueve4_secondary_color', [
			'label' => __( 'Secondary Color', 'nueve4' ),
			'section' => 'nueve4_colors_primary',
			'settings' => 'nueve4_secondary_color',
		]));
		
		// Text Colors
		$wp_customize->add_setting( 'nueve4_text_color', [
			'default' => '#333333',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nueve4_text_color', [
			'label' => __( 'Text Color', 'nueve4' ),
			'section' => 'nueve4_colors_primary',
			'settings' => 'nueve4_text_color',
		]));
		
		// Link Colors
		$wp_customize->add_setting( 'nueve4_link_color', [
			'default' => '#0073aa',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nueve4_link_color', [
			'label' => __( 'Link Color', 'nueve4' ),
			'section' => 'nueve4_colors_primary',
			'settings' => 'nueve4_link_color',
		]));
	}
	
	/**
	 * Add modern typography controls
	 */
	private function add_typography_controls( $wp_customize ) {
		// Typography Panel
		$wp_customize->add_panel( 'nueve4_typography_panel', [
			'title' => __( 'Typography', 'nueve4' ),
			'priority' => 30,
		]);
		
		// Body Typography Section
		$wp_customize->add_section( 'nueve4_typography_body', [
			'title' => __( 'Body Typography', 'nueve4' ),
			'panel' => 'nueve4_typography_panel',
			'priority' => 10,
		]);
		
		// Body Font Family
		$wp_customize->add_setting( 'nueve4_body_font_family', [
			'default' => 'system-ui, -apple-system, sans-serif',
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_body_font_family', [
			'label' => __( 'Font Family', 'nueve4' ),
			'section' => 'nueve4_typography_body',
			'type' => 'select',
			'choices' => $this->get_font_choices(),
		]);
		
		// Body Font Size
		$wp_customize->add_setting( 'nueve4_body_font_size', [
			'default' => 16,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_body_font_size', [
			'label' => __( 'Font Size (px)', 'nueve4' ),
			'section' => 'nueve4_typography_body',
			'type' => 'range',
			'input_attrs' => [
				'min' => 12,
				'max' => 24,
				'step' => 1,
			],
		]);
		
		// Body Line Height
		$wp_customize->add_setting( 'nueve4_body_line_height', [
			'default' => 1.6,
			'sanitize_callback' => [ $this, 'sanitize_float' ],
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_body_line_height', [
			'label' => __( 'Line Height', 'nueve4' ),
			'section' => 'nueve4_typography_body',
			'type' => 'range',
			'input_attrs' => [
				'min' => 1,
				'max' => 3,
				'step' => 0.1,
			],
		]);
		
		// Headings Typography Section
		$wp_customize->add_section( 'nueve4_typography_headings', [
			'title' => __( 'Headings Typography', 'nueve4' ),
			'panel' => 'nueve4_typography_panel',
			'priority' => 20,
		]);
		
		// Headings Font Family
		$wp_customize->add_setting( 'nueve4_headings_font_family', [
			'default' => 'inherit',
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_headings_font_family', [
			'label' => __( 'Headings Font Family', 'nueve4' ),
			'section' => 'nueve4_typography_headings',
			'type' => 'select',
			'choices' => array_merge( [ 'inherit' => __( 'Inherit from Body', 'nueve4' ) ], $this->get_font_choices() ),
		]);
	}
	
	/**
	 * Add layout controls
	 */
	private function add_layout_controls( $wp_customize ) {
		// Layout Panel
		$wp_customize->add_panel( 'nueve4_layout_panel', [
			'title' => __( 'Layout', 'nueve4' ),
			'priority' => 40,
		]);
		
		// Container Section
		$wp_customize->add_section( 'nueve4_layout_container', [
			'title' => __( 'Container', 'nueve4' ),
			'panel' => 'nueve4_layout_panel',
			'priority' => 10,
		]);
		
		// Container Width
		$wp_customize->add_setting( 'nueve4_container_width', [
			'default' => 1200,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_container_width', [
			'label' => __( 'Container Width (px)', 'nueve4' ),
			'section' => 'nueve4_layout_container',
			'type' => 'range',
			'input_attrs' => [
				'min' => 800,
				'max' => 1600,
				'step' => 10,
			],
		]);
		
		// Sidebar Layout
		$wp_customize->add_setting( 'nueve4_sidebar_layout', [
			'default' => 'right',
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'refresh',
		]);
		
		$wp_customize->add_control( 'nueve4_sidebar_layout', [
			'label' => __( 'Sidebar Layout', 'nueve4' ),
			'section' => 'nueve4_layout_container',
			'type' => 'radio',
			'choices' => [
				'none' => __( 'No Sidebar', 'nueve4' ),
				'left' => __( 'Left Sidebar', 'nueve4' ),
				'right' => __( 'Right Sidebar', 'nueve4' ),
			],
		]);
	}
	
	/**
	 * Add header controls
	 */
	private function add_header_controls( $wp_customize ) {
		// Header Panel
		$wp_customize->add_panel( 'nueve4_header_panel', [
			'title' => __( 'Header', 'nueve4' ),
			'priority' => 50,
		]);
		
		// Header Layout Section
		$wp_customize->add_section( 'nueve4_header_layout', [
			'title' => __( 'Header Layout', 'nueve4' ),
			'panel' => 'nueve4_header_panel',
			'priority' => 10,
		]);
		
		// Header Style
		$wp_customize->add_setting( 'nueve4_header_style', [
			'default' => 'default',
			'sanitize_callback' => 'sanitize_text_field',
			'transport' => 'refresh',
		]);
		
		$wp_customize->add_control( 'nueve4_header_style', [
			'label' => __( 'Header Style', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'radio',
			'choices' => [
				'default' => __( 'Default', 'nueve4' ),
				'centered' => __( 'Centered', 'nueve4' ),
				'minimal' => __( 'Minimal', 'nueve4' ),
			],
		]);
		
		// Header Height
		$wp_customize->add_setting( 'nueve4_header_height', [
			'default' => 80,
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		]);
		
		$wp_customize->add_control( 'nueve4_header_height', [
			'label' => __( 'Header Height (px)', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'range',
			'input_attrs' => [
				'min' => 60,
				'max' => 120,
				'step' => 5,
			],
		]);
		
		// Sticky Header
		$wp_customize->add_setting( 'nueve4_sticky_header', [
			'default' => false,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport' => 'refresh',
		]);
		
		$wp_customize->add_control( 'nueve4_sticky_header', [
			'label' => __( 'Sticky Header', 'nueve4' ),
			'section' => 'nueve4_header_layout',
			'type' => 'checkbox',
		]);
	}
	
	/**
	 * Get font choices
	 */
	private function get_font_choices() {
		return [
			'system-ui, -apple-system, sans-serif' => __( 'System Font', 'nueve4' ),
			'Arial, sans-serif' => 'Arial',
			'Helvetica, sans-serif' => 'Helvetica',
			'Georgia, serif' => 'Georgia',
			'Times, serif' => 'Times',
			'"Courier New", monospace' => 'Courier New',
			'Verdana, sans-serif' => 'Verdana',
			'Tahoma, sans-serif' => 'Tahoma',
		];
	}
	
	/**
	 * Sanitize float values
	 */
	public function sanitize_float( $value ) {
		return floatval( $value );
	}
	
	/**
	 * Enqueue preview scripts
	 */
	public function preview_scripts() {
		wp_enqueue_script(
			'nueve4-customizer-preview',
			get_template_directory_uri() . '/assets/js/customizer-preview.js',
			[ 'customize-preview' ],
			NUEVE4_VERSION,
			true
		);
	}
	
	/**
	 * Enqueue control scripts
	 */
	public function control_scripts() {
		wp_enqueue_script(
			'nueve4-customizer-controls',
			get_template_directory_uri() . '/assets/js/customizer-controls.js',
			[ 'customize-controls' ],
			NUEVE4_VERSION,
			true
		);
	}
}