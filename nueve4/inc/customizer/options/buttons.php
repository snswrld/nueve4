<?php
/**
 * Buttons section.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      20/08/2018
 *
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use Nueve4\Core\Settings\Config;
use Nueve4\Core\Settings\Mods;
use Nueve4\Customizer\Base_Customizer;
use Nueve4\Customizer\Types\Control;
use Nueve4\Customizer\Types\Section;

/**
 * Class Buttons
 *
 * @package Nueve4\Customizer\Options
 */
class Buttons extends Base_Customizer {

	/**
	 * Customizer section slug.
	 *
	 * @var string
	 */
	private $section_id = 'nueve4_buttons_section';

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->add_section(
			new Section(
				$this->section_id,
				array(
					'priority' => 40,
					'title'    => esc_html__( 'Buttons', 'nueve4' ),
				)
			)
		);

		$this->add_control(
			new Control(
				'nueve4_buttons_generic_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'General', 'nueve4' ),
					'section'          => $this->section_id,
					'class'            => 'buttons-general-accordion',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 2,
				],
				'Nueve4\Customizer\Controls\Heading'
			)
		);

		$mod_key  = Config::MODS_BUTTON_PRIMARY_PADDING;
		$defaults = Mods::get_alternative_mod_default( Config::MODS_BUTTON_PRIMARY_PADDING );
		$this->add_control(
			new Control(
				$mod_key,
				array(
					'default' => $defaults,
				),
				array(
					'label'             => __( 'Padding', 'nueve4' ),
					'sanitize_callback' => array( $this, 'sanitize_spacing_array' ),
					'section'           => $this->section_id,
					'input_attrs'       => [
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					],
					'default'           => $defaults,
				),
				'\Nueve4\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				Config::MODS_BUTTON_TYPEFACE,
				[
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Button Text', 'nueve4' ),
					'section'               => $this->section_id,
					'input_attrs'           => array(
						'size_units'             => [ 'px', 'em', 'rem' ],
						'weight_default'         => 700,
						'size_default'           => array(
							'suffix'  => array(
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							),
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'line_height_default'    => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'letter_spacing_default' => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
					),
					'type'                  => 'nueve4_typeface_control',
					'font_family_control'   => 'nueve4_body_font_family',
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--btnfs'            => [
									'key'        => 'fontSize',
									'responsive' => true,
									'suffix'     => 'px',
								],
								'--btnlineheight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--btnletterspacing' => [
									'key'        => 'letterSpacing',
									'responsive' => true,
									'suffix'     => 'px',
								],
								'--btntexttransform' => [
									'key' => 'textTransform',
								],
								'--btnfontweight'    => [
									'key' => 'fontWeight',
								],
							],
							'selector' => 'body',
						],
					],
					'refresh_on_reset'      => true,
				],
				'\Nueve4\Customizer\Controls\React\Typography'
			)
		);

		$buttons = [
			'button'           => __( 'Primary Button', 'nueve4' ),
			'secondary_button' => __( 'Secondary Button', 'nueve4' ),
		];

		foreach ( $buttons as $button => $heading_text ) {
			$this->add_control(
				new Control(
					'nueve4_' . $button . '_appearance_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => esc_html( $heading_text ),
						'section'          => $this->section_id,
						'class'            => 'buttons-' . $button . '-appearance-accordion',
						'accordion'        => true,
						'controls_to_wrap' => 1,
						'expanded'         => false,
					],
					'Nueve4\Customizer\Controls\Heading'
				)
			);

			$mod_key  = 'nueve4_' . $button . '_appearance';
			$defaults = nueve4_get_button_appearance_default( $button );

			$this->add_control(
				new Control(
					$mod_key,
					[
						'sanitize_callback' => 'nueve4_sanitize_button_appearance',
						'default'           => $defaults,
					],
					[
						'default_vals' => $defaults,
						'label'        => __( 'Button Appearance', 'nueve4' ),
						'section'      => $this->section_id,
					],
					'\Nueve4\Customizer\Controls\React\Button_Appearance'
				)
			);
		}
	}
}
