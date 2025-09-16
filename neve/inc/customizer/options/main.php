<?php
/**
 * Handles main customzier setup like root panels.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      20/08/2018
 *
 * @package Neve\Customizer\Options
 */

namespace Neve\Customizer\Options;

use Neve\Core\Settings\Mods;
use Neve\Customizer\Controls\React\Documentation_Section;
use Neve\Customizer\Controls\React\Instructions_Section;
use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Controls\Simple_Upsell;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Panel;
use Neve\Customizer\Types\Section;

/**
 * Main customizer handler.
 */
class Main extends Base_Customizer {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->register_types();
		$this->add_main_panels();
		$this->change_controls();
	}

	/**
	 * Register customizer controls type.
	 */
	private function register_types() {
		$this->register_type( 'Neve\Customizer\Controls\Radio_Image', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Range', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Responsive_Number', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Tabs', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Heading', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Checkbox', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Upsell_Control', 'control' );
		$this->register_type( 'Neve\Customizer\Controls\Upsells\Scroll_To_Top_Control', 'control' );
	}

	/**
	 * Add main panels.
	 */
	private function add_main_panels() {
		$panels = array(
			'nueve4_layout'     => array(
				'priority' => 25,
				'title'    => __( 'Layout', 'nueve4' ),
			),
			'nueve4_typography' => array(
				'priority' => 35,
				'title'    => __( 'Typography', 'nueve4' ),
			),
		);

		foreach ( $panels as $panel_id => $panel ) {
			$this->add_panel(
				new Panel(
					$panel_id,
					array(
						'priority' => $panel['priority'],
						'title'    => $panel['title'],
					)
				)
			);
		}
		$this->wpc->add_section(
			new Instructions_Section(
				$this->wpc,
				'nueve4_typography_quick_links',
				array(
					'priority' => - 100,
					'panel'    => 'nueve4_typography',
					'type'     => 'hfg_instructions',
					'options'  => array(
						'quickLinks' => array(
							'nueve4_body_font_family'     => array(
								'label' => esc_html__( 'Change main font', 'nueve4' ),
								'icon'  => 'dashicons-editor-spellcheck',
							),
							'nueve4_headings_font_family' => array(
								'label' => esc_html__( 'Change headings font', 'nueve4' ),
								'icon'  => 'dashicons-heading',
							),
							'nueve4_h1_accordion_wrap'    => array(
								'label' => esc_html__( 'Change H1 font size', 'nueve4' ),
								'icon'  => 'dashicons-info-outline',
							),
							'nueve4_archive_typography_post_title' => array(
								'label' => esc_html__( 'Change archive font size', 'nueve4' ),
								'icon'  => 'dashicons-sticky',
							),
						),
					),
				)
			)
		);

		$this->wpc->add_section(
			new Documentation_Section(
				$this->wpc,
				'nueve4_documentation',
				[
					'priority' => PHP_INT_MAX,
					'title'    => esc_html__( 'Neve', 'nueve4' ),
					'url'      => tsdk_utmify( 'https://docs.themeisle.com/article/946-nueve4-doc', 'docsbtn' ),
				]
			)
		);
	}

	/**
	 * Change controls
	 */
	protected function change_controls() {
		$this->change_customizer_object( 'section', 'static_front_page', 'panel', 'nueve4_layout' );
		// Change default for shop columns WooCommerce option.
		$this->change_customizer_object( 'setting', 'woocommerce_catalog_columns', 'default', 3 );
	}
}
