<?php
/**
 * Generic Tweaks
 *
 * @package Nueve4\Views
 */

namespace Nueve4\Views;

/**
 * Class Tweaks
 */
class Tweaks extends Base_View {
	/**
	 * Add hooks for the front end.
	 */
	public function init() {
		// Remove gallery default style.
		add_filter( 'use_default_gallery_style', '__return_false' );
	}
}
