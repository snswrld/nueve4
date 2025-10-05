/**
 * Customizer Live Preview
 */
(function($) {
    'use strict';

    // Primary Color
    wp.customize('nueve4_primary_color', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-primary-color').remove();
            $('head').append('<style id="nueve4-primary-color">:root { --nueve4-primary-color: ' + newval + '; } .btn-primary, .button-primary { background-color: ' + newval + '; }</style>');
        });
    });

    // Secondary Color
    wp.customize('nueve4_secondary_color', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-secondary-color').remove();
            $('head').append('<style id="nueve4-secondary-color">:root { --nueve4-secondary-color: ' + newval + '; } .btn-secondary { background-color: ' + newval + '; }</style>');
        });
    });

    // Text Color
    wp.customize('nueve4_text_color', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-text-color').remove();
            $('head').append('<style id="nueve4-text-color">body { color: ' + newval + '; }</style>');
        });
    });

    // Link Color
    wp.customize('nueve4_link_color', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-link-color').remove();
            $('head').append('<style id="nueve4-link-color">a { color: ' + newval + '; }</style>');
        });
    });

    // Body Font Family
    wp.customize('nueve4_body_font_family', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-body-font-family').remove();
            $('head').append('<style id="nueve4-body-font-family">body { font-family: ' + newval + '; }</style>');
        });
    });

    // Body Font Size
    wp.customize('nueve4_body_font_size', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-body-font-size').remove();
            $('head').append('<style id="nueve4-body-font-size">body { font-size: ' + newval + 'px; }</style>');
        });
    });

    // Body Line Height
    wp.customize('nueve4_body_line_height', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-body-line-height').remove();
            $('head').append('<style id="nueve4-body-line-height">body { line-height: ' + newval + '; }</style>');
        });
    });

    // Headings Font Family
    wp.customize('nueve4_headings_font_family', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-headings-font-family').remove();
            if (newval !== 'inherit') {
                $('head').append('<style id="nueve4-headings-font-family">h1, h2, h3, h4, h5, h6 { font-family: ' + newval + '; }</style>');
            }
        });
    });

    // Container Width
    wp.customize('nueve4_container_width', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-container-width').remove();
            $('head').append('<style id="nueve4-container-width">.container, .nv-container { max-width: ' + newval + 'px; }</style>');
        });
    });

    // Header Height
    wp.customize('nueve4_header_height', function(value) {
        value.bind(function(newval) {
            $('head').find('#nueve4-header-height').remove();
            $('head').append('<style id="nueve4-header-height">.site-header { min-height: ' + newval + 'px; } .site-header .navbar { min-height: ' + newval + 'px; }</style>');
        });
    });

})(jQuery);