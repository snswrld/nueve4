<?php
/**
 * FSE Compatibility.
 *
 * @package Nueve4\Compatibility
 */

namespace Nueve4\Compatibility;

use Nueve4\Core\Dynamic_Css;
use Nueve4\Customizer\Loader;
use WP_Admin_Bar;
use WP_Customize_Manager;

/**
 * Class Fse
 */
class Fse {
	/**
	 * Theme mod used for main flag.
	 */
	const FSE_ENABLED_SLUG = 'nueve4_enable_fse_templates';

	const CUSTOMIZER_NOTIFICATION = 'site_editor_block_theme_notice';

	/**
	 * Templates.
	 *
	 * @var array
	 */
	private $templates = [];

	/**
	 * Customizer Section.
	 *
	 * @var string
	 */
	private $customize_section = 'nueve4_fse';

	/**
	 * Fse constructor.
	 */
	public function __construct() {
		$this->templates = [
			'index'      => __( 'Blog', 'nueve4' ),
			'front-page' => __( 'Front Page', 'nueve4' ),
			'archive'    => __( 'Archive', 'nueve4' ),
			'404'        => '404',
			'search'     => __( 'Search', 'nueve4' ),
			'page'       => __( 'Page', 'nueve4' ),
			'single'     => __( 'Single Post', 'nueve4' ),
		];
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		if ( ! class_exists( '\WP_Theme_JSON_Data', false ) ) {
			return;
		}

		// Customizer.
		add_action( 'customize_register', [ $this, 'add_controls' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'add_styles' ] );

		// Remove site editor menu in admin bar and dashboard.
		add_action( 'admin_bar_menu', [ $this, 'remove_admin_bar_menu' ], PHP_INT_MAX );
		add_action( 'admin_menu', [ $this, 'remove_dashboard_menu' ], PHP_INT_MAX );

		// Filter out block templates (they load by default).
		add_filter( 'get_block_templates', [ $this, 'filter_templates' ], 10, 3 );


		add_action( 'admin_init', [ $this, 'shortcircuit_redirect' ] );

		// Theme header/footer
		add_action( 'wp_body_open', [ $this, 'handle_header' ], PHP_INT_MAX );
		add_action( 'wp_footer', [ $this, 'handle_footer' ], PHP_INT_MIN );

	}

	/**
	 * Remove admin bar menu item.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar the WP_Admin_Bar instance.
	 *
	 * @return void
	 */
	public function remove_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
		if ( $this->is_enabled() ) {
			return;
		}
		$wp_admin_bar->remove_node( 'site-editor' );
	}

	/**
	 * Remove dashboard menu item.
	 *
	 * @return void
	 */
	public function remove_dashboard_menu() {
		if ( $this->is_enabled() ) {
			return;
		}
		remove_submenu_page( 'themes.php', 'site-editor.php' );
	}

	/**
	 * Shortcircuits the redirect to the site editor.
	 * This is needed because the site editor sometimes breaks depending on what is enabled in the customizer Full Site Editing panel.
	 *
	 * @return void
	 */
	public function shortcircuit_redirect() {
		if ( ! $this->should_load( true ) ) {
			return;
		}

		global $pagenow;

		if ( $pagenow !== 'site-editor.php' ) {
			return;
		}

		if ( get_option( 'show_on_front' ) === 'page' && get_option( 'page_on_front' ) ) {
			if ( $this->is_template_enabled( 'front-page' ) ) {
				return;
			}

			if ( isset( $_GET['postType'] ) && $_GET['postType'] !== 'wp_template' && isset( $_GET['postId'] ) ) {
				$this->do_redirect();
			}
		} else {
			if ( $this->is_template_enabled( 'index' ) ) {
				return;
			}

			$this->do_redirect();
		}
	}

	/**
	 * Redirect to generic site editor URL.
	 *
	 * @return void
	 */
	private function do_redirect() {
		wp_safe_redirect( add_query_arg( 'postType', 'wp_template', admin_url( 'site-editor.php' ) ) );

		exit;
	}

	/**
	 * Set up the conditions to check if we're on a specific template.
	 *
	 * @return array
	 */
	public function get_template_conditions() {
		return [
			'index'      => $this->is_blog(),
			'front-page' => $this->is_front_page(),
			'archive'    => is_post_type_archive( 'post' ) && ! $this->is_blog(),
			'404'        => is_404(),
			'search'     => is_search(),
			'page'       => $this->is_single_page(),
			'single'     => is_singular( 'post' ),
		];
	}

	/**
	 * Handle header.
	 *
	 * @return void
	 */
	public function handle_header() {
		$template = $this->get_template_slug();

		if ( ! $this->is_template_enabled( $template ) ) {
			return;
		}

		$header_classes = apply_filters( 'nv_header_classes', 'header' );
		?>

		<div class="wrapper">
		<?php do_action( 'nueve4_before_header_wrapper_hook' ); ?>

		<header class="<?php echo esc_attr( $header_classes ); ?>" <?php echo ( nueve4_is_amp() ) ? 'next-page-hide' : ''; ?> >
			<a class="nueve4-skip-link show-on-focus" href="#content">
				<?php echo __( 'Skip to content', 'nueve4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php
			do_action( 'nueve4_before_header_hook' );

			if ( apply_filters( 'nueve4_filter_toggle_content_parts', true, 'header' ) === true ) {
				$this->handle_theme_part( 'header', $template );
			}

			do_action( 'nueve4_after_header_hook' );
			?>
		</header>

		<?php
		do_action( 'nueve4_after_header_wrapper_hook' );
		do_action( 'nueve4_before_primary' );
		?>
		<main id="content" class="nueve4-main">
		<?php
		do_action( 'nueve4_after_primary_start' );
	}

	/**
	 * Handle footer.
	 *
	 * @return void
	 */
	public function handle_footer() {
		$template = $this->get_template_slug();

		if ( ! $this->is_template_enabled( $template ) ) {
			return;
		}

		do_action( 'nueve4_before_primary_end' );
		?>
		</main><!--/.nueve4-main-->
		<?php
		do_action( 'nueve4_after_primary' );

		if ( apply_filters( 'nueve4_filter_toggle_content_parts', true, 'footer' ) === true ) {
			do_action( 'nueve4_before_footer_hook' );
			$this->handle_theme_part( 'footer', $template );
			do_action( 'nueve4_after_footer_hook' );
		}
		?>
		</div><!--/.wrapper-->
		<?php
	}

	/**
	 * Handle theme part.
	 *
	 * @param string $part the theme part to handle - [header|footer].
	 * @param string $template current template.
	 *
	 * @return void
	 */
	public function handle_theme_part( $part, $template ) {
		if ( ! in_array( $part, [ 'header', 'footer' ], true ) ) {
			return;
		}

		if ( ! $this->should_load() ) {
			return;
		}

		$option = $part === 'header' ? $this->get_option_slug_for_header( $template ) : $this->get_option_slug_for_footer( $template );

		if ( get_theme_mod( $option, true ) !== true ) {
			return;
		}

		do_action( 'nueve4_do_' . $part );
	}

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 * @param \WP_Block_Template[] $query_result Array of found block templates.
	 * @param array                $query Arguments to retrieve templates.
	 * @param string               $template_type wp_template or wp_template_part.
	 */
	public function filter_templates( $query_result, $query, $template_type ) {
		if ( $template_type !== 'wp_template' ) {
			return $query_result;
		}

		if ( ! $this->is_enabled() ) {
			return [];
		}

		foreach ( $query_result as $key => $template ) {
			// Skip if this is not defined by the theme itself. Allow all other templates to fall through.
			if ( ! in_array( $template->slug, array_keys( $this->templates ) ) ) {
				continue;
			}

			$enabled    = $this->is_template_enabled( $template->slug );
			$conditions = $this->get_template_conditions();


			// Still need to load all templates in admin.
			if ( ! is_admin() ) {
				if ( ! isset( $conditions[ $template->slug ] ) || $conditions[ $template->slug ] !== true ) {
					$enabled = false;
				}
				// Page should not affect the front page.
				if ( $template->slug === 'page' && $this->is_front_page() ) {
					$enabled = false;
				}

				// Don't pass through the index template if we're on archive.
				if ( $template->slug === 'index' && ! $this->is_blog() ) {
					$enabled = false;
				}
			}

			if ( $enabled ) {
				continue;
			}

			unset( $query_result[ $key ] );
		}

		return $query_result;
	}

	/**
	 * Add customizer controls.
	 *
	 * @param WP_Customize_Manager $wp_customize the customizer manager.
	 *
	 * @return void
	 */
	public function add_controls( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			$this->customize_section,
			array(
				'title'    => __( 'Full Site Editing', 'nueve4' ),
				'priority' => 1000,
			)
		);

		$wp_customize->add_setting(
			self::FSE_ENABLED_SLUG,
			array(
				'default'           => false,
				'sanitize_callback' => 'nueve4_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			self::FSE_ENABLED_SLUG,
			array(
				'label'   => __( 'Full Site Editing', 'nueve4' ),
				'section' => $this->customize_section,
				'type'    => 'nueve4_toggle_control',
			)
		);

		$priority = 10;

		foreach ( $this->templates as $slug => $label ) {
			$wp_customize->add_setting(
				'nueve4_fse_heading_' . $slug,
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				new \Nueve4\Customizer\Controls\React\Heading(
					$wp_customize,
					'nueve4_fse_heading_' . $slug,
					array(
						'section'         => $this->customize_section,
						'active_callback' => [ $this, 'is_enabled' ],
						'priority'        => $priority,
						'label'           => $label,
					)
				)
			);

			$priority ++;

			$wp_customize->add_setting(
				$this->get_option_slug_for_template( $slug ),
				array(
					'default'           => false,
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				$this->get_option_slug_for_template( $slug ),
				array(
					'active_callback' => [ $this, 'is_enabled' ],
					'section'         => $this->customize_section,
					'priority'        => $priority,
					'label'           => __( 'Yes', 'nueve4' ),
					'type'            => 'nueve4_toggle_control',
				)
			);

			$priority ++;

			$wp_customize->add_setting(
				$this->get_option_slug_for_header( $slug ),
				array(
					'default'           => true,
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				$this->get_option_slug_for_header( $slug ),
				array(
					'active_callback' => function () use ( $slug ) {
						return $this->is_enabled() && $this->is_template_enabled( $slug );
					},
					'section'         => $this->customize_section,
					'priority'        => $priority,
					'label'           => __( 'Header', 'nueve4' ),
					'type'            => 'nueve4_toggle_control',
				)
			);

			$priority ++;

			$wp_customize->add_setting(
				$this->get_option_slug_for_footer( $slug ),
				array(
					'default'           => true,
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				$this->get_option_slug_for_footer( $slug ),
				array(
					'active_callback' => function () use ( $slug ) {
						return $this->is_enabled() && $this->is_template_enabled( $slug );
					},
					'section'         => $this->customize_section,
					'priority'        => $priority,
					'label'           => __( 'Footer', 'nueve4' ),
					'type'            => 'nueve4_toggle_control',
				)
			);

			$priority += 10;
		}
	}

	/**
	 * Checks if template condition is met.
	 *
	 * @return string|null
	 */
	public function get_template_slug() {
		foreach ( $this->get_template_conditions() as $slug => $condition ) {
			if ( ! $condition ) {
				continue;
			}

			// Page shouldn't pass through to front page.
			if ( $slug === 'page' && $this->is_front_page() ) {
				continue;
			}

			// We're on archive but not on index.
			if ( $slug === 'archive' && $this->is_blog() ) {
				continue;
			}

			return $slug;
		}

		return null;
	}

	/**
	 * Check if the FSE templates are enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return get_theme_mod( self::FSE_ENABLED_SLUG, false );
	}

	/**
	 *  Check if templates should be loaded.
	 *
	 * @return bool
	 */
	private function should_load( $admin = false ) {
		if ( ! $this->is_enabled() ) {
			return false;
		}

		if ( $admin ) {
			return true;
		}

		$status = array_map(
			function ( $template ) {
				return $this->is_template_enabled( $template ) && $this->get_template_slug() === $template;
			},
			array_keys( $this->templates )
		);

		if ( ! in_array( true, $status, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the option ID for a template.
	 *
	 * @param string $template the template slug.
	 *
	 * @return string
	 */
	private function get_option_slug_for_template( $template ) {
		return 'nueve4_fse_' . $template;
	}

	/**
	 * Get the option ID for a template header.
	 *
	 * @param string $template the template slug.
	 *
	 * @return string
	 */
	private function get_option_slug_for_header( $template ) {
		return 'nueve4_fse_header_' . $template;
	}

	/**
	 * Get the option ID for a template footer.
	 *
	 * @param string $template the template slug.
	 *
	 * @return string
	 */
	private function get_option_slug_for_footer( $template ) {
		return 'nueve4_fse_footer_' . $template;
	}

	/**
	 * Is specific template enabled.
	 *
	 * @param string $template the template slug.
	 *
	 * @return bool
	 */
	private function is_template_enabled( $template ) {
		return get_theme_mod( $this->get_option_slug_for_template( $template ), false );
	}

	/**
	 * Check if the current page is blog.
	 *
	 * @return bool
	 */
	private function is_blog() {
		return is_post_type_archive( 'post' ) && is_home() && ! is_front_page();
	}

	/**
	 * Check if current page is front page.
	 *
	 * @return bool
	 */
	private function is_front_page() {
		return 'page' == get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) === get_the_ID();
	}

	/**
	 * Checks if single page.
	 *
	 * @return bool
	 */
	private function is_single_page() {
		// Disable PHP page templates.
		$page_template = get_page_template_slug( get_the_ID() );
		if ( strpos( $page_template, '.php' ) !== false ) {
			return false;
		}

		return is_singular( 'page' ) && ! $this->is_front_page();
	}

	/**
	 * Customizer inline styles.
	 *
	 * @return void
	 */
	public function add_styles() {
		$css = '
			#sub-accordion-section-nueve4_fse .customize-control-nueve4_toggle_control,
			#sub-accordion-section-nueve4_fse .customize-control-nueve4_customizer_heading { margin: 0; }
			#sub-accordion-section-nueve4_fse .customize-control-nueve4_customizer_heading  {margin-top: 10px;}
			#sub-accordion-section-nueve4_fse [id*="nueve4_fse_header"] .nueve4-white-background-control,
			#sub-accordion-section-nueve4_fse [id*="nueve4_fse_footer"] .nueve4-white-background-control {padding-left: 30px;}

			#accordion-section-nueve4_fse h3:before {
				content: "BETA";
				background-color: #0065a6;
				display: inline-flex;
				margin-right: 5px;
				border-radius: 3px;
				color: #fff;
				font-size: 11px;
				font-weight: 500;
				padding: 0 7px;
				height: 100%;
				line-height: 1.6;
			}
			';

		wp_add_inline_style( Loader::CUSTOMIZER_STYLE_HANDLE, Dynamic_Css::minify_css( $css ) );

		$js = '
		wp.customize.bind("ready", function() {
			wp.customize.notifications.remove("site_editor_block_theme_notice");
		});
		';

		wp_add_inline_script( 'react-controls', $js );
	}
}
