<?php
/**
 * Theme Bootstrap Class
 *
 * @package Nueve4\Core
 */

namespace Nueve4\Core;

/**
 * Handles theme initialization and bootstrapping
 */
class Bootstrap {
	private $container;
	private $template_dir;

	public function __construct() {
		$this->container = Container::getInstance();
		$this->template_dir = get_template_directory();
	}

	public function init() {
		$this->defineConstants();
		$this->checkRequirements();
		$this->loadDependencies();
		$this->registerHooks();
	}

	private static $compatibility_features = null;

	private function defineConstants() {
		if ( ! defined( 'NUEVE4_COMPATIBILITY_FEATURES' ) ) {
			define( 'NUEVE4_COMPATIBILITY_FEATURES', $this->getCompatibilityFeatures() );
		}
	}

	private function getCompatibilityFeatures() {
		if ( null === self::$compatibility_features ) {
			self::$compatibility_features = [
				'single_customizer' => true,
				'repeater_control' => true,
				'malformed_div_on_shop' => true,
				'custom_post_types_enh' => true,
				'mega_menu' => true,
				'scroll_to_top_icons' => true,
				'palette_logo' => true,
				'custom_icon' => true,
				'link_control' => true,
				'page_header_support' => true,
				'featured_post' => true,
				'php81_react_ctrls_fix' => true,
				'gradient_picker' => true,
				'custom_post_types_sidebar' => true,
				'meta_custom_fields' => true,
				'sparks' => true,
				'advanced_search_component' => true,
				'submenu_style' => true,
				'blog_hover_effects' => true,
				'hfg_d_search_iconbutton' => true,
				'restrict_content' => true,
				'theme_dedicated_menu' => true,
				'track' => true,
				'menu_icon_svg' => true,
				'custom_payment_icons' => true,
			];
		}
		return self::$compatibility_features;
	}

	private function checkRequirements() {
		// Vendor autoloader
		$vendor_file = $this->template_dir . '/vendor/autoload.php';
		if (is_readable($vendor_file)) {
			require_once $vendor_file;
		}
	}

	private function loadDependencies() {
		// Load theme autoloader
		$autoloader_path = realpath( $this->template_dir . '/autoloader.php' );
		if ( $autoloader_path && strpos( $autoloader_path, realpath( $this->template_dir ) ) === 0 ) {
			require_once $autoloader_path;
		}
		if ( class_exists( '\Nueve4\Autoloader' ) ) {
			$autoloader = new \Nueve4\Autoloader();
			$autoloader->add_namespace('Nueve4', $this->template_dir . '/inc/');
		} else {
			return;
		}
		
		if (defined('NUEVE4_PRO_SPL_ROOT')) {
			$autoloader->add_namespace('Nueve4_Pro', NUEVE4_PRO_SPL_ROOT);
		}
		
		$autoloader->register();
	}

	private function registerHooks() {
		// Initialize core loader
		if (class_exists('\\Nueve4\\Core\\Core_Loader')) {
			new \Nueve4\Core\Core_Loader();
		}

		// Initialize pro features if available
		if (class_exists('\\Nueve4_Pro\\Core\\Loader')) {
			$this->loadProComponents();
			\Nueve4_Pro\Core\Loader::instance();
		}
	}

	private function loadProComponents() {
		if ( ! defined( 'NUEVE4_PRO_SPL_ROOT' ) ) {
			return;
		}

		$pro_components = [
			'modules/header_footer_grid/components/Yoast_Breadcrumbs.php',
			'modules/header_footer_grid/components/Language_Switcher.php'
		];

		$pro_root = realpath( NUEVE4_PRO_SPL_ROOT );
		if ( ! $pro_root ) {
			return;
		}

		foreach ( $pro_components as $component ) {
			$file = $pro_root . DIRECTORY_SEPARATOR . ltrim( $component, '/' );
			$sanitized_file = realpath( $file );
			if ( $sanitized_file && strpos( $sanitized_file, $pro_root ) === 0 && is_file( $sanitized_file ) ) {
				require_once $sanitized_file;
			}
		}
	}
}