<?php
/**
 * Customizer Initialization
 * Ensures premium customizer loads with existing functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load premium customizer integration first
require_once get_template_directory() . '/inc/customizer/premium-integration.php';

// Ensure existing customizer modules still work
add_action( 'after_setup_theme', function() {
	// Initialize existing loader for compatibility
	$loader = new \Nueve4\Customizer\Loader();
	$loader->init();
}, 5 );