<?php
/**
 * Theme Bootstrap Entry Point
 *
 * @package Nueve4
 */

/**
 * Initialize theme using Bootstrap class
 */
function nueve4_run() {
	require_once get_template_directory() . '/inc/core/bootstrap.php';
	$bootstrap = new \Nueve4\Core\Bootstrap();
	$bootstrap->init();
}

nueve4_run();
