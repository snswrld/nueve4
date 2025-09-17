<?php
/**
 * Author:          Uriahs Victor
 * Created on:      14/07/2021 (d/m/y)
 *
 * @package Nueve4
 */

if ( defined( 'NEVE_PRO_PATH' ) && get_option( 'nv_pro_easy_digital_downloads_status' ) ) {
	do_action( 'nueve4_do_download_archive', 'archive-download' );
} else {
	do_action( 'nueve4_blog_post_template_part_content', 'single' );
}
