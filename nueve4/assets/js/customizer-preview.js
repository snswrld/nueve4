/**
 * Customizer Live Preview JavaScript
 * Provides real-time preview of customizer changes
 */

(function($) {
    'use strict';

    // Container width
    wp.customize('nueve4_container_width', function(value) {
        value.bind(function(newval) {
            $('.container, .nv-container').css('max-width', newval + 'px');
        });
    });

    // Container padding
    wp.customize('nueve4_container_padding', function(value) {
        value.bind(function(newval) {
            $('.container, .nv-container').css({
                'padding-left': newval + 'px',
                'padding-right': newval + 'px'
            });
        });
    });

    // Primary color
    wp.customize('nueve4_primary_color', function(value) {
        value.bind(function(newval) {
            $('.btn-primary, button[type="submit"], input[type="submit"]').css({
                'background-color': newval,
                'border-color': newval
            });
            $('a').css('color', newval);
            updateCustomProperty('--primary-color', newval);
        });
    });

    // Text color
    wp.customize('nueve4_text_color', function(value) {
        value.bind(function(newval) {
            $('body').css('color', newval);
            updateCustomProperty('--text-color', newval);
        });
    });

    // Background color
    wp.customize('nueve4_background_color', function(value) {
        value.bind(function(newval) {
            $('body').css('background-color', newval);
            updateCustomProperty('--background-color', newval);
        });
    });

    // Typography - Body font family
    wp.customize('nueve4_body_font_family', function(value) {
        value.bind(function(newval) {
            var fontFamily = newval === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : newval + ', sans-serif';
            $('body').css('font-family', fontFamily);
            
            // Load Google Font if needed
            if (newval !== 'inherit' && newval !== 'system-ui' && !isSystemFont(newval)) {
                loadGoogleFont(newval);
            }
        });
    });

    // Typography - Body font size
    wp.customize('nueve4_body_font_size', function(value) {
        value.bind(function(newval) {
            $('body').css('font-size', newval + 'px');
            updateCustomProperty('--body-font-size', newval + 'px');
        });
    });

    // Typography - Body font weight
    wp.customize('nueve4_body_font_weight', function(value) {
        value.bind(function(newval) {
            $('body').css('font-weight', newval);
        });
    });

    // Typography - Body line height
    wp.customize('nueve4_body_line_height', function(value) {
        value.bind(function(newval) {
            $('body').css('line-height', newval);
        });
    });

    // Header height
    wp.customize('nueve4_header_height', function(value) {
        value.bind(function(newval) {
            $('.site-header').css('min-height', newval + 'px');
            updateCustomProperty('--header-height', newval + 'px');
        });
    });

    // Header background color
    wp.customize('nueve4_header_background_color', function(value) {
        value.bind(function(newval) {
            $('.site-header').css('background-color', newval);
        });
    });

    // Header text color
    wp.customize('nueve4_header_text_color', function(value) {
        value.bind(function(newval) {
            $('.site-header, .site-header a, .site-header .site-title').css('color', newval);
        });
    });

    // Logo width
    wp.customize('nueve4_logo_width', function(value) {
        value.bind(function(newval) {
            $('.custom-logo').css('max-width', newval + 'px');
        });
    });

    // Sidebar width
    wp.customize('nueve4_sidebar_width', function(value) {
        value.bind(function(newval) {
            var contentWidth = 100 - newval;
            $('.has-sidebar .content-area').css('width', contentWidth + '%');
            $('.has-sidebar .widget-area').css('width', newval + '%');
        });
    });

    // Footer copyright
    wp.customize('nueve4_footer_copyright', function(value) {
        value.bind(function(newval) {
            $('.site-info').html(newval);
        });
    });

    // Heading typography
    var headingElements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'headings'];
    headingElements.forEach(function(element) {
        // Font family
        wp.customize('nueve4_' + element + '_font_family', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                var fontFamily = newval === 'inherit' ? 'inherit' : (newval === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : newval + ', sans-serif');
                $(selector).css('font-family', fontFamily);
                
                if (newval !== 'inherit' && newval !== 'system-ui' && !isSystemFont(newval)) {
                    loadGoogleFont(newval);
                }
            });
        });

        // Font size
        wp.customize('nueve4_' + element + '_font_size', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                $(selector).css('font-size', newval + 'px');
            });
        });

        // Font weight
        wp.customize('nueve4_' + element + '_font_weight', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                $(selector).css('font-weight', newval);
            });
        });

        // Line height
        wp.customize('nueve4_' + element + '_line_height', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                $(selector).css('line-height', newval);
            });
        });

        // Letter spacing
        wp.customize('nueve4_' + element + '_letter_spacing', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                $(selector).css('letter-spacing', newval + 'px');
            });
        });

        // Text transform
        wp.customize('nueve4_' + element + '_text_transform', function(value) {
            value.bind(function(newval) {
                var selector = element === 'headings' ? 'h1, h2, h3, h4, h5, h6' : element;
                $(selector).css('text-transform', newval);
            });
        });
    });

    // Menu typography
    wp.customize('nueve4_menu_font_family', function(value) {
        value.bind(function(newval) {
            var fontFamily = newval === 'inherit' ? 'inherit' : (newval === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : newval + ', sans-serif');
            $('.main-navigation a').css('font-family', fontFamily);
            
            if (newval !== 'inherit' && newval !== 'system-ui' && !isSystemFont(newval)) {
                loadGoogleFont(newval);
            }
        });
    });

    wp.customize('nueve4_menu_font_size', function(value) {
        value.bind(function(newval) {
            $('.main-navigation a').css('font-size', newval + 'px');
        });
    });

    // Button typography
    wp.customize('nueve4_buttons_font_family', function(value) {
        value.bind(function(newval) {
            var fontFamily = newval === 'inherit' ? 'inherit' : (newval === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : newval + ', sans-serif');
            $('button, .btn, input[type="submit"]').css('font-family', fontFamily);
            
            if (newval !== 'inherit' && newval !== 'system-ui' && !isSystemFont(newval)) {
                loadGoogleFont(newval);
            }
        });
    });

    wp.customize('nueve4_buttons_font_size', function(value) {
        value.bind(function(newval) {
            $('button, .btn, input[type="submit"]').css('font-size', newval + 'px');
        });
    });

    // Dark mode colors
    wp.customize('nueve4_dark_background_color', function(value) {
        value.bind(function(newval) {
            updateCustomProperty('--dark-background-color', newval);
        });
    });

    wp.customize('nueve4_dark_text_color', function(value) {
        value.bind(function(newval) {
            updateCustomProperty('--dark-text-color', newval);
        });
    });

    // Utility functions
    function updateCustomProperty(property, value) {
        document.documentElement.style.setProperty(property, value);
    }

    function isSystemFont(fontName) {
        var systemFonts = ['Arial', 'Helvetica', 'Georgia', 'Times', 'Verdana', 'Tahoma'];
        return systemFonts.includes(fontName);
    }

    function loadGoogleFont(fontName) {
        var fontUrl = 'https://fonts.googleapis.com/css2?family=' + fontName.replace(' ', '+') + ':wght@100;200;300;400;500;600;700;800;900&display=swap';
        
        // Check if font is already loaded
        if (!$('link[href="' + fontUrl + '"]').length) {
            $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
        }
    }

    // Initialize custom properties
    $(document).ready(function() {
        // Set initial CSS custom properties
        var properties = {
            '--primary-color': wp.customize('nueve4_primary_color')(),
            '--text-color': wp.customize('nueve4_text_color')(),
            '--background-color': wp.customize('nueve4_background_color')(),
            '--header-height': wp.customize('nueve4_header_height')() + 'px',
            '--body-font-size': wp.customize('nueve4_body_font_size')() + 'px'
        };

        Object.keys(properties).forEach(function(property) {
            if (properties[property]) {
                updateCustomProperty(property, properties[property]);
            }
        });
    });

})(jQuery);