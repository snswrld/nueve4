<?php
/**
 * Product class for Nueve4 SDK
 *
 * @package     Nueve4SDK
 * @copyright   Copyright (c) 2024, Kemetica.io
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace Nueve4SDK;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product class for Nueve4 SDK.
 */
class Product {
	/**
	 * Product slug.
	 */
	private $slug;
	
	/**
	 * Product constructor.
	 */
	public function __construct( $base_file ) {
		$this->slug = basename( dirname( $base_file ) );
	}
	
	/**
	 * Get product slug.
	 */
	public function get_slug() {
		return $this->slug;
	}
}