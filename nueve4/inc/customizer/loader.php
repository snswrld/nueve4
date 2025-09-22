<?php
/**
 * Author:          Andrei Baicus <andrei@kemetica.io>
 * Created on:      17/08/2018
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

use HFG\Core\Components\Utility\SearchIconButton;
use Nueve4\Core\Factory;
use Nueve4\Core\Limited_Offers;
use Nueve4\Core\Settings\Config;
use Nueve4\Customizer\Options\Colors_Background;

/**
 * Main customizer handler.
 *
 * @package Nueve4\Customizer
 */
class Loader {
	const CUSTOMIZER_STYLE_HANDLE = 'nueve4-customizer-style';
	/**
	 * Customizer modules.
	 *
	 * @var array
	 */
	private $customizer_modules = array();

	/**
	 * Loader constructor.
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'set_featured_image' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls' ) );
	}

	/**
	 * Initialize the customizer functionality
	 */
	public function init() {
		global $wp_customize;

		if ( ! isset( $wp_customize ) ) {
			return;
		}
		$this->define_modules();
		$this->load_modules();
		add_action( 'customize_register', array( $this, 'change_pro_controls' ), PHP_INT_MAX );
		add_action( 'customize_register', array( $this, 'register_setting_local_gf' ) );
	}

	/**
	 * Method to modify already defined controls.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WP_Customize_Manager object.
	 */
	public function change_pro_controls( \WP_Customize_Manager $wp_customize ) {
		if ( nueve4_can_use_conditional_header() ) {
			return;
		}

		$controls_to_disable = [ 'nueve4_global_header', 'nueve4_header_conditional_selector' ];
		foreach ( $controls_to_disable as $control_slug ) {
			$wp_customize->remove_control( $control_slug );
		}
	}

	/**
	 * Define the modules that will be loaded.
	 */
	private function define_modules() {
		$this->customizer_modules = apply_filters(
			'nueve4_filter_customizer_modules',
			array(
				'Customizer\Options\Main',
				'Customizer\Options\Layout_Container',
				'Customizer\Options\Layout_Blog',
				'Customizer\Options\Layout_Single_Post',
				'Customizer\Options\Layout_Single_Page',
				'Customizer\Options\Layout_Single_Product',
				'Customizer\Options\Layout_Sidebar',
				'Customizer\Options\Typography',
				'Customizer\Options\Colors_Background',
				'Customizer\Options\Checkout',
				'Customizer\Options\Buttons',
				'Customizer\Options\Form_Fields',
				'Customizer\Options\Rtl',
				// ...existing code...
			)
		);
	}

	/**
	 * Enqueue customizer controls script.
	 */
	public function enqueue_customizer_controls() {
		if ( class_exists( '\Nueve4\Customizer\Assets_Manager' ) ) {
			\Nueve4\Customizer\Assets_Manager::enqueue_assets();
		}
	}

	/**
	 * Enqueue customizer preview script.
	 */
	public function enqueue_customizer_preview() {
		wp_enqueue_style(
			'nueve4-customizer-preview-style',
			NUEVE4_ASSETS_URL . 'css/customizer-preview' . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css',
			array(),
			NUEVE4_VERSION
		);
		wp_register_script(
			'nueve4-customizer-preview',
			NUEVE4_ASSETS_URL . 'js/build/all/customizer-preview.js',
			array(),
			NUEVE4_VERSION,
			true
		);

		$shop_has_meta = 'no';
		$shop_id       = get_option( 'woocommerce_shop_page_id' );
		if ( ! empty( $shop_id ) ) {
			$meta = get_post_meta( $shop_id, 'nueve4_meta_sidebar', true );

			if ( ! empty( $meta ) && $meta !== 'default' ) {
				$shop_has_meta = 'yes';
			}
		}

		wp_localize_script(
			'nueve4-customizer-preview',
			'nueve4CustomizePreview',
			apply_filters(
				'nueve4_customize_preview_localization',
				array(
					'currentFeaturedImage' => '',
					'newBuilder'           => nueve4_is_new_builder(),
					'newSkin'              => nueve4_is_new_skin(),
					'shopHasMetaSidebar'   => $shop_has_meta,
				)
			)
		);
		wp_enqueue_script( 'nueve4-customizer-preview' );
	}

	/**
	 * Save featured image in previously localized object.
	 */
	public function set_featured_image() {
		if ( ! is_customize_preview() ) {
			return;
		}
		if ( ! is_singular() ) {
			return;
		}
		$thumbnail = get_the_post_thumbnail_url();
		if ( $thumbnail === false ) {
			return;
		}
		wp_add_inline_script( 'nueve4-customizer-preview', 'nueve4CustomizePreview.currentFeaturedImage = "' . esc_url( get_the_post_thumbnail_url() ) . '";' );
	}

	/**
	 * Load the customizer modules.
	 *
	 * @return void
	 */
	private function load_modules() {
		$factory = new Factory( $this->customizer_modules );
		$factory->load_modules();
	}

	/**
	 * Register setting for "Toggle that enables local host of Google fonts"
	 *
	 * @param \WP_Customize_Manager $wp_customize \WP_Customize_Manager instance.
	 * @return void
	 */
	public function register_setting_local_gf( $wp_customize ) {
		$wp_customize->add_setting(
			Config::OPTION_LOCAL_GOOGLE_FONTS_HOSTING,
			[
				'type'              => 'option',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			]
		);
	}
}
