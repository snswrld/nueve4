/**
 * Customizer button control functionality
 *
 * @package Nueve4\Customizer\Controls
 */
( function($) {
	'use strict';
	wp.nueve4CustomizeButton = {
		init: function() {
			$( '#customize-theme-controls' ).on(
				'click', '.menu-shortcut', function(e) {
					wp.customize.section( 'menu_locations' ).focus();
					e.preventDefault();
				}
			);
			$( '#customize-theme-controls' ).on(
				'click', '.nueve4-control-focus', function(e) {
					wp.customize.control( $( this ).data( 'control-to-focus' ) ).focus();
					e.preventDefault();
				}
			);
		}
	};

	$( document ).ready(
		function() {
			wp.nueve4CustomizeButton.init();
		}
	);
} )( jQuery );