<?php
/**
 * The main loader class for Nueve4 SDK
 *
 * @package     Nueve4SDK
 * @subpackage  Loader
 * @copyright   Copyright (c) 2024, Kemetica.io
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace Nueve4SDK;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Singleton loader for Nueve4 SDK.
 */
final class Loader {
	/**
	 * Singleton instance.
	 */
	private static $instance;
	
	/**
	 * Current loader version.
	 */
	private static $version = '1.0.0';
	
	/**
	 * Holds registered products.
	 */
	private static $products = [];
	
	/**
	 * Holds available modules to load.
	 */
	private static $available_modules = [
		'dashboard_widget',
		'rollback',
		'logger',
		'review',
		'notification',
		'welcome',
		'about_us',
	];

	/**
	 * Initialize the sdk logic.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Loader ) ) {
			self::$instance = new Loader();
		}
	}

	/**
	 * Register product into SDK.
	 */
	public static function add_product( $base_file ) {
		if ( ! is_file( $base_file ) ) {
			return self::$instance;
		}
		
		return self::$instance;
	}

	/**
	 * Get all registered modules by the SDK.
	 */
	public static function get_modules() {
		return self::$available_modules;
	}

	/**
	 * Get all products using the SDK.
	 */
	public static function get_products() {
		return self::$products;
	}

	/**
	 * Get the version of the SDK.
	 */
	public static function get_version() {
		return self::$version;
	}
}