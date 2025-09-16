<?php
/**
 * The template for displaying comments.
 *
 * @package nueve4
 */
if ( post_password_required() ) {
	return;
}

?>

<div id="comments" class="<?php echo esc_attr( apply_filters( 'nueve4_comments_area_class', 'comments-area' ) ); ?>">
	<?php do_action( 'nueve4_do_comment_area' ); ?>
</div>
