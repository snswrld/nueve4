<?php
/**
 * PWA Plugin compatibility.
 *
 * @package Nueve4\Compatibility
 */

namespace Nueve4\Compatibility;

/**
 * Class PWA
 */
class PWA {

	/**
	 * Init function.
	 *
	 * @return bool
	 */
	public function init() {
		if ( ! $this->should_load() ) {
			return false;
		}
		$this->load_hooks();

		return true;
	}

	/**
	 * Decide if class should run.
	 */
	private function should_load() {
		return defined( 'PWA_VERSION' ) && function_exists( 'wp_service_worker_error_details_template' ) && function_exists( 'pwa_get_header' ) && function_exists( 'wp_service_worker_error_message_placeholder' ) && function_exists( 'pwa_get_footer' );
	}

	/**
	 * Load hooks.
	 */
	private function load_hooks() {
		add_action( 'nueve4_do_offline', array( $this, 'offline_default_template' ) );
		add_action( 'nueve4_do_server_error', array( $this, 'server_error_default_template' ) );
	}

	/**
	 * Load offline default template.
	 */
	public function offline_default_template() {
		?>
		<main>
			<h1><?php esc_html_e( 'Oops! It looks like you&#8217;re offline.', 'nueve4' ); ?></h1>
			<?php wp_service_worker_error_message_placeholder(); ?>
		</main>
		<?php
	}

	/**
	 * Load server error template.
	 */
	public function server_error_default_template() {

		?>
		<main>
			<h1><?php esc_html_e( 'Oops! Something went wrong.', 'nueve4' ); ?></h1>
			<?php wp_service_worker_error_message_placeholder(); ?>
			<?php wp_service_worker_error_details_template(); ?>
		</main>
		<?php
	}
}
