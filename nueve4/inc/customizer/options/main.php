<?php
/**
 * Handles main customzier setup like root panels.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      20/08/2018
 *
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use Nueve4\Core\Settings\Mods;
use Nueve4\Customizer\Controls\React\Documentation_Section;
use Nueve4\Customizer\Controls\React\Instructions_Section;
use Nueve4\Customizer\Base_Customizer;
use Nueve4\Customizer\Controls\Simple_Upsell;
use Nueve4\Customizer\Types\Control;
use Nueve4\Customizer\Types\Panel;
use Nueve4\Customizer\Types\Section;

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
		\Nueve4\Customizer\Control_Registrar::register_controls( $this );
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
					'title'    => esc_html__( 'Nueve4', 'nueve4' ),
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
