<?php
/**
 * Customizer CSS Output
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

/**
 * Generate CSS from customizer settings
 */
class Output_CSS {
	
	/**
	 * Initialize CSS output
	 */
	public function init() {
		add_action( 'wp_head', [ $this, 'output_css' ], 100 );
	}
	
	/**
	 * Output customizer CSS
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
		
		// Colors
		$primary_color = get_theme_mod( 'nueve4_primary_color', '#0073aa' );
		$secondary_color = get_theme_mod( 'nueve4_secondary_color', '#005177' );
		$text_color = get_theme_mod( 'nueve4_text_color', '#333333' );
		$link_color = get_theme_mod( 'nueve4_link_color', '#0073aa' );
		
		$css .= ':root {';
		$css .= '--nueve4-primary-color: ' . $primary_color . ';';
		$css .= '--nueve4-secondary-color: ' . $secondary_color . ';';
		$css .= '--nueve4-text-color: ' . $text_color . ';';
		$css .= '--nueve4-link-color: ' . $link_color . ';';
		$css .= '}';
		
		// Body styles
		$css .= 'body {';
		$css .= 'color: ' . $text_color . ';';
		
		$body_font_family = get_theme_mod( 'nueve4_body_font_family', 'system-ui, -apple-system, sans-serif' );
		if ( $body_font_family ) {
			$css .= 'font-family: ' . $body_font_family . ';';
		}
		
		$body_font_size = get_theme_mod( 'nueve4_body_font_size', 16 );
		if ( $body_font_size ) {
			$css .= 'font-size: ' . $body_font_size . 'px;';
		}
		
		$body_line_height = get_theme_mod( 'nueve4_body_line_height', 1.6 );
		if ( $body_line_height ) {
			$css .= 'line-height: ' . $body_line_height . ';';
		}
		
		$css .= '}';
		
		// Links
		$css .= 'a { color: ' . $link_color . '; }';
		$css .= 'a:hover { color: ' . $this->darken_color( $link_color, 20 ) . '; }';
		
		// Headings
		$headings_font_family = get_theme_mod( 'nueve4_headings_font_family', 'inherit' );
		if ( $headings_font_family && $headings_font_family !== 'inherit' ) {
			$css .= 'h1, h2, h3, h4, h5, h6 { font-family: ' . $headings_font_family . '; }';
		}
		
		// Container
		$container_width = get_theme_mod( 'nueve4_container_width', 1200 );
		if ( $container_width ) {
			$css .= '.container, .nv-container { max-width: ' . $container_width . 'px; }';
		}
		
		// Header
		$header_height = get_theme_mod( 'nueve4_header_height', 80 );
		if ( $header_height ) {
			$css .= '.site-header { min-height: ' . $header_height . 'px; }';
			$css .= '.site-header .navbar { min-height: ' . $header_height . 'px; }';
		}
		
		// Buttons
		$css .= '.btn-primary, .button-primary, input[type="submit"] {';
		$css .= 'background-color: ' . $primary_color . ';';
		$css .= 'border-color: ' . $primary_color . ';';
		$css .= '}';
		
		$css .= '.btn-primary:hover, .button-primary:hover, input[type="submit"]:hover {';
		$css .= 'background-color: ' . $this->darken_color( $primary_color, 10 ) . ';';
		$css .= 'border-color: ' . $this->darken_color( $primary_color, 10 ) . ';';
		$css .= '}';
		
		$css .= '.btn-secondary {';
		$css .= 'background-color: ' . $secondary_color . ';';
		$css .= 'border-color: ' . $secondary_color . ';';
		$css .= '}';
		
		// Sticky header
		$sticky_header = get_theme_mod( 'nueve4_sticky_header', false );
		if ( $sticky_header ) {
			$css .= '.site-header { position: sticky; top: 0; z-index: 999; }';
		}
		
		return $this->minify_css( $css );
	}
	
	/**
	 * Darken a color
	 */
	private function darken_color( $color, $percent ) {
		$color = ltrim( $color, '#' );
		$rgb = array_map( 'hexdec', str_split( $color, 2 ) );
		
		foreach ( $rgb as &$value ) {
			$value = max( 0, min( 255, $value - ( $value * $percent / 100 ) ) );
		}
		
		return '#' . implode( '', array_map( function( $val ) {
			return str_pad( dechex( $val ), 2, '0', STR_PAD_LEFT );
		}, $rgb ) );
	}
	
	/**
	 * Minify CSS
	 */
	private function minify_css( $css ) {
		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/;\s*}/', '}', $css );
		$css = str_replace( array( '; ', ' {', '{ ', ' }', '} ' ), array( ';', '{', '{', '}', '}' ), $css );
		return trim( $css );
	}
}