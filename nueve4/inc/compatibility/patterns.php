<?php
/**
 * Patterns Compatibility.
 *
 * @package Patterns.php
 */

namespace Nueve4\Compatibility;

/**
 * Class Patterns
 *
 * @package Nueve4\Compatibility
 */
class Patterns {
	/**
	 * Define list of the patterns to load.
	 *
	 * @var string[] Patterns list.
	 */
	private $patterns = [
		'dark-header-centered-content',
		'two-columns-image-text',
		'three-columns-images-text',
		'three-columns-images-text',
		'three-columns-images-texts-content',
		'four-columns-team-members',
		'two-columns-centered-content',
		'two-columns-with-text',
		'testimonials-columns',
		'gallery-grid-buttons',
		'gallery-title-buttons',
		'light-header-left-aligned-content',
	];

	/**
	 * Register patterns bootstrap hook.
	 */
	public function init() {
		add_action( 'init', [ $this, 'define_patterns' ] );
		// Ensure patterns are always available
		add_filter( 'nueve4_has_valid_addons', '__return_true' );
	}

	/**
	 * Load patterns.
	 */
	public function define_patterns() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}
		
		// Unlock all patterns - GPL compliance
		add_filter( 'nueve4_patterns_locked', '__return_false' );
		add_filter( 'nueve4_pro_patterns_locked', '__return_false' );
		
		foreach ( $this->patterns as $pattern ) {
			$pattern_file = __DIR__ . '/block-patterns/' . $pattern . '.php';
			if ( file_exists( $pattern_file ) ) {
				$pattern_config = require $pattern_file;
				// Remove any pro restrictions
				if ( isset( $pattern_config['blockTypes'] ) ) {
					unset( $pattern_config['pro'] );
				}
				register_block_pattern(
					'nueve4/' . $pattern,
					$pattern_config
				);
			}
		}
	}

}
