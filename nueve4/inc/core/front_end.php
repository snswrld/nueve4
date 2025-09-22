<?php
/**
 * Front end functionality
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      17/08/2018
 *
 * @package Nueve4\Core
 */

namespace Nueve4\Core;

use Nueve4\Compatibility\Elementor;
use Nueve4\Compatibility\Starter_Content;
use Nueve4\Core\Settings\Config;
use Nueve4\Core\Settings\Mods;
use Nueve4\Core\Dynamic_Css;
use Nueve4\Core\Traits\Theme_Mods;

/**
 * Front end handler class.
 *
 * @package Nueve4\Core
 */
class Front_End {
	use Theme_Mods;

	/**
	 * Theme setup.
	 */
	public function setup_theme() {
		$this->setup_content_width();
		$this->add_theme_supports();
		$this->setup_filters();
		$this->setup_menus();
		$this->setup_image_sizes();
		$this->add_amp_support();
		$this->add_woo_support();
	}

	/**
	 * Initialize textdomain at proper time.
	 */
	public function init_textdomain() {
		$this->setup_textdomain();
	}

	/**
	 * Setup content width.
	 */
	private function setup_content_width() {
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = apply_filters( 'nueve4_content_width', 1200 );
		}
	}

	/**
	 * Setup theme textdomain.
	 */
	private function setup_textdomain() {
		load_theme_textdomain( 'nueve4', get_template_directory() . '/languages' );
	}

	/**
	 * Add theme supports.
	 */
	private function add_theme_supports() {
		$supports = [
			'align-wide' => true,
			'automatic-feed-links' => true,
			'border' => true,
			'custom-background' => [],
			'custom-logo' => [
				'flex-width' => true,
				'flex-height' => true,
				'height' => 50,
				'width' => 200,
			],
			'custom-spacing' => true,
			'custom-units' => true,
			'customize-selective-refresh-widgets' => true,
			'editor-color-palette' => $this->get_gutenberg_color_palette(),
			'fl-theme-builder-footers' => true,
			'fl-theme-builder-headers' => true,
			'fl-theme-builder-parts' => true,
			'header-footer-elementor' => true,
			'html5' => [ 'search-form', 'script', 'style', 'comment-form', 'comment-list', 'gallery', 'caption' ],
			'lifterlms-sidebars' => true,
			'lifterlms' => true,
			'link-color' => true,
			'post-thumbnails' => true,
			'service_worker' => true,
			'starter-content' => ( new Starter_Content() )->get(),
			'title-tag' => true,
		];

		foreach ( $supports as $feature => $args ) {
			add_theme_support( $feature, $args );
		}
	}

	/**
	 * Setup theme filters.
	 */
	private function setup_filters() {
		add_filter( 'script_loader_tag', [ $this, 'filter_script_loader_tag' ], 10, 2 );
		add_filter( 'embed_oembed_html', [ $this, 'wrap_oembeds' ], 10, 3 );
		add_filter( 'video_embed_html', [ $this, 'wrap_jetpack_oembeds' ], 10, 1 );
		add_filter( 'wp_nav_menu_args', [ $this, 'nav_walker' ], 1001 );
		add_filter( 'theme_mod_background_color', '__return_empty_string' );
		add_filter( 'nueve4_dynamic_style_output', [ $this, 'css_global_custom_colors' ], PHP_INT_MAX, 2 );
	}

	/**
	 * Setup navigation menus.
	 */
	private function setup_menus() {
		$nav_menus = apply_filters( 'nueve4_register_nav_menus', [
			'primary' => 'Primary Menu',
			'footer' => 'Footer Menu', 
			'top-bar' => 'Secondary Menu',
		] );
		register_nav_menus( $nav_menus );
	}

	/**
	 * Setup image sizes.
	 */
	private function setup_image_sizes() {
		add_image_size( 'nueve4-blog', 930, 620, true );
	}

	/**
	 * Get Gutenberg color palette.
	 */
	private function get_gutenberg_color_palette() {
		$prefix = ( apply_filters( 'ti_wl_theme_is_localized', false ) ? __( 'Theme', 'nueve4' ) : 'Nueve4' ) . ' - ';
		$colors = $this->get_default_colors( $prefix );
		$colors = array_merge( $colors, $this->get_global_custom_color_vars() );

		$palette = [];
		foreach ( $colors as $slug => $args ) {
			$palette[] = [
				'name' => esc_html( $args['label'] ),
				'slug' => esc_html( $slug ),
				'color' => nueve4_sanitize_colors( $args['val'] ),
			];
		}

		return $palette;
	}

	/**
	 * Get default color definitions.
	 */
	private function get_default_colors( $prefix ) {
		return [
			'nueve4-link-color' => [
				'val' => 'var(--nv-primary-accent)',
				'label' => $prefix . 'Primary Accent',
			],
			'nueve4-link-hover-color' => [
				'val' => 'var(--nv-secondary-accent)',
				'label' => $prefix . 'Secondary Accent',
			],
			'nv-site-bg' => [
				'val' => 'var(--nv-site-bg)',
				'label' => $prefix . 'Site Background',
			],
			'nv-light-bg' => [
				'val' => 'var(--nv-light-bg)',
				'label' => $prefix . 'Light Background',
			],
			'nv-dark-bg' => [
				'val' => 'var(--nv-dark-bg)',
				'label' => $prefix . 'Dark Background',
			],
			'nueve4-text-color' => [
				'val' => 'var(--nv-text-color)',
				'label' => $prefix . 'Text Color',
			],
			'nv-text-dark-bg' => [
				'val' => 'var(--nv-text-dark-bg)',
				'label' => $prefix . 'Text Dark Background',
			],
			'nv-c-1' => [
				'val' => 'var(--nv-c-1)',
				'label' => $prefix . 'Extra Color 1',
			],
			'nv-c-2' => [
				'val' => 'var(--nv-c-2)',
				'label' => $prefix . 'Extra Color 2',
			],
		];
	}

	/**
	 * Returns global custom colors with css vars
	 *
	 * @return array[]
	 */
	private function get_global_custom_color_vars() {
		$css_vars = [];
		foreach ( Mods::get( Config::MODS_GLOBAL_CUSTOM_COLORS, [] ) as $slug => $args ) {
			$css_vars[ $slug ] = [
				'label' => $args['label'],
				'val'   => sprintf( 'var(--%s)', $slug ),
			];
		}

		return $css_vars;
	}

	/**
	 * Add AMP support
	 */
	private function add_amp_support() {
		if ( ! defined( 'AMP__VERSION' ) ) {
			return;
		}
		if ( version_compare( AMP__VERSION, '1.0.0', '<' ) ) {
			return;
		}
		add_theme_support(
			'amp',
			apply_filters(
				'nueve4_filter_amp_support',
				array(
					'paired' => true,
				)
			)
		);
	}

	/**
	 * Add WooCommerce support
	 */
	private function add_woo_support() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		$woocommerce_settings = apply_filters(
			'nueve4s_woocommerce_args',
			array(
				'product_grid' => array(
					'default_columns' => 3,
					'default_rows'    => 4,
					'min_columns'     => 1,
					'max_columns'     => 6,
					'min_rows'        => 1,
				),
			)
		);

		add_theme_support( 'woocommerce', $woocommerce_settings );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

	}

	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag The script tag.
	 * @param string $handle The script handle.
	 *
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag( $tag, $handle ) {
		foreach ( array( 'async', 'defer' ) as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}
			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}
			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}

	/**
	 * Wrap embeds.
	 *
	 * @param string $markup embed markup.
	 * @param string $url embed url.
	 * @param array  $attr embed attributes [width/height].
	 *
	 * @return string
	 */
	public function wrap_oembeds( $markup, $url, $attr ) {
		$sources = [
			'youtube.com',
			'youtu.be',
			'cloudup.com',
			'dailymotion.com',
			'ted.com',
			'vimeo.com',
			'speakerdeck.com',
		];
		foreach ( $sources as $source ) {
			if ( strpos( $url, $source ) !== false ) {
				return '<div class="nv-iframe-embed">' . $markup . '</div>';
			}
		}

		return $markup;
	}

	/**
	 * Wrap Jetpack embeds.
	 * Fixes the compose module aspect ratio issue.
	 *
	 * @param string $markup embed markup.
	 *
	 * @return string
	 */
	public function wrap_jetpack_oembeds( $markup ) {
		return '<div class="nv-iframe-embed">' . $markup . '</div>';
	}

	/**
	 * Tweak menu walker to support selective refresh.
	 *
	 * @param array $args List of arguments for navigation.
	 *
	 * @return mixed
	 */
	public function nav_walker( $args ) {
		if ( isset( $args['walker'] ) && is_string( $args['walker'] ) && class_exists( $args['walker'] ) ) {
			$args['walker'] = new $args['walker']();
		}

		return $args;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		$this->add_styles();
		$this->add_inline_styles();
		$this->add_scripts();
	}

	/**
	 * Enqueue inline styles for core components.
	 */
	private function add_inline_styles() {

		// Add Inline styles if buttons shadows are being used.
		$primary_values   = get_theme_mod( Config::MODS_BUTTON_PRIMARY_STYLE, nueve4_get_button_appearance_default() );
		$secondary_values = get_theme_mod( Config::MODS_BUTTON_SECONDARY_STYLE, nueve4_get_button_appearance_default( 'secondary' ) );

		$style = '';

		if (
			( isset( $primary_values['useShadow'] ) && ! empty( $primary_values['useShadow'] ) ) ||
			( isset( $primary_values['useShadowHover'] ) && ! empty( $primary_values['useShadowHover'] ) ) ||
			( isset( $secondary_values['useShadow'] ) && ! empty( $secondary_values['useShadow'] ) ) ||
			( isset( $secondary_values['useShadowHover'] ) && ! empty( $secondary_values['useShadowHover'] ) )
		) {
			$button_shadow_css = [
				'.button.button-primary, .is-style-primary .wp-block-button__link {box-shadow: var(--primarybtnshadow, none);}',
				'.button.button-primary:hover, .is-style-primary .wp-block-button__link:hover {box-shadow: var(--primarybtnhovershadow, none);}',
				'.button.button-secondary, .is-style-secondary .wp-block-button__link {box-shadow: var(--secondarybtnshadow, none);}',
				'.button.button-secondary:hover, .is-style-secondary .wp-block-button__link:hover {box-shadow: var(--secondarybtnhovershadow, none);}'
			];
			$style .= implode( ' ', $button_shadow_css );;
		}

		foreach ( nueve4_get_headings_selectors() as $heading_id => $heading_selector ) {
			$font_family = get_theme_mod( $this->get_mod_key_heading_fontfamily( $heading_id ), '' ); // default value is empty string to be consistent with default customizer control value.

			$css_var = sprintf( '--%1$sfontfamily', $heading_id );

			if ( is_customize_preview() ) {
				$style .= sprintf( '%s {font-family: var(%s, var(--headingsfontfamily)), var(--nv-fallback-ff);} ', $heading_id, $css_var ); // fallback values for the first page load on the customizer
				continue;
			}

			// If font family is inherit, do not add a style for this heading.
			if ( $font_family === '' ) {
				continue;
			}

			$style .= sprintf( '%s {font-family: var(%s);}', $heading_id, $css_var );
		}

		if ( empty( $style ) ) {
			return;
		}

		wp_add_inline_style( 'nueve4-style', Dynamic_Css::minify_css( $style ) );
	}

	/**
	 * Enqueue styles.
	 */
	private function add_styles() {
		if ( class_exists( 'WooCommerce', false ) ) {
			$style_path = 'css/woocommerce';

			wp_register_style( 'nueve4-woocommerce', NUEVE4_ASSETS_URL . $style_path . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css', array(), apply_filters( 'nueve4_version_filter', NUEVE4_VERSION ) );
			wp_style_add_data( 'nueve4-woocommerce', 'rtl', 'replace' );
			wp_style_add_data( 'nueve4-woocommerce', 'suffix', '.min' );
			if ( ! Elementor::is_elementor_checkout() ) {
				wp_enqueue_style( 'nueve4-woocommerce' );
			}
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {

			$style_path = 'css/easy-digital-downloads';

			wp_register_style( 'nueve4-easy-digital-downloads', NUEVE4_ASSETS_URL . $style_path . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css', array(), apply_filters( 'nueve4_version_filter', NUEVE4_VERSION ) );
			wp_style_add_data( 'nueve4-easy-digital-downloads', 'rtl', 'replace' );
			wp_style_add_data( 'nueve4-easy-digital-downloads', 'suffix', '.min' );
			wp_enqueue_style( 'nueve4-easy-digital-downloads' );

		}

		$style_path = '/style-main-new';

		wp_register_style( 'nueve4-style', get_template_directory_uri() . $style_path . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css', array(), apply_filters( 'nueve4_version_filter', NUEVE4_VERSION ) );
		wp_style_add_data( 'nueve4-style', 'rtl', 'replace' );
		wp_style_add_data( 'nueve4-style', 'suffix', '.min' );
		wp_enqueue_style( 'nueve4-style' );

		$mm_path = 'mega-menu';

		wp_register_style( 'nueve4-mega-menu', get_template_directory_uri() . '/assets/css/' . $mm_path . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css', array(), apply_filters( 'nueve4_version_filter', NUEVE4_VERSION ) );
		wp_style_add_data( 'nueve4-mega-menu', 'rtl', 'replace' );
		wp_style_add_data( 'nueve4-mega-menu', 'suffix', '.min' );
	}

	/**
	 * Enqueue scripts.
	 */
	private function add_scripts() {
		if ( nueve4_is_amp() ) {
			return;
		}

		wp_register_script( 'nueve4-script', NUEVE4_ASSETS_URL . 'js/build/modern/frontend.js', apply_filters( 'nueve4_filter_main_script_dependencies', array() ), NUEVE4_VERSION, true );

		wp_localize_script(
			'nueve4-script',
			'Nueve4Properties',
			apply_filters(
				'nueve4_filter_main_script_localization',
				array(
					'ajaxurl'     => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'       => wp_create_nonce( 'wp_rest' ),
					'isRTL'       => is_rtl(),
					'isCustomize' => is_customize_preview(),
				)
			)
		);
		wp_enqueue_script( 'nueve4-script' );
		wp_script_add_data( 'nueve4-script', 'async', true );
		$inline_scripts = apply_filters( 'hfg_component_scripts', '' );
		if ( ! empty( $inline_scripts ) ) {
			wp_add_inline_script( 'nueve4-script', $inline_scripts );
		}

		if ( class_exists( 'WooCommerce', false ) && is_woocommerce() ) {
			wp_register_script( 'nueve4-shop-script', NUEVE4_ASSETS_URL . 'js/build/modern/shop.js', array(), NUEVE4_VERSION, true );
			wp_enqueue_script( 'nueve4-shop-script' );
			wp_script_add_data( 'nueve4-shop-script', 'async', true );
		}

		if ( $this->should_load_comments_reply() ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Dequeue comments-reply script if comments are closed.
	 *
	 * @return bool
	 */
	public function should_load_comments_reply() {

		if ( ! is_singular() ) {
			return false;
		}

		if ( ! comments_open() ) {
			return false;
		}

		if ( ! (bool) get_option( 'thread_comments' ) ) {
			return false;
		}

		if ( post_password_required() ) {
			return false;
		}

		$post_type = get_post_type();
		if ( ! post_type_supports( $post_type, 'comments' ) ) {
			return false;
		}

		if ( ! apply_filters( 'nueve4_post_has_comments', false ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register widgets for the theme.
	 *
	 * @since    1.0.0
	 */
	public function register_sidebars() {
		$sidebars = array(
			'blog-sidebar' => 'Sidebar',
			'shop-sidebar' => 'Shop Sidebar',
		);

		$footer_sidebars = apply_filters(
			'nueve4_footer_widget_areas_array',
			array(
				'footer-one-widgets'   => 'Footer One',
				'footer-two-widgets'   => 'Footer Two',
				'footer-three-widgets' => 'Footer Three',
				'footer-four-widgets'  => 'Footer Four',
			)
		);

		$sidebars = array_merge( $sidebars, $footer_sidebars );

		foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
			$sidebar_settings = array(
				'name'          => $sidebar_name,
				'id'            => $sidebar_id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<p class="widget-title">',
				'after_title'   => '</p>',
			);
			register_sidebar( $sidebar_settings );
		}
	}

	/**
	 * Get strings.
	 *
	 * @return array
	 */
	public function get_strings() {
		return [
			'add_item'                 => 'Add item',
			'add_items'                => 'Add items by clicking the ones below.',
			'all_selected'             => 'All items are already selected.',
			'page_layout'              => 'Page Layout',
			'page_title'               => 'Page Title',
			'upsell_components'        => 'Upgrade to Nueve4 Pro and unlock all components, including Wish List, Breadcrumbs, Custom Layouts and many more.',
			'header_booster'           => 'Header Booster',
			'blog_booster'             => 'Blog Booster',
			'woo_booster'              => 'WooCommerce Booster',
			'custom_layouts'           => 'Custom Layouts',
			'white_label'              => 'White Label module',
			'scroll_to_top'            => 'Scroll to Top module',
			'elementor_booster'        => 'Elementor Booster',
			'ext_h_description'        => 'Extend your header with more components and settings, build sticky/transparent headers or display them conditionally.',
			'ctm_h_description'        => 'Easily create custom headers and footers as well as adding your own custom code or content in any of the hooks locations.',
			'elem_description'         => 'Leverage the true flexibility of Elementor with powerful addons and templates that you can import with just one click.',
			'get_pro_cta'              => 'Get the PRO version!',
			'opens_new_tab_des'        => '(opens in a new tab)',
			'filter'                   => 'Filter',
			'nueve4_options'             => '%s Options',
			'migrate_builder_d'        => 'Migrating builder data',
			'rollback_builder'         => 'Rolling back builder',
			'remove_old_data'          => 'Removing old data',
			'customizer_values_notice' => 'You must save the current customizer values before running the migration.',
			'wrong_reload_notice'      => 'Something went wrong. Please reload the page and try again.',
			'rollback_to_old'          => 'Want to roll back to the old builder?',
			'new_hfg_experience'       => "We've created a new Header/Footer Builder experience! You can always roll back to the old builder from right here.",
			'manual_adjust'            => 'Some manual adjustments may be required.',
			'reload'                   => 'Reload',
			'migrate'                  => 'Migrate Builders Data',
			'legacy_skin'              => 'Legacy Skin',
			'nueve4_30'                  => 'Nueve4 3.0',
			'switching_skin'           => 'Switching skin',
			'switch_skin'              => 'Switch Skin',
			'dismiss'                  => 'Dismiss',
			'rollback'                 => 'Roll Back',
		];
	}

	/**
	 * Adds CSS rules to resolve .has-dynamicslug-color .has-dynamicslug-background-color classes.
	 *
	 * @param  string $current_styles Current dynamic style.
	 * @param  string $context gutenberg|frontend Represents the type of the context.
	 * @return string dynamic styles has resolving global custom colors
	 */
	public function css_global_custom_colors( $current_styles, $context ) {
		if ( $context !== 'frontend' ) {
			return $current_styles;
		}

		foreach ( Mods::get( Config::MODS_GLOBAL_CUSTOM_COLORS, [] ) as $slug => $args ) {
			$css_var         = sprintf( 'var(--%s) !important', $slug );
			$current_styles .= Dynamic_CSS::minify_css( sprintf( '.has-%s-color {color:%s} .has-%s-background-color {background-color:%s}', $slug, $css_var, $slug, $css_var ) );
		}

		return $current_styles;
	}

	/**
	 * Get mod key for heading font family
	 *
	 * @param string $heading_id Heading ID (h1, h2, etc.)
	 * @return string Mod key for heading font family
	 */
	private function get_mod_key_heading_fontfamily( $heading_id ) {
		return 'nueve4_' . $heading_id . '_font_family';
	}
}
