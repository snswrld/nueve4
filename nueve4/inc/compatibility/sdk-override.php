<?php
/**
 * SDK Override Compatibility
 * 
 * Disables external SDK calls and replaces with Nueve4 branded alternatives
 *
 * @package Nueve4\Compatibility
 */

namespace Nueve4\Compatibility;

/**
 * Class SDK_Override
 */
class SDK_Override {
	
	/**
	 * Initialize SDK overrides
	 */
	public function init() {
		// Disable ThemeIsle SDK
		add_filter( 'themeisle_sdk_products', '__return_empty_array' );
		add_filter( 'themeisle_sdk_enable_telemetry', '__return_false' );
		
		// Override external functions
		add_action( 'init', [ $this, 'override_functions' ], 1 );
		
		// Remove external upsells
		add_filter( 'nueve4_dashboard_notifications', [ $this, 'filter_notifications' ] );
		
		// Unlock premium features
		add_filter( 'nueve4_has_valid_addons', '__return_true' );
		add_filter( 'nueve4_pro_addon_is_active', '__return_true' );
	}
	
	/**
	 * Override external functions
	 */
	public function override_functions() {
		if ( ! function_exists( 'tsdk_utmify' ) ) {
			function tsdk_utmify( $url, $medium = '', $campaign = '' ) {
				return $url;
			}
		}
	}
	
	/**
	 * Filter dashboard notifications to remove external promotions
	 */
	public function filter_notifications( $notifications ) {
		// Remove any ThemeIsle/external promotions
		$filtered = [];
		foreach ( $notifications as $key => $notification ) {
			if ( ! isset( $notification['url'] ) || 
				 strpos( $notification['url'], 'themeisle.com' ) === false ) {
				$filtered[ $key ] = $notification;
			}
		}
		return $filtered;
	}
}