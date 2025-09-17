<?php
/**
 * Three columns with images and text pattern.
 *
 * @package Nueve4
 */

return array(
	'title'      => __( 'Three columns with images and text', 'nueve4' ),
	'content'    => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {} -->
<figure class="wp-block-image"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/patterns/nueve4-patterns-12.jpg" alt="" /></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {} -->
<figure class="wp-block-image "><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/patterns/nueve4-patterns-17.jpg" alt="" /></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3>The quick brown fox jumps over the lazy dog"</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
	'categories' => array( 'columns' ),
);
