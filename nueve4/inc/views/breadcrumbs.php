<?php
/**
 * Breadcrumbs integration file.
 *
 * @package Nueve4\Views
 */

namespace Nueve4\Views;

use RankMath\Helpers\Api;
use WPSEO_Options;


/**
 * Class Yoast
 *
 * @package Nueve4\Compatibility
 */
class Breadcrumbs extends Base_View {

	/**
	 * Module init.
	 */
	public function init() {
		add_action( 'nueve4_pro_hfg_breadcrumb', array( $this, 'render_breadcrumbs' ), 10, 2 );
		$this->load_theme_breadcrumbs();
	}

	/**
	 * Load hooks and filters.
	 */
	private function load_theme_breadcrumbs() {

		$breadcrumbs_hooks = apply_filters(
			'nueve4_breadcrumbs_locations',
			array(
				'nueve4_before_page_title',
				'nueve4_before_post_title',
			)
		);

		foreach ( $breadcrumbs_hooks as $hook ) {
			add_action( $hook, array( $this, 'render_theme_breadcrumbs' ) );
		}
	}

	/**
	 * Render breadcrumbs in Nueve4 theme.
	 *
	 * @return bool | void
	 */
	public function render_theme_breadcrumbs() {
		if ( ! $this->is_breadcrumb_enabled() ) {
			return false;
		}
		$this->render_breadcrumbs( 'small' );
	}

	/**
	 * Check if Yoast breadcrumbs are enabled.
	 *
	 * @return bool
	 */
	public function is_breadcrumb_enabled() {

		if ( ! apply_filters( 'nueve4_show_breadcrumbs', true ) ) {
			return false;
		}

		// Yoast breadcrumbs
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			return WPSEO_Options::get( 'breadcrumbs-enable', false ) === true;
		}

		// SEOPress breadcrumbs
		if ( function_exists( 'seopress_display_breadcrumbs' ) ) {
			return true;
		}

		// Rank Math breadcrumbs
		if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
			return true;
		}

		if ( function_exists( 'bcn_display' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Render Breadcrumbs.
	 *
	 * @param string $html_tag Wrapper HTML tag.
	 */
	public function render_breadcrumbs( $html_tag, $context = '' ) {
		if ( $context !== 'hfg' && is_front_page() ) {
			return;
		}
		if ( empty( $html_tag ) ) {
			$html_tag = 'small';
		}

		self::maybe_render_seo_breadcrumbs( $html_tag );
	}


	/**
	 * Render 3rd parties breadcrumbs
	 *
	 * @param string $html_tag Wrapper HTML tag.
	 * @param bool   $check Flag that controls if is needed to check if breadcrumbs are enabled in 3rd parties.
	 *
	 * @return bool
	 */
	public static function maybe_render_seo_breadcrumbs( $html_tag, $check = false ) {

		// Yoast breadcrumbs
		$yoast_breadcrumbs_enabled = true;
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			if ( $check && class_exists( 'WPSEO_Options' ) && method_exists( 'WPSEO_Options', 'get' ) ) {
				$yoast_breadcrumbs_enabled = WPSEO_Options::get( 'breadcrumbs-enable', false );
			}
			if ( $yoast_breadcrumbs_enabled ) {
				yoast_breadcrumb( '<' . esc_html( $html_tag ) . ' class="nv--yoast-breadcrumb nueve4-breadcrumbs-wrapper">', '</' . esc_html( $html_tag ) . '>' );
				return true;
			}
		}

		// SEOPress breadcrumbs
		$seopress_breadcrumbs_enabled = true;
		if ( function_exists( 'seopress_display_breadcrumbs' ) ) {
			if ( $check ) {
				$seopress_breadcrumbs_enabled = get_option( 'seopress_toggle' );
			}
			if ( $seopress_breadcrumbs_enabled ) {
				echo '<' . esc_html( $html_tag ) . ' class="nueve4-breadcrumbs-wrapper">';
				seopress_display_breadcrumbs();
				echo '</' . esc_html( $html_tag ) . '>';
				return true;
			}
		}

		// Rank Math breadcrumbs
		$rankmath_breadcrumbs_enabled = true;
		if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
			if ( $check && class_exists( '\RankMath\Helpers\Api' ) ) {
				$rankmath_breadcrumbs_enabled = Api::get_settings( 'general.breadcrumbs' );
			}

			if ( $rankmath_breadcrumbs_enabled ) {
				echo '<' . esc_html( $html_tag ) . ' class="nueve4-breadcrumbs-wrapper">';
				rank_math_the_breadcrumbs(
					[
						'wrap_before' => '<nav aria-label="breadcrumbs" class="rank-math-breadcrumb">',
						'wrap_after'  => '</nav>',
					]
				);
				echo '</' . esc_html( $html_tag ) . '>';
				return true;
			}
		}

		return false;
	}


}
