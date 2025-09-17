<?php
/**
 * Author:          Uriahs Victor
 * Created on:      05/08/2021 (d/m/y)
 *
 * @package Nueve4
 */

if ( defined( 'NEVE_PRO_PATH' ) && get_option( 'nv_pro_easy_digital_downloads_status' ) ) {
	do_action( 'nueve4_do_single_download', 'single-download' );
} else {
	do_action( 'nueve4_do_single_post', 'single-post' );
}
