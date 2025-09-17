<?php
/**
 * Single page layout section.
 *
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use Nueve4\Customizer\Types\Control;

/**
 * Class Layout_Single_Page
 */
class Layout_Single_Page extends Base_Layout_Single {

	/**
	 * Returns the post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return 'page';
	}

	/**
	 * @return string
	 */
	public function get_cover_selector() {
		return '.page .nv-post-cover';
	}

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		parent::add_controls();
		$this->add_control(
			new Control(
				'nueve4_page_hide_title',
				[
					'sanitize_callback' => 'nueve4_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'    => esc_html__( 'Disable Title', 'nueve4' ),
					'section'  => $this->section,
					'type'     => 'nueve4_toggle_control',
					'priority' => 25,
				],
				'Nueve4\Customizer\Controls\Checkbox'
			)
		);
	}

	/**
	 * Fuction used for active_callback control property.
	 *
	 * @return bool
	 */
	public static function is_cover_layout() {
		return get_theme_mod( 'nueve4_page_header_layout' ) === 'cover';
	}
}
