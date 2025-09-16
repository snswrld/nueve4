/**
 * Handle the layout preview.
 */
(function ( $ ) {
	$.nueve4LayoutPreview = {

		/**
		 * Initialize the layout preview functions.
		 */
		init: function () {
			this.contentWidthsPreview();
			this.containersLivePreview();
		},

		/**
		 * Define content width controls and correspondent properties.
		 */
		contentWidths: {
			'nueve4_sitewide_content_width': {
				content: '.nueve4-main > .container .col, .nueve4-main > .container-fluid .col',
				sidebar: '.nv-sidebar-wrap'
			},
			'nueve4_blog_archive_content_width': {
				content: '.archive-container .nv-index-posts',
				sidebar: '.archive-container .nv-sidebar-wrap'
			},
			'nueve4_single_post_content_width': {
				content: '.single-post-container .nv-single-post-wrap',
				sidebar: '.single-post-container .nv-sidebar-wrap'
			},
			'nueve4_shop_archive_content_width': {
				content: '.archive.woocommerce .shop-container .nv-shop.col',
				sidebar: '.archive.woocommerce .shop-container .nv-sidebar-wrap'
			},
			'nueve4_single_product_content_width': {
				content: '.single-product .shop-container .nv-shop.col',
				sidebar: '.single-product .shop-container .nv-sidebar-wrap'
			},
			'nueve4_other_pages_content_width': {
				content: 'body:not(.single):not(.archive):not(.blog):not(.search) .nueve4-main > .container .col',
				sidebar: 'body:not(.single):not(.archive):not(.blog):not(.search) .nv-sidebar-wrap'
			},
		},

		/**
		 * Run the content width previews.
		 */
		contentWidthsPreview: function () {
			$.each( this.contentWidths, function ( id, args ) {
				wp.customize( id, function ( value ) {
					value.bind( function ( newval ) {
						jQuery( args.content ).css( 'max-width', newval + '%' );
						jQuery( args.sidebar ).css( 'max-width', 100 - newval + '%' );
					} );
				} );
			} );
		},

		/**
		 * Define container theme mods and correspondent selectors.
		 */
		containersLayoutMap: {
			'nueve4_default_container_style': '.page:not(.woocommerce) .single-page-container',
			'nueve4_blog_archive_container_style': '.archive-container',
			'nueve4_single_post_container_style': '.single-post-container',
			'nueve4_shop_archive_container_style': '.woocommerce-page.post-type-archive .nueve4-main > div',
			'nueve4_single_product_container_style': '.single-product .nueve4-main > div',
		},

		/**
		 * Run the containers live preview.
		 */
		containersLivePreview: function () {
			'use strict';
			$.each( this.containersLayoutMap, function ( controlId, cssSelector ) {
				cssSelector += ':not(.set-in-metabox)';
				wp.customize( controlId, function ( value ) {
					value.bind( function ( newval ) {
						if ( newval === 'contained' ) {
							$( cssSelector ).removeClass( 'container-fluid' ).addClass( 'container' );
							return false;
						}
						$( cssSelector ).removeClass( 'container' ).addClass( 'container-fluid' );
					} );
				} );
			} );
		},
	};
})( jQuery );

jQuery.nueve4LayoutPreview.init();


