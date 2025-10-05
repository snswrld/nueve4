<?php
/**
 * Footer Fixes and Branding
 *
 * @package Nueve4
 */

namespace Nueve4;

/**
 * Fix footer positioning and branding
 */
class Footer_Fixes {
	
	/**
	 * Initialize footer fixes
	 */
	public function init() {
		add_action( 'wp_head', [ $this, 'footer_css' ] );
		add_filter( 'nueve4_footer_wrap_classes', [ $this, 'footer_classes' ] );
		add_action( 'nueve4_do_footer', [ $this, 'add_nueve4_branding' ], 999 );
		$this->remove_upsells();
	}
	
	/**
	 * Add footer CSS to prevent overlap
	 */
	public function footer_css() {
		echo '<style id="nueve4-footer-fixes">
			body { 
				min-height: 100vh; 
				display: flex; 
				flex-direction: column; 
			}
			.wrapper { 
				flex: 1; 
				display: flex; 
				flex-direction: column; 
			}
			.nueve4-main { 
				flex: 1; 
			}
			.site-footer { 
				margin-top: auto; 
				position: relative; 
				z-index: 1; 
			}
			.nueve4-branding {
				text-align: center;
				padding: 10px 0;
				background: #f8f9fa;
				border-top: 1px solid #e9ecef;
				font-size: 12px;
				color: #6c757d;
			}
			.nueve4-branding a {
				color: #0073aa;
				text-decoration: none;
			}
		</style>';
	}
	
	/**
	 * Add footer classes
	 */
	public function footer_classes( $classes ) {
		return $classes . ' nueve4-footer-fixed';
	}
	
	/**
	 * Add Nueve4 branding to footer
	 */
	public function add_nueve4_branding() {
		echo '<div class="nueve4-branding">';
		echo '<p>' . sprintf( 
			__( 'Powered by %s', 'nueve4' ), 
			'<a href="https://kemetica.io/" target="_blank">Nueve4 Theme</a>' 
		) . '</p>';
		echo '</div>';
	}
	
	/**
	 * Remove all upsells
	 */
	private function remove_upsells() {
		// Remove upsell hooks
		add_action( 'init', function() {
			remove_all_actions( 'nueve4_dashboard_upsell' );
			remove_all_actions( 'nueve4_customizer_upsell' );
		}, 999 );
		
		// Remove upsell controls from customizer
		add_action( 'customize_register', [ $this, 'remove_upsell_controls' ], 999 );
		
		// Remove template upsells
		add_filter( 'nueve4_show_starter_sites', '__return_false' );
		add_filter( 'nueve4_show_templates', '__return_false' );
		
		// Remove pro upsells
		add_filter( 'nueve4_show_pro_features', '__return_false' );
		add_filter( 'nueve4_show_upgrade_notices', '__return_false' );
	}
	
	/**
	 * Remove upsell controls from customizer
	 */
	public function remove_upsell_controls( $wp_customize ) {
		// Remove upsell sections
		$upsell_sections = [
			'nueve4_upsell_section',
			'nueve4_pro_upsell',
			'nueve4_starter_sites_upsell',
			'typography_extra_section',
		];
		
		foreach ( $upsell_sections as $section ) {
			$wp_customize->remove_section( $section );
		}
		
		// Remove upsell controls
		$upsell_controls = [
			'nueve4_upsell_control',
			'nueve4_pro_upsell_control',
			'nueve4_starter_sites_control',
		];
		
		foreach ( $upsell_controls as $control ) {
			$wp_customize->remove_control( $control );
		}
	}
}