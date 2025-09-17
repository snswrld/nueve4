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
	}

	/**
	 * Load patterns.
	 */
	public function define_patterns() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}
		foreach ( $this->patterns as $pattern ) {
			register_block_pattern(
				'nueve4/' . $pattern,
				require __DIR__ . '/block-patterns/' . $pattern . '.php'
			);
		}
	}

}
