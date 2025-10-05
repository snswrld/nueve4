<?php
/**
 * Customizer Panel Organizer
 * Cleans up and organizes existing customizer panels
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

/**
 * Organize and clean up customizer panels
 */
class Panel_Organizer {
	
	/**
	 * Initialize panel organizer
	 */
	public function init() {
		add_action( 'customize_register', [ $this, 'reorganize_panels' ], 999 );
		add_action( 'customize_register', [ $this, 'remove_unused_controls' ], 1000 );
	}
	
	/**
	 * Reorganize panels for better UX
	 */
	public function reorganize_panels( $wp_customize ) {
		// Remove confusing/duplicate sections
		$sections_to_remove = [
			'nueve4_colors_background_section', // Replaced with better color controls
			'typography_font_pair_section', // Simplified
		];
		
		foreach ( $sections_to_remove as $section ) {
			$wp_customize->remove_section( $section );
		}
		
		// Reorganize existing panels
		$this->reorganize_header_panel( $wp_customize );
		$this->reorganize_layout_panel( $wp_customize );
		$this->clean_up_typography_panel( $wp_customize );
	}
	
	/**
	 * Remove unused/broken controls
	 */
	public function remove_unused_controls( $wp_customize ) {
		// Remove controls that don't work or are confusing
		$controls_to_remove = [
			'nueve4_global_colors', // Replaced with simpler color controls
			'nueve4_global_custom_colors',
			'nueve4_typography_font_pairs', // Simplified
		];
		
		foreach ( $controls_to_remove as $control ) {
			$wp_customize->remove_control( $control );
		}
	}
	
	/**
	 * Reorganize header panel
	 */
	private function reorganize_header_panel( $wp_customize ) {
		// Get existing header panel
		$header_panel = $wp_customize->get_panel( 'hfg_header' );
		if ( $header_panel ) {
			$header_panel->title = __( 'Header Settings', 'nueve4' );
			$header_panel->description = __( 'Customize your header layout, colors, and behavior.', 'nueve4' );
			$header_panel->priority = 50;
		}
		
		// Clean up header sections
		$header_sections = $wp_customize->sections();
		foreach ( $header_sections as $section_id => $section ) {
			if ( strpos( $section_id, 'hfg_header' ) !== false ) {
				// Simplify section titles
				if ( strpos( $section->title, 'Header' ) === false ) {
					$section->title = 'Header ' . $section->title;
				}
			}
		}
	}
	
	/**
	 * Reorganize layout panel
	 */
	private function reorganize_layout_panel( $wp_customize ) {
		$layout_panel = $wp_customize->get_panel( 'nueve4_layout' );
		if ( $layout_panel ) {
			$layout_panel->title = __( 'Layout & Structure', 'nueve4' );
			$layout_panel->description = __( 'Control the overall layout and structure of your site.', 'nueve4' );
			$layout_panel->priority = 40;
		}
	}
	
	/**
	 * Clean up typography panel
	 */
	private function clean_up_typography_panel( $wp_customize ) {
		$typography_panel = $wp_customize->get_panel( 'nueve4_typography' );
		if ( $typography_panel ) {
			$typography_panel->title = __( 'Typography & Fonts', 'nueve4' );
			$typography_panel->description = __( 'Customize fonts, sizes, and text styling throughout your site.', 'nueve4' );
			$typography_panel->priority = 30;
		}
		
		// Simplify typography sections
		$typography_sections = [
			'nueve4_typography_general' => __( 'Body Text', 'nueve4' ),
			'nueve4_typography_headings' => __( 'Headings', 'nueve4' ),
			'nueve4_typography_blog' => __( 'Blog Typography', 'nueve4' ),
		];
		
		foreach ( $typography_sections as $section_id => $title ) {
			$section = $wp_customize->get_section( $section_id );
			if ( $section ) {
				$section->title = $title;
			}
		}
	}
}