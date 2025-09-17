<?php
/**
 * WordPress troubleshooting module.
 *
 * @package Nueve4
 */

namespace Nueve4\Admin\Troubleshoot;

/**
 * Class Main
 *
 * @package Nueve4\Admin\Troubleshoot
 */
final class Main {
	/**
	 * Init function
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'debug_information', [ $this, 'nueve4_add_debug_info' ] );

		add_filter( 'site_status_tests', [ $this, 'nueve4_add_tests' ] );
	}

	/**
	 * Register Nueve4 Accordion on Debug Info page.
	 *
	 * @param array $debug_info Debug List.
	 *
	 * @return array
	 */
	public function nueve4_add_debug_info( $debug_info ) {
		$custom_customizer_css = wp_get_custom_css();

		$debug_info['nueve4'] = array(
			'label'  => __( 'Nueve4', 'nueve4' ),
			'fields' => array(
				'api'            => array(
					'label'   => __( 'API connectivity', 'nueve4' ),
					'value'   => $this->test_api_connectivity() ? __( 'Yes', 'nueve4' ) : __( 'No', 'nueve4' ) . ' ' . get_transient( 'nueve4_troubleshoot_api_reason' ),
					'private' => false,
				),
				'child'          => array(
					'label'   => __( 'Child theme files', 'nueve4' ),
					'value'   => is_child_theme() ? $this->list_files() : __( 'No', 'nueve4' ),
					'private' => false,
				),
				'customizer_css' => array(
					'label'   => __( 'Customizer Custom CSS', 'nueve4' ),
					'value'   => empty( $custom_customizer_css ) ? __( 'No', 'nueve4' ) : $custom_customizer_css,
					'private' => false,
				),
			),
		);

		return $debug_info;
	}

	/**
	 * List active theme files
	 *
	 * @return string
	 */
	public function list_files() {
		return implode( ",\n\r", list_files( get_stylesheet_directory(), 2 ) );
	}

	/**
	 * Register tests for the Status Page
	 *
	 * @param array $tests List of tests.
	 *
	 * @return array
	 */
	public function nueve4_add_tests( $tests ) {
		$tests['direct']['nueve4_api_test'] = array(
			'label' => __( 'Nueve4', 'nueve4' ) . ' ' . __( 'API connectivity', 'nueve4' ),
			'test'  => [ $this, 'nueve4_api_test' ],
		);

		return $tests;
	}

	/**
	 * Nueve4 API test pretty response
	 *
	 * @return array
	 */
	public function nueve4_api_test() {
		$result = array(
			'label'       => __( 'Nueve4', 'nueve4' ) . ' ' . __( 'API connectivity', 'nueve4' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Nueve4', 'nueve4' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				/* translators: Theme Name */
				sprintf( __( 'API for %s is reachable.', 'nueve4' ), __( 'Nueve4', 'nueve4' ) )
			),
			'actions'     => '',
			'test'        => 'nueve4_api_test',
		);

		if ( ! $this->test_api_connectivity() ) {
			$result['status']         = 'critical';
			$result['label']          = __( 'Can not connect to API', 'nueve4' );
			$result['badge']['color'] = 'red';
			$result['description']    = sprintf(
				'<p>%s</p>',
				/* translators: Theme Name */
				sprintf( __( 'API for %s is reachable on your site.', 'nueve4' ), __( 'Nueve4', 'nueve4' ) )
			);
		}

		return $result;
	}

	/**
	 * Test API connectivity to Themeisle
	 *
	 * @return bool
	 */
	public function test_api_connectivity() {
		$transient = get_transient( 'nueve4_troubleshoot_api_response' );
		if ( $transient !== false ) {
			return ( $transient === 'yes' );
		}
		$response = nueve4_safe_get( 'https://api.themeisle.com/health' );
		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			$reason = is_wp_error( $response ) ? $response->get_error_message() : $response['response']['message'];
			set_transient( 'nueve4_troubleshoot_api_reason', $reason, 10 * MINUTE_IN_SECONDS );
			set_transient( 'nueve4_troubleshoot_api_response', 'no', 10 * MINUTE_IN_SECONDS );

			return false;
		}
		set_transient( 'nueve4_troubleshoot_api_reason', '', 10 * MINUTE_IN_SECONDS );
		set_transient( 'nueve4_troubleshoot_api_response', 'yes', 10 * MINUTE_IN_SECONDS );

		return true;
	}
}
