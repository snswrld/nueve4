<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "wrapper" div and all content after.
 *
 * @package Neve
 * @since   1.0.0
 */

/**
 * Executes actions before main tag is closed.
 *
 * @since 1.0.4
 */
do_action( 'nueve4_before_primary_end' ); ?>

</main><!--/.nueve4-main-->

<?php

/**
 * Executes actions after main tag is closed.
 *
 * @since 1.0.4
 */
do_action( 'nueve4_after_primary' );

/**
 * Filters the content parts.
 *
 * @since 1.0.9
 *
 * @param bool   $status Whether the component should be displayed or not.
 * @param string $context The context name.
 */
if ( apply_filters( 'nueve4_filter_toggle_content_parts', true, 'footer' ) === true ) {

	/**
	 * Executes actions before the footer was rendered.
	 *
	 * @since 1.0.0
	 */
	do_action( 'nueve4_before_footer_hook' );

	/**
	 * Executes the rendering function for the footer.
	 *
	 * @since 1.0.0
	 */
	do_action( 'nueve4_do_footer' );

	/**
	 * Executes actions after the footer was rendered.
	 *
	 * @since 1.0.0
	 */
	do_action( 'nueve4_after_footer_hook' );
}
?>

</div><!--/.wrapper-->
<?php

wp_footer();

/**
 * Executes actions before the body tag is closed.
 *
 * @since 2.11
 */
do_action( 'nueve4_body_end_before' );

?>
</body>

</html>
