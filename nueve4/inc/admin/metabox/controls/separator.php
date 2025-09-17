<?php
/**
 * Metabox separator.
 *
 * @package Nueve4\Admin\Metabox\Controls
 */

namespace Nueve4\Admin\Metabox\Controls;

/**
 * Class Separator
 *
 * @package Nueve4\Admin\Metabox\Controls
 */
class Separator extends Control_Base {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'separator';

	/**
	 * Render control.
	 *
	 * @return void
	 */
	public function render_content( $post_id ) {
		echo '<hr/>';
	}
}
