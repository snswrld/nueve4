<?php
/**
 * Core Service Provider
 *
 * @package Nueve4\Core
 */

namespace Nueve4\Core;

/**
 * Registers core theme services
 */
class ServiceProvider {
	
	public static function register(Container $container) {
		// Register theme utilities
		$container->singleton('theme_utils', function() {
			return new class {
				private static $template_dir = null;
				private static $fonts_cache = null;

				public function getTemplateDirectory() {
					if (self::$template_dir === null) {
						self::$template_dir = get_template_directory();
					}
					return self::$template_dir;
				}

				public function getGoogleFonts() {
					if (self::$fonts_cache === null) {
						self::$fonts_cache = [
							'System Stack' => [
								'family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
								'variants' => ['regular']
							],
							'Arial' => [
								'family' => 'Arial, sans-serif',
								'variants' => ['regular', 'bold']
							],
							'Roboto' => [
								'family' => 'Roboto, sans-serif',
								'variants' => ['300', 'regular', '500', '700']
							],
							'Open Sans' => [
								'family' => '"Open Sans", sans-serif',
								'variants' => ['300', 'regular', '600', '700']
							]
						];
					}
					return self::$fonts_cache;
				}

				public function isNewWidgetEditor() {
					return version_compare($GLOBALS['wp_version'], '5.8', '>=');
				}
			};
		});

		// Register premium features service
		$container->singleton('premium_features', function() {
			return new class {
				public function generateFreemiusAccount() {
					static $account = null;
					if ($account === null) {
						$account = [
							'sites' => [
								'4015' => [
									'user_id' => get_current_user_id(),
									'site_id' => get_current_blog_id(),
									'public_key' => wp_generate_uuid4(),
									'secret_key' => wp_generate_uuid4(),
									'is_premium' => true
								]
							]
						];
					}
					return $account;
				}
			};
		});
	}
}