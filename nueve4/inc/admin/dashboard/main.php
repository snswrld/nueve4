<?php
/**
 * Main class of the Nueve4 Dashboard
 *
 * @package nueve4
 */

namespace Nueve4\Admin\Dashboard;

use Nueve4\Core\Limited_Offers;
use Nueve4\Core\Theme_Info;
use Nueve4\Core\Tracker;

/**
 * Class Main
 *
 * @package Nueve4\Admin\Dashboard
 */
class Main {

	use Theme_Info;
	/**
	 * Changelog Handler.
	 *
	 * @var Changelog_Handler
	 */
	private $cl_handler;
	/**
	 * Plugin Helper instance.
	 *
	 * @var Plugin_Helper
	 */
	private $plugin_helper;
	/**
	 * Current theme args.
	 *
	 * @var array
	 */
	private $theme_args = [];

	/**
	 * Useful plugins array.
	 *
	 * @var array
	 */
	private $useful_plugins = [
		'wp-cloudflare-page-cache',
		'translatepress-multilingual',
		'amp',
		'elementor',
		'woocommerce',
		'contact-form-7',
	];

	/**
	 * Plugins Cache key.
	 *
	 * @var string
	 */
	private $plugins_cache_key = 'nueve4_dash_useful_plugins';

	/**
	 * Plugins Cache Hash key.
	 *
	 * @var string
	 */
	private $plugins_cache_hash_key = 'nueve4_dash_useful_plugins_hash';

	/**
	 * Main constructor.
	 */
	public function __construct() {
		$this->plugin_helper = new Plugin_Helper();
		$this->cl_handler    = new Changelog_Handler();
	}

	/**
	 * Run WordPress attached to actions.
	 */
	public function init() {

		$this->setup_config();
		add_action( 'init', [ $this, 'setup_config' ] );
		add_action( 'admin_menu', [ $this, 'register' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'register_about_page' ), 1 );
	}

	/**
	 * Add the about page with respect to the white label settings.
	 *
	 * @return void
	 */
	public function register_about_page() {
		$theme         = wp_get_theme();
		$filtered_name = apply_filters( 'ti_wl_theme_name', $theme->__get( 'Name' ) );
		$slug          = $theme->__get( 'stylesheet' );

		if ( empty( $slug ) || empty( $filtered_name ) ) {
			return;
		}

		// We check if the name is different from the filtered name,
		// if it is, the whitelabel is in use and we should not add the about page.
		// this check allows for child themes to use the about page.
		if ( $filtered_name !== $theme->__get( 'Name' ) ) {
			return;
		}

		add_filter(
			'nueve4_about_us_metadata',
			function () use ( $filtered_name ) {

				return [
					// Top-level page in the dashboard sidebar
					'location'         => 'nueve4-welcome',
					// Logo to display on the page
					'logo'             => get_template_directory_uri() . '/assets/img/dashboard/nueve4-icon.svg',
					// Condition to show or hide the upgrade menu in the sidebar
					'has_upgrade_menu' => ! defined( 'NUEVE4_PRO_VERSION' ),
					// Add predefined product pages to the about page.
					'product_pages'    => [],
					// Upgrade menu item link & text
					'upgrade_link'     => esc_url( 'https://kemetica.io/themes/nueve4/upgrade/' ),
					'upgrade_text'     => esc_html__( 'Upgrade', 'nueve4' ) . ' ' . $filtered_name,
				];
			}
		);
	}

	/**
	 * Register Logger Setting
	 */
	public function register_settings() {
		register_setting(
			'nueve4_settings',
			'nueve4_logger_flag',
			[
				'type'         => 'string',
				'show_in_rest' => true,
				'default'      => '',
			]
		);
	}

	/**
	 * Setup the class props based on current theme.
	 */
	public function setup_config() {
		$theme = wp_get_theme();

		$this->theme_args['name']        = apply_filters( 'ti_wl_theme_name', $theme->__get( 'Name' ) );
		$this->theme_args['template']    = $theme->get( 'Template' );
		$this->theme_args['version']     = $theme->__get( 'Version' );
		$this->theme_args['description'] = apply_filters( 'ti_wl_theme_description', $theme->__get( 'Description' ) );
		$this->theme_args['slug']        = $theme->__get( 'stylesheet' );
	}

	/**
	 * Register theme options page.
	 *
	 * @return void
	 */
	public function register() {
		$theme = $this->theme_args;

		if ( empty( $theme['name'] ) || empty( $theme['slug'] ) ) {
			return;
		}

		$theme_page = ! empty( $theme['template'] ) ? $theme['template'] . '-welcome' : $theme['slug'] . '-welcome';

		$icon = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( get_template_directory() . '/assets/img/dashboard/nueve4-icon.svg' ) );
		if ( $theme['name'] !== 'Nueve4' ) {
			$icon = 'dashicons-admin-appearance';
		}
		$nueve4_icon  = apply_filters( 'nueve4_menu_icon', $icon );
		$priority   = apply_filters( 'nueve4_menu_priority', 59 );  // The position of the menu item, 60 is the position of the Appearance menu.
		$capability = 'manage_options';

		// Place a theme page in the Appearance menu, for older versions of Nueve4 Pro or TPC. to maintain backwards compatibility.
		if (
			( defined( 'NUEVE4_PRO_VERSION' ) && version_compare( NUEVE4_PRO_VERSION, '2.6.1', '<=' ) ) ||
			( defined( 'TIOB_VERSION' ) && version_compare( TIOB_VERSION, '1.1.38', '<=' ) )
		) {
			add_theme_page(
				/* translators: %s - Theme name */
				sprintf( esc_html__( '%s Options', 'nueve4' ), wp_kses_post( $theme['name'] ) ),
				/* translators: %s - Theme name */
				sprintf( esc_html__( '%s Options', 'nueve4' ), wp_kses_post( $theme['name'] ) ),
				$capability,
				'admin.php?page=nueve4-welcome'
			);
		}

		add_menu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_menu_page
			wp_kses_post( $theme['name'] ),
			wp_kses_post( $theme['name'] ),
			$capability,
			$theme_page,
			[ $this, 'render' ],
			$nueve4_icon, // The URL to the icon to be used for this menu
			$priority
		);

		// Add Dashboard submenu. Same slug as parent to allow renaming the automatic submenu that is added.
		add_submenu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_submenu_page
			$theme_page,
			/* translators: %s - Theme name */
			sprintf( esc_html__( '%s Options', 'nueve4' ), wp_kses_post( $theme['name'] ) ),
			/* translators: %s - Theme name */
			sprintf( esc_html__( '%s Options', 'nueve4' ), wp_kses_post( $theme['name'] ) ),
			$capability,
			$theme_page,
			[ $this, 'render' ]
		);

		$this->copy_customizer_page( $theme_page );

		if ( ! defined( 'NUEVE4_PRO_VERSION' ) || 'valid' !== apply_filters( 'product_nueve4_license_status', false ) ) {
			// Add Custom Layout submenu for upsell.
			add_submenu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_submenu_page
				$theme_page,
				esc_html__( 'Custom Layouts', 'nueve4' ),
				esc_html__( 'Custom Layouts', 'nueve4' ),
				$capability,
				'admin.php?page=nueve4-welcome#custom-layouts'
			);
		}
	}

	/**
	 * Copy the customizer page to the dashboard.
	 *
	 * @param string $theme_page The theme page slug.
	 *
	 * @return void
	 */
	private function copy_customizer_page( $theme_page ) {
		global $submenu;
		if ( ! isset( $submenu['themes.php'] ) ) {
			return;
		}
		$themes_menu = $submenu['themes.php'];
		if ( empty( $themes_menu ) ) {
			return;
		}
		$customize_pos = array_search( 'customize', array_column( $themes_menu, 1 ) );
		if ( false === $customize_pos ) {
			return;
		}
		$themes_page_keys = array_keys( $themes_menu );
		if ( ! isset( $themes_page_keys[ $customize_pos ] ) ) {
			return;
		}

		$customizer_menu_item = array_splice( $themes_menu, $customize_pos, 1 );
		$customizer_menu_item = reset( $customizer_menu_item );
		if ( empty( $customizer_menu_item ) ) {
			return;
		}

		add_submenu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_submenu_page
			$theme_page,
			$customizer_menu_item[0],
			$customizer_menu_item[0],
			'manage_options',
			'customize.php'
		);
	}

	/**
	 * Render the application stub.
	 *
	 * @return void
	 */
	public function render() {
		echo '<div id="nueve4-dashboard"></div>';
	}

	/**
	 * Load css and scripts for the about page
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return;
		}

		$theme      = $this->theme_args;
		$theme_page = ! empty( $theme['template'] ) ? $theme['template'] . '-welcome' : $theme['slug'] . '-welcome';

		if ( $screen->id !== 'toplevel_page_' . $theme_page ) {
			return;
		}

		$build_path   = get_template_directory_uri() . '/assets/apps/dashboard/build/';
		$dependencies = ( include get_template_directory() . '/assets/apps/dashboard/build/dashboard.asset.php' );
		
		wp_register_style( 'nueve4-dash-style', $build_path . 'style-dashboard.css', [ 'wp-components', 'nueve4-components' ], $dependencies['version'] );
		wp_style_add_data( 'nueve4-dash-style', 'rtl', 'replace' );
		wp_enqueue_style( 'nueve4-dash-style' );
		wp_register_script( 'nueve4-dash-script', $build_path . 'dashboard.js', array_merge( $dependencies['dependencies'], [ 'updates' ] ), $dependencies['version'], true );
		wp_localize_script( 'nueve4-dash-script', 'nueve4Dash', apply_filters( 'nueve4_dashboard_page_data', $this->get_localization() ) );
		wp_enqueue_script( 'nueve4-dash-script' );

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'nueve4-dash-script', 'nueve4' );
		}

		do_action( 'themeisle_sdk_dependency_enqueue_script', 'survey' );
	}

	/**
	 * Get localization data for the dashboard script.
	 *
	 * @return array
	 */
	private function get_localization() {

		$offer = new Limited_Offers();

		$old_about_config  = apply_filters( 'ti_about_config_filter', [ 'useful_plugins' => true ] );
		$theme_name        = apply_filters( 'ti_wl_theme_name', $this->theme_args['name'] );
		$plugin_name       = apply_filters( 'ti_wl_plugin_name', 'Nueve4 Pro' );
		$plugin_name_addon = apply_filters( 'ti_wl_plugin_name', 'Nueve4 Pro Addon' );
		$data              = [
			'nonce'                   => wp_create_nonce( 'wp_rest' ),
			'version'                 => 'v' . NUEVE4_VERSION,
			'assets'                  => get_template_directory_uri() . '/assets/img/dashboard/',
			'hasOldPro'               => (bool) ( defined( 'NUEVE4_PRO_VERSION' ) && version_compare( NUEVE4_PRO_VERSION, '1.1.11', '<' ) ),
			'isRTL'                   => is_rtl(),
			'isValidLicense'          => $this->has_valid_addons(),
			'notifications'           => $this->get_notifications(),
			'customizerShortcuts'     => $this->get_customizer_shortcuts(),
			'plugins'                 => $this->get_useful_plugins(),
			'featureData'             => $this->get_free_pro_features(),
			'showFeedbackNotice'      => $this->should_show_feedback_notice(),
			'allfeaturesNueve4ProURL'   => 'https://kemetica.io/themes/nueve4/upgrade/',
			'startSitesgetNueve4ProURL' => 'https://kemetica.io/themes/nueve4/upgrade/',
			'customLayoutsNueve4ProURL' => 'https://kemetica.io/themes/nueve4/upgrade/',
			'upgradeURL'              => apply_filters( 'nueve4_upgrade_link_from_child_theme_filter', 'https://kemetica.io/themes/nueve4/upgrade/' ),
			'supportURL'              => esc_url( 'https://kemetica.io/support/' ),
			'docsURL'                 => esc_url( 'https://docs.kemetica.io/nueve4/' ),
			'codexURL'                => esc_url( 'https://docs.kemetica.io/nueve4/' ),
			'strings'                 => [
				'proTabTitle'                   => wp_kses_post( $plugin_name ),
				/* translators: %s - Theme name */
				'header'                        => sprintf( __( '%s Options', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: %s - Theme name */
				'starterSitesCardDescription'   => sprintf( __( '%s now comes with a sites library with various designs to pick from. Visit our collection of demos that are constantly being added.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				'starterSitesCardUpsellMessage' => esc_html__( 'All starter sites and templates are included with Nueve4.', 'nueve4' ),
				/* translators: %s - Theme name */
				'starterSitesTabDescription'    => sprintf( __( 'With %s, you can choose from multiple unique demos, specially designed for you, that can be installed with a single click. You just need to choose your favorite, and we will take care of everything else.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: 1 - Theme name, 2 - Cloud Templates & Patterns Collection */
				'starterSitesUnavailableActive' => sprintf( __( 'Starter sites are built into %1$s and ready to use.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: %s - Theme name */
				'starterSitesUnavailableUpdate' => sprintf( __( 'All starter sites are included with %1$s.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: %s - Theme name */
				'supportCardDescription'        => sprintf( __( 'We want to make sure you have the best experience using %1$s, and that is why we have gathered all the necessary information here for you. We hope you will enjoy using %1$s as much as we enjoy creating great products.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: %s - Theme name */
				'docsCardDescription'           => sprintf( __( 'Need more details? Please check our full documentation for detailed information on how to use %s.', 'nueve4' ), wp_kses_post( $theme_name ) ),
				/* translators: %s - "Nueve4 Pro Addon" */
				'licenseCardHeading'            => sprintf( __( '%s license', 'nueve4' ), wp_kses_post( $plugin_name_addon ) ),
				/* translators: %s - "Nueve4 Pro Addon" */
				'updateOldPro'                  => sprintf( __( 'Please update %s to the latest version and then refresh this page to have access to the options.', 'nueve4' ), wp_kses_post( $plugin_name_addon ) ),
				/* translators: %1$s - Author link - Kemetica */
				'licenseCardDescription'        => sprintf(
				// translators: store name (Kemetica)
					__( 'Enter your license from %1$s purchase history in order to get plugin updates', 'nueve4' ),
					'<a target="_blank" rel="external noreferrer noopener" href="https://kemetica.io/account/">Kemetica<span class="components-visually-hidden">' . esc_html__( '(opens in a new tab)', 'nueve4' ) . '</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="components-external-link__icon" role="img" aria-hidden="true" focusable="false" style="fill: #0073AA"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a>'
				),
			],
			'changelog'               => $this->cl_handler->get_changelog( get_template_directory() . '/CHANGELOG.md' ),
			'onboarding'              => [],
			'hasFileSystem'           => WP_Filesystem(),
			'hidePluginsTab'          => apply_filters( 'nueve4_hide_useful_plugins', ! array_key_exists( 'useful_plugins', $old_about_config ) ),
			'tpcPath'                 => '',
			'tpcAdminURL'             => '',
			'pluginsURL'              => esc_url( admin_url( 'plugins.php' ) ),
			'getPluginStateBaseURL'   => esc_url( rest_url( '/nv/v1/dashboard/plugin-state/' ) ),
			'canInstallPlugins'       => current_user_can( 'install_plugins' ),
			'canActivatePlugins'      => current_user_can( 'activate_plugins' ),
			'deal'                    => ! defined( 'NUEVE4_PRO_VERSION' ) ? $offer->get_localized_data() : array(),
			'rootUrl'                 => get_site_url(),
			'daysSinceInstall'        => round( ( time() - get_option( 'nueve4_install', 0 ) ) / DAY_IN_SECONDS ),
			'proPluginVersion'        => defined( 'NUEVE4_PRO_VERSION' ) ? NUEVE4_PRO_VERSION : '',
		];

		if ( defined( 'NUEVE4_PRO_PATH' ) ) {
			$installed_plugins                     = get_plugins();
			$is_otter_installed                    = array_key_exists( 'otter-pro/otter-pro.php', $installed_plugins );
			$is_sparks_installed                   = array_key_exists( 'sparks-for-woocommerce/sparks-for-woocommerce.php', $installed_plugins );
			$data['changelogPro']                  = $this->cl_handler->get_changelog( NUEVE4_PRO_PATH . '/CHANGELOG.md' );
			$data['isOtterProInstalled']           = $is_otter_installed;
			$data['otterProInstall']               = $is_otter_installed ? esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=otter-pro%2Fotter-pro.php&plugin_status=all&paged=1&s' ), 'activate-plugin_otter-pro/otter-pro.php' ) ) : esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=install_otter_pro' ), 'install_otter_pro' ) );
			$data['sparksInstallActivateEndpoint'] = $is_sparks_installed ? esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=sparks-for-woocommerce%2Fsparks-for-woocommerce.php&plugin_status=all&paged=1&s' ), 'activate-plugin_sparks-for-woocommerce/sparks-for-woocommerce.php' ) ) : esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=install_sparks' ), 'install_sparks' ) );
			$data['moduleObserver']                = array(
				'customLayouts' => array(
					'labelSubMenu' => __( 'Custom Layouts', 'nueve4' ),
					'linkSubMenu'  => 'edit.php?post_type=nueve4_custom_layouts',
				),
			);

		}

		if ( isset( $_GET['onboarding'] ) && $_GET['onboarding'] === 'yes' ) {
			$data['isOnboarding'] = true;
		}

		return $data;
	}

	/**
	 * Get the notifications for plugin and theme updates.
	 *
	 * @return array
	 */
	public function get_notifications() {
		$notifications = [];
		$slug          = 'nueve4';
		$themes_update = get_site_transient( 'update_themes' );

		$plugin_folder = defined( 'NUEVE4_PRO_BASEFILE' ) ? basename( dirname( NUEVE4_PRO_BASEFILE ) ) : null;
		$plugin_path   = $plugin_folder ? $plugin_folder . '/nueve4-pro-addon.php' : null;

		if ( isset( $themes_update->response[ $slug ] ) ) {
			$update                = $themes_update->response[ $slug ];
			$notifications['nueve4'] = [
				// translators: s - theme name (Nueve4).
				'text'   => sprintf( __( 'New theme update for %1$s! Please update to %2$s.', 'nueve4' ), wp_kses_post( $this->theme_args['name'] ), wp_kses_post( $update['new_version'] ) ),
				'update' => [
					'type' => 'theme',
					'slug' => $slug,
				],
				'cta'    => __( 'Update Now', 'nueve4' ),
				'type'   => ( $plugin_path && is_plugin_active( $plugin_path ) ) ? 'warning' : null,
			];
		}

		if ( $plugin_path ) {
			$plugins_update = get_site_transient( 'update_plugins' );
			if ( is_plugin_active( $plugin_path ) && isset( $plugins_update->response[ $plugin_path ] ) ) {
				$update                          = $plugins_update->response[ $plugin_path ];
				$notifications['nueve4-pro-addon'] = [
					'text'   => sprintf(
					// translators: s - Pro plugin name (Nueve4 Pro)
						__( 'New plugin update for %1$s! Please update to %2$s.', 'nueve4' ),
						wp_kses_post( apply_filters( 'ti_wl_plugin_name', 'Nueve4 Pro' ) ),
						wp_kses_post( $update->new_version )
					),
					'update' => [
						'type' => 'plugin',
						'slug' => 'nueve4-pro-addon',
						'path' => $plugin_path,
					],
					'cta'    => __( 'Update Now', 'nueve4' ),
					'type'   => 'warning',
				];
			}
		}

		// Branding notice removed - all features available in GPL version

		if ( count( $notifications ) === 1 && is_plugin_active( $plugin_path ) ) {
			foreach ( $notifications as $key => $notification ) {
				/* translators: 1 - Theme Name (Nueve4), 2 - Plugin Name (Nueve4 Pro) */
				$notifications[ $key ]['text'] = sprintf( __( 'We recommend that both %1$s and %2$s are updated to the latest version to ensure optimal intercompatibility.', 'nueve4' ), wp_kses_post( $this->theme_args['name'] ), apply_filters( 'ti_wl_plugin_name', 'Nueve4 Pro' ) );
			}
		}

		$notifications = apply_filters( 'nueve4_dashboard_notifications', $notifications );

		return $notifications;
	}

	/**
	 * Should branding notice be shown.
	 *
	 * @return bool
	 */
	private function show_branding_notice() {
		if ( $this->has_valid_addons() ) {
			return false;
		}

		return time() < strtotime( '2022-07-06' );
	}

	/**
	 * Get the Customizer Shortcut Links.
	 *
	 * @return array
	 */
	private function get_customizer_shortcuts() {
		return [
			[
				'text' => esc_html__( 'Upload Logo', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[control]' => 'custom_logo' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Set Colors', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[section]' => 'nueve4_colors_background_section' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Customize Fonts', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[control]' => 'nueve4_headings_font_family' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Layout Options', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[panel]' => 'nueve4_layout' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Header Options', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[panel]' => 'hfg_header' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Blog Layouts', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[section]' => 'nueve4_blog_archive_layout' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Footer Options', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[panel]' => 'hfg_footer' ], admin_url( 'customize.php' ) ),
			],
			[
				'text' => esc_html__( 'Content / Sidebar', 'nueve4' ),
				'link' => add_query_arg( [ 'autofocus[section]' => 'nueve4_sidebar' ], admin_url( 'customize.php' ) ),
			],
		];
	}

	/**
	 * Get doc link.
	 *
	 * @param string $utm_term utm term to use for doc link.
	 * @param string $url url to doc.
	 * @return string
	 */
	private function get_doc_link( $utm_term, $url ) {
		// Replace ThemeIsle docs with Kemetica docs
		$url = str_replace( 'docs.themeisle.com', 'docs.kemetica.io', $url );
		return $url;
	}

	/**
	 * Get the pro features for the free v pro table.
	 *
	 * @return array
	 */
	private function get_free_pro_features() {
		return [
			[
				'title'       => __( 'Header/Footer builder', 'nueve4' ),
				'description' => __( 'Easily build your header and footer by dragging and dropping all the important elements in the real-time WordPress Customizer. More advanced options are available in PRO.', 'nueve4' ),
				'inLite'      => true,
				'docsLink'    => $this->get_doc_link( 'Header/Footer builder', 'https://docs.kemetica.io/nueve4/header-builder' ),
			],
			[
				'title'       => __( 'Page Builder Compatibility', 'nueve4' ),
				'description' => __( 'Nueve4 is fully compatible with Gutenberg, the new WordPress editor and for all of you page builder fans, Nueve4 has full compatibility with Elementor, Beaver Builder, and all the other popular page builders.', 'nueve4' ),
				'inLite'      => true,
				'docsLink'    => $this->get_doc_link( 'Page Builder Compatibility', 'https://docs.kemetica.io/nueve4/page-builders' ),
			],
			[
				'title'       => __( 'Header Booster', 'nueve4' ),
				'description' => __( 'Take the header builder to a new level with new awesome components: socials, contact, breadcrumbs, language switcher, multiple HTML, sticky and transparent menu, page header builder and many more.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Header Booster', 'https://docs.kemetica.io/nueve4/header-booster' ),
			],
			[
				'title'       => __( 'Page Header Builder', 'nueve4' ),
				'description' => __( 'The Page Header is the horizontal area that sits directly below the header and contains the page/post title. Easily design an attractive Page Header area using our dedicated builder.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Page Header Builder', 'https://docs.kemetica.io/nueve4/page-header' ),
			],
			[
				'title'       => __( 'Custom Layouts', 'nueve4' ),
				'description' => __( 'Powerful Custom Layouts builder which allows you to easily create your own header, footer or custom content on any of the hook locations available in the theme.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Custom Layouts', 'https://docs.kemetica.io/nueve4/custom-layouts' ),
			],
			[
				'title'       => __( 'Blog Booster', 'nueve4' ),
				'description' => __( 'Give a huge boost to your entire blogging experience with features specially designed for increased user experience.', 'nueve4' ) . ' ' . __( 'Sharing, custom article sorting, comments integrations, number of minutes needed to read an article and many more.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Blog Booster', 'https://docs.kemetica.io/nueve4/blog-booster' ),
			],
			[
				'title'       => __( 'Elementor Booster', 'nueve4' ),
				'description' => __( 'Leverage the true flexibility of Elementor with powerful addons and templates that you can import with just one click.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Elementor Booster', 'https://docs.kemetica.io/nueve4/elementor-booster' ),
			],
			[
				'title'       => __( 'WooCommerce Booster', 'nueve4' ),
				'description' => __( 'Empower your online store with awesome new features, specially designed for a smooth WooCommerce integration.', 'nueve4' ) . ' ' . __( 'Wishlist, quick view, video products, advanced reviews, multiple dedicated layouts and many more.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'WooCommerce Booster', 'https://docs.kemetica.io/nueve4/woocommerce-booster' ),
			],
			[
				'title'       => __( 'LifterLMS Booster', 'nueve4' ),
				'description' => __( 'Make your LifterLMS pages look stunning with our PRO design options. Specially created to help you set up your online courses with minimum customizations.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'LifterLMS Booster', 'https://docs.kemetica.io/nueve4/lifterlms-booster' ),
			],
			[
				'title'       => __( 'Typekit(Adobe) Fonts', 'nueve4' ),
				'description' => __( "The module allows for an easy way of enabling new awesome Adobe (previous Typekit) Fonts in Nueve4's Typography options.", 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Typekit(Adobe) Fonts', 'https://docs.kemetica.io/nueve4/typekit-fonts' ),
			],
			[
				'title'       => __( 'White Label', 'nueve4' ),
				'description' => __( "For any developer or agency out there building websites for their own clients, we've made it easy to present the theme as your own.", 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'White Label', 'https://docs.kemetica.io/nueve4/white-label' ),
			],
			[
				'title'       => __( 'Scroll To Top', 'nueve4' ),
				'description' => __( 'Simple but effective module to help you navigate back to the top of the really long pages.', 'nueve4' ),
				'inLite'      => false,
				'docsLink'    => $this->get_doc_link( 'Scroll To Top', 'https://docs.kemetica.io/nueve4/scroll-to-top' ),
			],
			[
				'title'          => __( 'See all PRO features', 'nueve4' ),
				'presentational' => true,
			],
		];
	}

	/**
	 * Get the useful plugin data.
	 *
	 * @return array
	 */
	private function get_useful_plugins() {
		$available    = get_transient( $this->plugins_cache_key );
		$hash         = get_transient( $this->plugins_cache_hash_key );
		$current_hash = substr( md5( wp_json_encode( $this->useful_plugins ) ), 0, 5 );

		if ( $available !== false && $hash === $current_hash ) {
			$available = json_decode( $available, true );
			foreach ( $available as $slug => $args ) {
				$available[ $slug ]['cta']        = ( $args['cta'] === 'external' ) ? 'external' : $this->plugin_helper->get_plugin_state( $slug );
				$available[ $slug ]['path']       = $this->plugin_helper->get_plugin_path( $slug );
				$available[ $slug ]['activate']   = $this->plugin_helper->get_plugin_action_link( $slug );
				$available[ $slug ]['deactivate'] = $this->plugin_helper->get_plugin_action_link( $slug, 'deactivate' );
				$available[ $slug ]['network']    = $this->plugin_helper->get_is_network_wide( $slug );
				$available[ $slug ]['version']    = ! empty( $available[ $slug ]['version'] ) ? $this->plugin_helper->get_plugin_version( $slug, $available[ $slug ]['version'] ) : '';
			}

			return $available;
		}

		$data = [];
		foreach ( $this->useful_plugins as $slug ) {

			if ( array_key_exists( $slug, $this->get_external_plugins_data() ) ) {
				$data[ $slug ] = $this->get_external_plugins_data()[ $slug ];
				continue;
			}

			$current_plugin = $this->plugin_helper->get_plugin_details( $slug );
			if ( $current_plugin instanceof \WP_Error ) {
				continue;
			}
			$data[ $slug ] = [
				'banner'      => $current_plugin->banners['low'],
				'name'        => html_entity_decode( $current_plugin->name ),
				'description' => html_entity_decode( $current_plugin->short_description ),
				'version'     => $current_plugin->version,
				'author'      => html_entity_decode( wp_strip_all_tags( $current_plugin->author ) ),
				'cta'         => $this->plugin_helper->get_plugin_state( $slug ),
				'path'        => $this->plugin_helper->get_plugin_path( $slug ),
				'activate'    => $this->plugin_helper->get_plugin_action_link( $slug ),
				'deactivate'  => $this->plugin_helper->get_plugin_action_link( $slug, 'deactivate' ),
				'network'     => $this->plugin_helper->get_is_network_wide( $slug ),
			];
		}

		set_transient( $this->plugins_cache_hash_key, $current_hash );
		set_transient( $this->plugins_cache_key, wp_json_encode( $data ) );

		return $data;
	}

	/**
	 * Check if feedback notice should be shown after 14 days since activation.
	 *
	 * @return bool
	 */
	private function should_show_feedback_notice() {
		$activated_time = get_option( 'nueve4_install' );
		if ( ! empty( $activated_time ) ) {
			if ( time() - intval( $activated_time ) > 14 * DAY_IN_SECONDS ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get data of external plugins that are not hosted on wp.org.
	 *
	 * @return array
	 */
	private function get_external_plugins_data() {
		// No external plugins - all recommendations from wp.org
		return [];
	}

}
