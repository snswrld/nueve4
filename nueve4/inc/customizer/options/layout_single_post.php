<?php
/**
 * Single post layout section.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      20/08/2018
 *
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use HFG\Traits\Core;
use Nueve4\Core\Settings\Config;
use Nueve4\Customizer\Defaults\Layout;
use Nueve4\Customizer\Types\Control;
use Nueve4\Customizer\Defaults\Single_Post;

/**
 * Class Layout_Single_Post
 *
 * @package Nueve4\Customizer\Options
 */
class Layout_Single_Post extends Base_Layout_Single {
	use Core;
	use Layout;
	use Single_Post;

	/**
	 * Returns the post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return 'post';
	}

	/**
	 * @return string
	 */
	public function get_cover_selector() {
		return '.single .nv-post-cover';
	}

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		parent::add_controls();
		$this->control_content_order();
		$this->content_vspacing();
		$this->add_subsections();
		$this->header_layout();
		$this->post_meta();
		$this->comments();
		add_action( 'customize_register', [ $this, 'adjust_headings' ], PHP_INT_MAX );
	}

	/**
	 * Add sections headings.
	 */
	private function add_subsections() {

		$headings = [
			'page_elements'    => [
				'title'            => esc_html__( 'Page Elements', 'nueve4' ),
				'priority'         => 95,
				'controls_to_wrap' => 2,
				'expanded'         => false,
			],
			'page_settings'    => [
				'title'            => esc_html__( 'Page', 'nueve4' ) . ' ' . esc_html__( 'Settings', 'nueve4' ),
				'priority'         => 106,
				'controls_to_wrap' => 2,
				'expanded'         => false,
			],
			'meta'             => [
				'title'            => esc_html__( 'Post Meta', 'nueve4' ),
				'priority'         => 110,
				'controls_to_wrap' => 5,
				'expanded'         => false,
			],
			'comments_section' => [
				'title'           => esc_html__( 'Comments Section', 'nueve4' ),
				'priority'        => 150,
				'expanded'        => true,
				'accordion'       => false,
				'active_callback' => function() {
					return $this->element_is_enabled( 'comments' );
				},
			],
			'comments_form'    => [
				'title'           => esc_html__( 'Submit Form Section', 'nueve4' ),
				'priority'        => 175,
				'expanded'        => true,
				'accordion'       => false,
				'active_callback' => function() {
					return $this->element_is_enabled( 'comments' );
				},
			],
		];

		foreach ( $headings as $heading_id => $heading_data ) {
			$this->add_control(
				new Control(
					'nueve4_post_' . $heading_id . '_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => $heading_data['title'],
						'section'          => $this->section,
						'priority'         => $heading_data['priority'],
						'class'            => $heading_id . '-accordion',
						'expanded'         => $heading_data['expanded'],
						'accordion'        => array_key_exists( 'accordion', $heading_data ) ? $heading_data['accordion'] : true,
						'controls_to_wrap' => array_key_exists( 'controls_to_wrap', $heading_data ) ? $heading_data['controls_to_wrap'] : 0,
						'active_callback'  => array_key_exists( 'active_callback', $heading_data ) ? $heading_data['active_callback'] : '__return_true',
					],
					'Nueve4\Customizer\Controls\Heading'
				)
			);
		}
	}

	/**
	 * Add header layout controls.
	 */
	private function header_layout() {
		$this->add_control(
			new Control(
				'nueve4_post_cover_meta_before_title',
				[
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Display meta before title', 'nueve4' ),
					'section'         => $this->section,
					'type'            => 'nueve4_toggle_control',
					'priority'        => 40,
					'active_callback' => [ $this, 'is_cover_layout' ],
				],
				'Nueve4\Customizer\Controls\Checkbox'
			)
		);
	}

	/**
	 * Add content order control.
	 */
	private function control_content_order() {

		$all_components = [
			'title-meta'      => __( 'Title & Meta', 'nueve4' ),
			'thumbnail'       => __( 'Thumbnail', 'nueve4' ),
			'content'         => __( 'Content', 'nueve4' ),
			'tags'            => __( 'Tags', 'nueve4' ),
			'post-navigation' => __( 'Post navigation', 'nueve4' ),
			'comments'        => __( 'Comments', 'nueve4' ),
		];

		if ( self::is_cover_layout() ) {
			$all_components = [
				'content'         => __( 'Content', 'nueve4' ),
				'tags'            => __( 'Tags', 'nueve4' ),
				'post-navigation' => __( 'Post navigation', 'nueve4' ),
				'comments'        => __( 'Comments', 'nueve4' ),
			];
		}

		$order_default_components = $this->post_ordering();

		/**
		 * Filters the elements on the single post page.
		 *
		 * @param array $all_components Single post page components.
		 *
		 * @since 2.11.4
		 */
		$components = apply_filters( 'nueve4_single_post_elements', $all_components );

		$this->add_control(
			new Control(
				'nueve4_layout_single_post_elements_order',
				[
					'sanitize_callback' => [ $this, 'sanitize_post_elements_ordering' ],
					'default'           => wp_json_encode( $order_default_components ),
				],
				[
					'label'      => esc_html__( 'Elements Order', 'nueve4' ),
					'section'    => $this->section,
					'components' => $components,
					'priority'   => 100,
				],
				'Nueve4\Customizer\Controls\React\Ordering'
			)
		);

		$this->add_control(
			new Control(
				'nueve4_single_post_elements_spacing',
				[
					'sanitize_callback' => 'nueve4_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":60,"tablet":60,"mobile":60}',
				],
				[
					'label'                 => esc_html__( 'Spacing between elements', 'nueve4' ),
					'section'               => $this->section,
					'type'                  => 'nueve4_responsive_range_control',
					'input_attrs'           => [
						'max'        => 500,
						'units'      => [ 'px', 'em', 'rem' ],
						'defaultVal' => [
							'mobile'  => 60,
							'tablet'  => 60,
							'desktop' => 60,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 105,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--spacing',
							'selector'   => '.nv-single-post-wrap',
							'suffix'     => 'px',
						],
					],
				],
				'\Nueve4\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Add content spacing control.
	 */
	private function content_vspacing() {
		$this->add_control(
			new Control(
				Config::MODS_SINGLE_POST_VSPACING_INHERIT,
				[
					'sanitize_callback' => 'nueve4_sanitize_vspace_type',
					'default'           => 'inherit',
				],
				[
					'label'              => esc_html__( 'Content Vertical Spacing', 'nueve4' ),
					'section'            => $this->section,
					'priority'           => 107,
					'choices'            => [
						'inherit'  => [
							'tooltip' => esc_html__( 'Inherit', 'nueve4' ),
							'icon'    => 'text',
						],
						'specific' => [
							'tooltip' => esc_html__( 'Custom', 'nueve4' ),
							'icon'    => 'text',
						],
					],
					'footer_description' => [
						'inherit' => [
							'template'         => esc_html__( 'Customize the default vertical spacing <ctaButton>here</ctaButton>.', 'nueve4' ),
							'control_to_focus' => Config::MODS_CONTENT_VSPACING,
						],
					],
				],
				'\Nueve4\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$default_value = get_theme_mod( Config::MODS_CONTENT_VSPACING, $this->content_vspacing_default() );
		$this->add_control(
			new Control(
				Config::MODS_SINGLE_POST_CONTENT_VSPACING,
				[
					'default'   => $default_value,
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Custom Value', 'nueve4' ),
					'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
					'section'               => $this->section,
					'input_attrs'           => [
						'units'     => [ 'px', 'vh' ],
						'axis'      => 'vertical',
						'dependsOn' => [ Config::MODS_SINGLE_POST_VSPACING_INHERIT => 'specific' ],
					],
					'default'               => $default_value,
					'priority'              => 107,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--c-vspace',
							'selector'   => 'body.single:not(.single-product) .nueve4-main',
							'responsive' => true,
							'fallback'   => '',
						],
						'directional' => true,
					],
				],
				'\Nueve4\Customizer\Controls\React\Spacing'
			)
		);
	}

	/**
	 * Add post meta controls.
	 */
	private function post_meta() {

		$components = apply_filters(
			'nueve4_meta_filter',
			[
				'author'   => __( 'Author', 'nueve4' ),
				'category' => __( 'Category', 'nueve4' ),
				'date'     => __( 'Date', 'nueve4' ),
				'comments' => __( 'Comments', 'nueve4' ),
			]
		);

		$default_value = get_theme_mod( 'nueve4_single_post_meta_fields', self::get_default_single_post_meta_fields() );

		$this->add_control(
			new Control(
				'nueve4_single_post_meta_fields',
				[
					'sanitize_callback' => 'nueve4_sanitize_meta_repeater',
					'default'           => $default_value,
				],
				[
					'label'            => esc_html__( 'Meta Order', 'nueve4' ),
					'section'          => $this->section,
					'fields'           => [
						'hide_on_mobile' => [
							'type'  => 'checkbox',
							'label' => __( 'Hide on mobile', 'nueve4' ),
						],
					],
					'components'       => $components,
					'allow_new_fields' => 'no',
					'priority'         => 115,
				],
				'\Nueve4\Customizer\Controls\React\Repeater'
			)
		);

		$default_separator = get_theme_mod( 'nueve4_metadata_separator', esc_html( '/' ) );
		$this->add_control(
			new Control(
				'nueve4_single_post_metadata_separator',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => $default_separator,
				],
				[
					'priority'    => 120,
					'section'     => $this->section,
					'label'       => esc_html__( 'Separator', 'nueve4' ),
					'description' => esc_html__( 'For special characters make sure to use Unicode. For example > can be displayed using \003E.', 'nueve4' ),
					'type'        => 'text',
				]
			)
		);

		$author_avatar_default = get_theme_mod( 'nueve4_author_avatar', false );
		$this->add_control(
			new Control(
				'nueve4_single_post_author_avatar',
				[
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
					'default'           => $author_avatar_default,
				],
				[
					'label'    => esc_html__( 'Show Author Avatar', 'nueve4' ),
					'section'  => $this->section,
					'type'     => 'nueve4_toggle_control',
					'priority' => 125,
				]
			)
		);

		$avatar_size_default = get_theme_mod( 'nueve4_author_avatar_size', '{ "mobile": 20, "tablet": 20, "desktop": 20 }' );
		$this->add_control(
			new Control(
				'nueve4_single_post_avatar_size',
				[
					'sanitize_callback' => 'nueve4_sanitize_range_value',
					'default'           => $avatar_size_default,
				],
				[
					'label'           => esc_html__( 'Avatar Size', 'nueve4' ),
					'section'         => $this->section,
					'units'           => [ 'px' ],
					'input_attr'      => [
						'mobile'  => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
						'tablet'  => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
						'desktop' => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
					],
					'input_attrs'     => [
						'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
						'max'        => 50,
						'defaultVal' => [
							'mobile'  => 20,
							'tablet'  => 20,
							'desktop' => 20,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px', 'em', 'rem' ],
					],
					'priority'        => 130,
					'active_callback' => function () {
						return get_theme_mod( 'nueve4_single_post_author_avatar', false );
					},
					'responsive'      => true,
				],
				'Nueve4\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'nueve4_single_post_show_last_updated_date',
				[
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
					'default'           => get_theme_mod( 'nueve4_show_last_updated_date', false ),
				],
				[
					'label'    => esc_html__( 'Use last updated date instead of the published one', 'nueve4' ),
					'section'  => $this->section,
					'type'     => 'nueve4_toggle_control',
					'priority' => 135,
				]
			)
		);
	}

	/**
	 * Add comments controls.
	 */
	private function comments() {

		$this->add_control(
			new Control(
				'nueve4_post_comment_section_title',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Section title', 'nueve4' ),
					'description'     => esc_html__( 'The following magic tags are available for this field: {title} and {comments_number}. Leave this field empty for default behavior.', 'nueve4' ),
					'priority'        => 155,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function() {
						return $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		$this->add_boxed_layout_controls(
			'comments',
			[
				'priority'                  => 160,
				'section'                   => $this->section,
				'padding_default'           => $this->padding_default(),
				'background_default'        => 'var(--nv-light-bg)',
				'color_default'             => 'var(--nv-text-color)',
				'boxed_selector'            => '.nv-is-boxed.nv-comments-wrap',
				'text_color_css_selector'   => '.nv-comments-wrap.nv-is-boxed, .nv-comments-wrap.nv-is-boxed a',
				'border_color_css_selector' => '.nv-comments-wrap.nv-is-boxed .nv-comment-article',
				'toggle_active_callback'    => function() {
					return $this->element_is_enabled( 'comments' );
				},
				'active_callback'           => function() {
					return $this->element_is_enabled( 'comments' ) && get_theme_mod( 'nueve4_comments_boxed_layout', false );
				},
			]
		);

		$this->add_control(
			new Control(
				'nueve4_post_comment_form_title',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Section title', 'nueve4' ),
					'priority'        => 180,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function() {
						return $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'nueve4_post_comment_form_button_style',
				[
					'default'           => 'primary',
					'sanitize_callback' => 'nueve4_sanitize_button_type',
				],
				[
					'label'           => esc_html__( 'Button style', 'nueve4' ),
					'section'         => $this->section,
					'priority'        => 185,
					'type'            => 'select',
					'choices'         => [
						'primary'   => esc_html__( 'Primary', 'nueve4' ),
						'secondary' => esc_html__( 'Secondary', 'nueve4' ),
					],
					'active_callback' => function() {
						return $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'nueve4_post_comment_form_button_text',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Button text', 'nueve4' ),
					'priority'        => 190,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function() {
						return $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		$this->add_boxed_layout_controls(
			'comments_form',
			[
				'priority'                => 195,
				'section'                 => $this->section,
				'padding_default'         => $this->padding_default(),
				'is_boxed_default'        => true,
				'background_default'      => 'var(--nv-light-bg)',
				'color_default'           => 'var(--nv-text-color)',
				'boxed_selector'          => '.nv-is-boxed.comment-respond',
				'text_color_css_selector' => '.comment-respond.nv-is-boxed, .comment-respond.nv-is-boxed a',
				'toggle_active_callback'  => function() {
					return $this->element_is_enabled( 'comments' );
				},
				'active_callback'         => function() {
					return $this->element_is_enabled( 'comments' ) && get_theme_mod( 'nueve4_comments_form_boxed_layout', true );
				},
			]
		);
	}

	/**
	 * Change heading controls properties.
	 */
	public function adjust_headings() {
		$this->change_customizer_object( 'control', 'nueve4_comments_heading', 'controls_to_wrap', 15 );
	}

	/**
	 * Active callback for sharing controls.
	 *
	 * @param string $element Post page element.
	 *
	 * @return bool
	 */
	public function element_is_enabled( $element ) {
		$default_order = apply_filters(
			'nueve4_single_post_elements_default_order',
			array(
				'title-meta',
				'thumbnail',
				'content',
				'tags',
				'comments',
			)
		);

		$content_order = get_theme_mod( 'nueve4_layout_single_post_elements_order', wp_json_encode( $default_order ) );
		$content_order = json_decode( $content_order, true );
		if ( ! in_array( $element, $content_order, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanitize content order control.
	 */
	public function sanitize_post_elements_ordering( $value ) {
		$allowed = [
			'thumbnail',
			'title-meta',
			'content',
			'tags',
			'post-navigation',
			'comments',
			'author-biography',
			'related-posts',
			'sharing-icons',
		];

		if ( empty( $value ) ) {
			return wp_json_encode( $allowed );
		}

		$decoded = json_decode( $value, true );

		foreach ( $decoded as $val ) {
			if ( ! in_array( $val, $allowed, true ) ) {
				return wp_json_encode( $allowed );
			}
		}

		return $value;
	}

	/**
	 * Fuction used for active_callback control property.
	 *
	 * @return bool
	 */
	public static function is_cover_layout() {
		return get_theme_mod( 'nueve4_post_header_layout' ) === 'cover';
	}

	/**
	 *  Fuction used for active_callback control property for boxed title.
	 *
	 * @return bool
	 */
	public function is_boxed_title() {
		if ( ! self::is_cover_layout() ) {
			return false;
		}
		return get_theme_mod( 'nueve4_post_cover_title_boxed_layout', false );
	}
}
