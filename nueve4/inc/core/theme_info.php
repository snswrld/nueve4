<?php
/**
 * Theme Info trait.
 *
 * @package Nueve4\Core
 */

namespace Nueve4\Core;

/**
 * Theme_Info trait
 */
trait Theme_Info {
	/**
	 * Check validity of addons plugin.
	 *
	 * @return bool
	 */
	private function has_valid_addons() {
		if ( ! defined( 'NUEVE4_PRO_BASEFILE' ) ) {
			return false;
		}

		$option_name = basename( dirname( NUEVE4_PRO_BASEFILE ) );
		$option_name = str_replace( '-', '_', strtolower( trim( $option_name ) ) );
		$status      = get_option( $option_name . '_license_data' );

		if ( ! $status ) {
			return false;
		}

		if ( ! isset( $status->license ) ) {
			return false;
		}

		if ( $status->license === 'not_active' || $status->license === 'invalid' ) {
			return false;
		}

		return true;
	}
}
