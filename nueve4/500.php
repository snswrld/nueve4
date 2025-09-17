<?php
/**
 * The template for 500 page in PWA.
 *
 * @package Nueve4
 * @since   2.4.3
 */
pwa_get_header( 'pwa' );

do_action( 'nueve4_do_server_error' );

pwa_get_footer( 'pwa' );
