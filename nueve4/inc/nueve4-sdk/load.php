<?php
/**
 * Nueve4 SDK Loader
 *
 * @package Nueve4SDK
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/Loader.php';
require_once __DIR__ . '/Product.php';

// Initialize Nueve4 SDK
\Nueve4SDK\Loader::init();

// Compatibility functions for existing code
if ( ! function_exists( 'tsdk_utmify' ) ) {
	function tsdk_utmify( $url, $medium = '', $campaign = '' ) {
		return $url;
	}
}

// Override themeisle SDK functions
add_filter( 'themeisle_sdk_products', function( $products ) {
	return [];
});

add_filter( 'themeisle_sdk_enable_telemetry', '__return_false' );