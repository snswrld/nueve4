<?php
/**
 * Control Converter
 * Converts problematic text controls to proper control types
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

/**
 * Convert problematic controls to proper types
 */
class Control_Converter {
	
	/**
	 * Initialize control converter
	 */
	public function init() {
		add_action( 'customize_register', [ $this, 'convert_controls' ], 1001 );
	}
	
	/**
	 * Convert problematic controls
	 */
	public function convert_controls( $wp_customize ) {
		$this->convert_boolean_controls( $wp_customize );
		$this->convert_color_controls( $wp_customize );
		$this->convert_number_controls( $wp_customize );
	}
	
	/**
	 * Convert text fields that should be checkboxes
	 */
	private function convert_boolean_controls( $wp_customize ) {
		$boolean_controls = [
			'nueve4_sticky_header' => __( 'Enable Sticky Header', 'nueve4' ),
			'nueve4_show_breadcrumbs' => __( 'Show Breadcrumbs', 'nueve4' ),
			'nueve4_enable_masonry' => __( 'Enable Masonry Layout', 'nueve4' ),
			'nueve4_show_author_avatar' => __( 'Show Author Avatar', 'nueve4' ),
			'nueve4_enable_infinite_scroll' => __( 'Enable Infinite Scroll', 'nueve4' ),
		];
		
		foreach ( $boolean_controls as $control_id => $label ) {
			$existing_control = $wp_customize->get_control( $control_id );
			if ( $existing_control && $existing_control->type === 'text' ) {
				// Remove old control
				$wp_customize->remove_control( $control_id );
				
				// Get existing setting
				$setting = $wp_customize->get_setting( $control_id );
				if ( $setting ) {
					$setting->sanitize_callback = 'wp_validate_boolean';
				}
				
				// Add new checkbox control
				$wp_customize->add_control( $control_id, [
					'label' => $label,
					'section' => $existing_control->section,
					'type' => 'checkbox',
					'priority' => $existing_control->priority,
				]);
			}
		}
	}
	
	/**
	 * Convert text fields that should be color pickers
	 */
	private function convert_color_controls( $wp_customize ) {
		$color_controls = [
			'nueve4_header_text_color' => __( 'Header Text Color', 'nueve4' ),
			'nueve4_footer_background_color' => __( 'Footer Background Color', 'nueve4' ),
			'nueve4_button_color' => __( 'Button Color', 'nueve4' ),
		];
		
		foreach ( $color_controls as $control_id => $label ) {
			$existing_control = $wp_customize->get_control( $control_id );
			if ( $existing_control && $existing_control->type === 'text' ) {
				// Remove old control
				$wp_customize->remove_control( $control_id );
				
				// Get existing setting
				$setting = $wp_customize->get_setting( $control_id );
				if ( $setting ) {
					$setting->sanitize_callback = 'sanitize_hex_color';
					$setting->transport = 'postMessage';
				}
				
				// Add new color control
				$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, $control_id, [
					'label' => $label,
					'section' => $existing_control->section,
					'priority' => $existing_control->priority,
				]));
			}
		}
	}
	
	/**
	 * Convert text fields that should be number/range controls
	 */
	private function convert_number_controls( $wp_customize ) {
		$number_controls = [
			'nueve4_container_width' => [
				'label' => __( 'Container Width (px)', 'nueve4' ),
				'type' => 'range',
				'input_attrs' => [ 'min' => 800, 'max' => 1600, 'step' => 10 ],
			],
			'nueve4_sidebar_width' => [
				'label' => __( 'Sidebar Width (%)', 'nueve4' ),
				'type' => 'range',
				'input_attrs' => [ 'min' => 20, 'max' => 40, 'step' => 1 ],
			],
			'nueve4_header_height' => [
				'label' => __( 'Header Height (px)', 'nueve4' ),
				'type' => 'range',
				'input_attrs' => [ 'min' => 60, 'max' => 120, 'step' => 5 ],
			],
		];
		
		foreach ( $number_controls as $control_id => $config ) {
			$existing_control = $wp_customize->get_control( $control_id );
			if ( $existing_control && $existing_control->type === 'text' ) {
				// Remove old control
				$wp_customize->remove_control( $control_id );
				
				// Get existing setting
				$setting = $wp_customize->get_setting( $control_id );
				if ( $setting ) {
					$setting->sanitize_callback = 'absint';
					$setting->transport = 'postMessage';
				}
				
				// Add new range control
				$wp_customize->add_control( $control_id, [
					'label' => $config['label'],
					'section' => $existing_control->section,
					'type' => $config['type'],
					'priority' => $existing_control->priority,
					'input_attrs' => $config['input_attrs'],
				]);
			}
		}
	}
}