<?php
/**
 * Container layout section.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      20/08/2018
 *
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use Nueve4\Customizer\Base_Customizer;
use Nueve4\Customizer\Types\Control;
use Nueve4\Customizer\Types\Section;
use Nueve4\Customizer\Defaults\Layout;
use Nueve4\Core\Settings\Config;

/**
 * Class Layout_Container
 *
 * @package Nueve4\Customizer\Options
 */
class Layout_Container extends Base_Customizer {
	use Layout;

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->section_container();
		$this->control_container_width();
		$this->control_vertical_spacing();
		$this->control_container_style();
	}

	/**
	 * Add customize section
	 */
	private function section_container() {
		$this->add_section(
			new Section(
				'nueve4_container',
				array(
					'priority' => 25,
					'title'    => esc_html__( 'Container', 'nueve4' ),
					'panel'    => 'nueve4_layout',
				)
			)
		);
	}

	/**
	 * Add container width control
	 */
	private function control_container_width() {
		$this->add_control(
			new Control(
				'nueve4_container_width',
				[
					'sanitize_callback' => 'nueve4_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 748, "tablet": 992, "desktop": 1170 }',
				],
				[
					'label'                 => esc_html__( 'Container width', 'nueve4' ),
					'section'               => 'nueve4_container',
					'type'                  => 'nueve4_responsive_range_control',
					'input_attrs'           => [
						'min'        => 200,
						'max'        => 2000,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 748,
							'tablet'  => 992,
							'desktop' => 1170,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'selector'   => 'body',
							'vars'       => '--container',
							'responsive' => true,
							'suffix'     => 'px',
						],
					],
					'priority'              => 25,
				],
				'\Nueve4\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Add vertical spacing control
	 */
	private function control_vertical_spacing() {
		$this->add_control(
			new Control(
				Config::MODS_CONTENT_VSPACING,
				[
					'default'   => $this->content_vspacing_default(),
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Content Vertical Spacing', 'nueve4' ),
					'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
					'section'               => 'nueve4_container',
					'input_attrs'           => [
						'units' => [ 'px', 'vh' ],
						'axis'  => 'vertical',
					],
					'default'               => $this->content_vspacing_default(),
					'priority'              => 26,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--c-vspace',
							'selector'   => 'body.single:not(.single-product), body.page',
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
	 * Add container style controls
	 */
	private function control_container_style() {
		$container_style_controls = array(
			'nueve4_default_container_style'      => array(
				'priority' => 30,
				'label'    => __( 'Default Container Style', 'nueve4' ),
			),
			'nueve4_blog_archive_container_style' => array(
				'priority' => 35,
				'label'    => __( 'Blog / Archive Container Style', 'nueve4' ),
			),
			'nueve4_single_post_container_style'  => array(
				'priority' => 40,
				'label'    => __( 'Single Post Container Style', 'nueve4' ),
			),
		);

		if ( class_exists( 'WooCommerce', false ) ) {
			$container_style_controls = array_merge(
				$container_style_controls,
				array(
					'nueve4_shop_archive_container_style'   => array(
						'priority' => 45,
						'label'    => __( 'Shop / Archive Container Style', 'nueve4' ),
					),
					'nueve4_single_product_container_style' => array(
						'priority' => 50,
						'label'    => __( 'Single Product Container Style', 'nueve4' ),
					),
				)
			);
		}

		/**
		 * Filters the container style controls.
		 *
		 * @param array $container_style_controls Container style controls.
		 *
		 * @since 3.1.0
		 */
		$container_style_controls = apply_filters( 'nueve4_container_style_filter', $container_style_controls );

		foreach ( $container_style_controls as $control_id => $control ) {
			$this->add_control(
				new Control(
					$control_id,
					array(
						'sanitize_callback' => 'nueve4_sanitize_container_layout',
						'transport'         => $this->selective_refresh,
						'default'           => 'contained',
					),
					array(
						'label'    => $control['label'],
						'section'  => 'nueve4_container',
						'type'     => 'select',
						'priority' => $control['priority'],
						'choices'  => array(
							'contained'  => __( 'Contained', 'nueve4' ),
							'full-width' => __( 'Full Width', 'nueve4' ),
						),
					)
				)
			);
		}
	}
}
