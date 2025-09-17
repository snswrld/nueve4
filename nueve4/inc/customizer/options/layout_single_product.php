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

use Nueve4\Customizer\Base_Customizer;
use Nueve4\Customizer\Types\Control;
use Nueve4\Customizer\Types\Section;

/**
 * Class Layout_Single_Product
 *
 * @package Nueve4\Customizer\Options
 */
class Layout_Single_Product extends Base_Customizer {

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		if ( ! $this->should_load() ) {
			return;
		}

		$this->section_single_product();
		$this->exclusive_products_controls();
	}

	/**
	 * Check if the controls for Single Product should load.
	 */
	private function should_load() {
		return class_exists( 'WooCommerce', false );
	}

	/**
	 * Add single product layout section.
	 */
	private function section_single_product() {
		$this->add_section(
			new Section(
				'nueve4_single_product_layout',
				array(
					'priority' => 65,
					'title'    => esc_html__( 'Single Product', 'nueve4' ),
					'panel'    => 'woocommerce',
				)
			)
		);
	}

	/**
	 * Add customizer controls for exclusive products section.
	 */
	private function exclusive_products_controls() {
		$this->add_control(
			new Control(
				'nueve4_exclusive_products_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Exclusive Products', 'nueve4' ),
					'section'          => 'nueve4_single_product_layout',
					'priority'         => 200,
					'class'            => 'exclusive-products-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 2,
					'expanded'         => true,
				),
				'Nueve4\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'nueve4_exclusive_products_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'priority' => 210,
					'section'  => 'nueve4_single_product_layout',
					'label'    => esc_html__( 'Title', 'nueve4' ),
					'type'     => 'text',
				)
			)
		);

		$product_cat = $this->get_shop_categories();
		$this->add_control(
			new Control(
				'nueve4_exclusive_products_category',
				array(
					'default'           => '-',
					'sanitize_callback' => array( $this, 'sanitize_categories' ),
				),
				array(
					'label'    => esc_html__( 'Category', 'nueve4' ),
					'section'  => 'nueve4_single_product_layout',
					'priority' => 220,
					'type'     => 'select',
					'choices'  => $product_cat,
				)
			)
		);
	}

	/**
	 * Get shop categories.
	 *
	 * @return array
	 */
	private function get_shop_categories() {
		$categories         = array(
			'-'   => esc_html__( 'None', 'nueve4' ),
			'all' => esc_html__( 'All', 'nueve4' ),
		);
		$product_categories = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => 0,
				'title_li'   => '',
			)
		);
		if ( empty( $product_categories ) ) {
			return $categories;
		}

		foreach ( $product_categories as $category ) {
			$term_id  = $category->term_id;
			$cat_name = $category->name;
			if ( empty( $term_id ) || empty( $cat_name ) ) {
				continue;
			}

			$categories[ $term_id ] = $cat_name;
		}

		return $categories;
	}

	/**
	 * Sanitize categories value.
	 *
	 * @param string $value Value from the control.
	 *
	 * @return string
	 */
	public function sanitize_categories( $value ) {
		$categories = $this->get_shop_categories();
		if ( ! array_key_exists( $value, $categories ) ) {
			return '-';
		}

		return $value;
	}


}
