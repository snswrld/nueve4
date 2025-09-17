<?php
/**
 * Customizer panel type enforcement
 *
 * @package Nueve4\Customizer\Types
 */

namespace Nueve4\Customizer\Types;

/**
 * Class Panel
 *
 * @package Nueve4\Customizer\Types
 */
class Panel {
	/**
	 * ID of panel
	 *
	 * @var string the control ID.
	 */
	public $id;

	/**
	 * Args for panel instance.
	 *
	 * @var array args passed into panel instance.
	 */
	public $args = array();

	/**
	 * Constructor.
	 *
	 * @param string $id   the control id.
	 * @param array  $args the panel args.
	 */
	public function __construct( $id, $args ) {
		$this->id   = $id;
		$this->args = $args;
	}
}
