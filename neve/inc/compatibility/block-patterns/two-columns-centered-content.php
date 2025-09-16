<?php
/**
 * Two columns with centered content pattern.
 *
 * @package Neve
 */

return array(
	'title'      => __( 'Two columns with centered content', 'nueve4' ),
	'content'    => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {} -->
<figure class="wp-block-image"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/patterns/nueve4-patterns-16.jpg" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:heading {"align":"center","level":3} -->
<h3 class="has-text-align-center">Heading three</h3>
<!-- /wp:heading -->

<!-- wp:separator {"color":"nueve4-button-color","className":"is-style-default"} -->
<hr class="wp-block-separator has-text-color has-background has-nueve4-button-color-background-color has-nueve4-button-color-color is-style-default"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. </p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {} -->
<figure class="wp-block-image"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/patterns/nueve4-patterns-15.jpg" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:heading {"align":"center","level":3} -->
<h3 class="has-text-align-center">Heading three</h3>
<!-- /wp:heading -->

<!-- wp:separator {"color":"nueve4-button-color","className":"is-style-default"} -->
<hr class="wp-block-separator has-text-color has-background has-nueve4-button-color-background-color has-nueve4-button-color-color is-style-default"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
	'categories' => array( 'columns' ),
);
