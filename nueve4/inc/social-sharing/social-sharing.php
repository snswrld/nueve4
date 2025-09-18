<?php
/**
 * Nueve4 Social Sharing Integration
 *
 * @package Nueve4\SocialSharing
 */

namespace Nueve4\SocialSharing;

/**
 * Social Sharing Manager
 */
class Social_Sharing {
	
	private $plugin_dir;
	private $plugin_url;
	
	public function __construct() {
		$this->plugin_dir = get_template_directory() . '/inc/social-sharing/';
		$this->plugin_url = get_template_directory_uri() . '/inc/social-sharing/';
		
		$this->define_constants();
		$this->load_dependencies();
		$this->init_hooks();
	}
	
	private function define_constants() {
		if (!defined('NUEVE4_SOCIAL_VERSION')) {
			define('NUEVE4_SOCIAL_VERSION', '1.0.0');
		}
		if (!defined('NUEVE4_SOCIAL_DIR')) {
			define('NUEVE4_SOCIAL_DIR', $this->plugin_dir);
		}
		if (!defined('NUEVE4_SOCIAL_URL')) {
			define('NUEVE4_SOCIAL_URL', $this->plugin_url);
		}
		
		// Enable all premium features by default
		if (!defined('NUEVE4_SOCIAL_PRO')) {
			define('NUEVE4_SOCIAL_PRO', true);
		}
	}
	
	private function load_dependencies() {
		$blog2social_dir = dirname(get_template_directory()) . '/blog2social/';
		
		if (is_dir($blog2social_dir)) {
			// Override constants for theme integration
			if (!defined('B2S_PLUGIN_VERSION')) {
				define('B2S_PLUGIN_VERSION', '844');
			}
			if (!defined('B2S_PLUGIN_DIR')) {
				define('B2S_PLUGIN_DIR', $blog2social_dir);
			}
			if (!defined('B2S_PLUGIN_URL')) {
				define('B2S_PLUGIN_URL', get_template_directory_uri() . '/assets/social-sharing/');
			}
			
			// Load core files with premium override
			require_once $blog2social_dir . 'includes/Loader.php';
			require_once $blog2social_dir . 'includes/Tools.php';
			require_once $blog2social_dir . 'includes/System.php';
			require_once $blog2social_dir . 'includes/Options.php';
			
			// Override premium restrictions
			if (!defined('B2S_PLUGIN_USER_VERSION')) {
				define('B2S_PLUGIN_USER_VERSION', 4); // Premium Business
			}
			if (!defined('B2S_PLUGIN_TRAIL_END')) {
				define('B2S_PLUGIN_TRAIL_END', date('Y-m-d H:i:s', strtotime('+10 years')));
			}
		}
	}
	
	private function init_hooks() {
		add_action('init', [$this, 'init_social_features']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
		
		// Remove upsells and premium restrictions
		add_filter('nueve4_social_is_premium', '__return_true');
		add_filter('nueve4_social_show_upsells', '__return_false');
		add_filter('b2s_is_premium', '__return_true');
		
		// Add to admin menu
		add_action('admin_menu', [$this, 'add_admin_menu']);
	}
	
	public function init_social_features() {
		if (class_exists('B2S_Loader')) {
			$loader = new \B2S_Loader();
			$loader->load();
		}
	}
	
	public function enqueue_assets() {
		wp_enqueue_style(
			'nueve4-social-sharing',
			$this->plugin_url . 'assets/css/social-sharing.css',
			[],
			NUEVE4_SOCIAL_VERSION
		);
		
		wp_enqueue_script(
			'nueve4-social-sharing',
			$this->plugin_url . 'assets/js/social-sharing.js',
			['jquery'],
			NUEVE4_SOCIAL_VERSION,
			true
		);
	}
	
	public function enqueue_admin_assets() {
		wp_enqueue_style(
			'nueve4-social-admin',
			$this->plugin_url . 'assets/css/admin.css',
			[],
			NUEVE4_SOCIAL_VERSION
		);
	}
	
	public function add_admin_menu() {
		// Integrate with existing Blog2Social menu structure
		if (class_exists('B2S_Loader')) {
			// Remove original branding and replace with Nueve4
			remove_menu_page('blog2social');
			
			add_menu_page(
				__('Social Sharing', 'nueve4'),
				__('Social Sharing', 'nueve4'),
				'manage_options',
				'nueve4-social-sharing',
				[$this, 'admin_page'],
				'dashicons-share',
				30
			);
			
			// Add submenus with original functionality
			add_submenu_page('nueve4-social-sharing', __('Dashboard', 'nueve4'), __('Dashboard', 'nueve4'), 'manage_options', 'nueve4-social-sharing', [$this, 'admin_page']);
			add_submenu_page('nueve4-social-sharing', __('Networks', 'nueve4'), __('Networks', 'nueve4'), 'manage_options', 'nueve4-social-networks', [$this, 'networks_page']);
			add_submenu_page('nueve4-social-sharing', __('Auto Post', 'nueve4'), __('Auto Post', 'nueve4'), 'manage_options', 'nueve4-auto-post', [$this, 'autopost_page']);
			add_submenu_page('nueve4-social-sharing', __('Calendar', 'nueve4'), __('Calendar', 'nueve4'), 'manage_options', 'nueve4-social-calendar', [$this, 'calendar_page']);
			add_submenu_page('nueve4-social-sharing', __('Settings', 'nueve4'), __('Settings', 'nueve4'), 'manage_options', 'nueve4-social-settings', [$this, 'settings_page']);
		}
	}
	
	public function admin_page() {
		echo '<div class="wrap">';
		echo '<h1>' . __('Nueve4 Social Sharing', 'nueve4') . '</h1>';
		echo '<p>' . __('Manage your social media sharing and auto-posting settings.', 'nueve4') . '</p>';
		
		// Include original dashboard with rebranding
		$blog2social_views = dirname(get_template_directory()) . '/blog2social/views/b2s/';
		if (file_exists($blog2social_views . 'dashboard.php')) {
			ob_start();
			include $blog2social_views . 'dashboard.php';
			$content = ob_get_clean();
			// Remove Blog2Social branding
			$content = str_replace('Blog2Social', 'Nueve4 Social Sharing', $content);
			$content = str_replace('blog2social', 'nueve4-social-sharing', $content);
			echo $content;
		}
		
		echo '</div>';
	}
	
	public function networks_page() {
		$this->load_page('network.php', __('Social Networks', 'nueve4'));
	}
	
	public function autopost_page() {
		$this->load_page('autopost.php', __('Auto Post Settings', 'nueve4'));
	}
	
	public function calendar_page() {
		$this->load_page('post.calendar.php', __('Social Media Calendar', 'nueve4'));
	}
	
	public function settings_page() {
		$this->load_page('settings.php', __('Social Sharing Settings', 'nueve4'));
	}
	
	private function load_page($file, $title) {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html($title) . '</h1>';
		
		$blog2social_views = dirname(get_template_directory()) . '/blog2social/views/b2s/';
		if (file_exists($blog2social_views . $file)) {
			ob_start();
			include $blog2social_views . $file;
			$content = ob_get_clean();
			// Remove Blog2Social branding
			$content = str_replace('Blog2Social', 'Nueve4 Social Sharing', $content);
			$content = str_replace('blog2social', 'nueve4-social-sharing', $content);
			echo $content;
		}
		
		echo '</div>';
	}
}

// Initialize
new Social_Sharing();