/**
 * Customizer order control.
 *
 * @package Neve\Customizer\Controls
 */
( function ( $ ) {
	'use strict';
	wp.nueve4HeadingAccordion = {
		init: function () {
			this.handleToggle();
		},
		handleToggle: function () {
			$( '.customize-control-customizer-heading.accordion .nueve4-customizer-heading' ).on( 'click', function () {
				var accordion = $( this ).closest( '.accordion' );
				$( accordion ).toggleClass( 'expanded' );
				return false;
			} );
		},
	};

	$( document ).ready(
		function () {
			wp.nueve4HeadingAccordion.init();
		}
	);
} )( jQuery );
