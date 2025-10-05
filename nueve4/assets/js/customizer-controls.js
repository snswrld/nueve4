/**
 * Customizer Controls JavaScript
 * Enhanced controls and interactions for the customizer
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeCustomControls();
        initializeConditionalControls();
        initializeFontPreview();
        initializeColorPalettes();
        initializeResponsiveControls();
    });

    /**
     * Initialize custom control enhancements
     */
    function initializeCustomControls() {
        // Enhanced range controls with live values
        $('.customize-control-range input[type="range"]').each(function() {
            var $range = $(this);
            var $output = $('<output class="range-value"></output>');
            $range.after($output);
            
            function updateOutput() {
                $output.text($range.val() + ($range.data('unit') || ''));
            }
            
            updateOutput();
            $range.on('input', updateOutput);
        });

        // Enhanced color controls with opacity
        $('.customize-control-color input[type="text"]').each(function() {
            var $input = $(this);
            if (!$input.hasClass('wp-color-picker')) {
                $input.wpColorPicker({
                    change: function(event, ui) {
                        $input.trigger('change');
                    },
                    clear: function() {
                        $input.trigger('change');
                    }
                });
            }
        });

        // Typography control enhancements
        $('.customize-control select[data-customize-setting-link*="font_family"]').each(function() {
            var $select = $(this);
            $select.on('change', function() {
                var fontName = $(this).val();
                if (fontName && fontName !== 'inherit' && fontName !== 'system-ui') {
                    loadGoogleFontPreview(fontName);
                }
            });
        });
    }

    /**
     * Initialize conditional control visibility
     */
    function initializeConditionalControls() {
        // Show/hide controls based on other settings
        wp.customize('nueve4_enable_dark_mode', function(setting) {
            setting.bind(function(value) {
                var darkModeControls = [
                    'nueve4_dark_background_color',
                    'nueve4_dark_text_color',
                    'nueve4_dark_border_color'
                ];
                
                darkModeControls.forEach(function(controlId) {
                    var control = wp.customize.control(controlId);
                    if (control) {
                        control.container.toggle(value);
                    }
                });
            });
        });

        // Header layout conditional controls
        wp.customize('nueve4_header_layout', function(setting) {
            setting.bind(function(value) {
                var transparentControl = wp.customize.control('nueve4_transparent_header');
                if (transparentControl) {
                    transparentControl.container.toggle(value !== 'minimal');
                }
            });
        });

        // Blog layout conditional controls
        wp.customize('nueve4_blog_layout_type', function(setting) {
            setting.bind(function(value) {
                var columnsControl = wp.customize.control('nueve4_blog_columns');
                if (columnsControl) {
                    columnsControl.container.toggle(['grid', 'masonry', 'cards'].includes(value));
                }
            });
        });

        // Sidebar conditional controls
        wp.customize('nueve4_sidebar_position', function(setting) {
            setting.bind(function(value) {
                var widthControl = wp.customize.control('nueve4_sidebar_width');
                if (widthControl) {
                    widthControl.container.toggle(value !== 'none');
                }
            });
        });
    }

    /**
     * Initialize font preview functionality
     */
    function initializeFontPreview() {
        $('.customize-control select[data-customize-setting-link*="font_family"]').each(function() {
            var $select = $(this);
            var $preview = $('<div class="font-preview">The quick brown fox jumps over the lazy dog</div>');
            $select.after($preview);
            
            function updatePreview() {
                var fontName = $select.val();
                if (fontName && fontName !== 'inherit') {
                    var fontFamily = fontName === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : fontName + ', sans-serif';
                    $preview.css('font-family', fontFamily).show();
                    
                    if (fontName !== 'system-ui' && !isSystemFont(fontName)) {
                        loadGoogleFontPreview(fontName);
                    }
                } else {
                    $preview.hide();
                }
            }
            
            updatePreview();
            $select.on('change', updatePreview);
        });
    }

    /**
     * Initialize color palette functionality
     */
    function initializeColorPalettes() {
        // Add color palette presets
        var colorPalettes = {
            'default': {
                name: 'Default',
                colors: {
                    'nueve4_primary_color': '#0073aa',
                    'nueve4_secondary_color': '#666666',
                    'nueve4_accent_color': '#ff6b35',
                    'nueve4_text_color': '#333333',
                    'nueve4_background_color': '#ffffff'
                }
            },
            'dark': {
                name: 'Dark',
                colors: {
                    'nueve4_primary_color': '#4a9eff',
                    'nueve4_secondary_color': '#8a8a8a',
                    'nueve4_accent_color': '#ff6b35',
                    'nueve4_text_color': '#ffffff',
                    'nueve4_background_color': '#1a1a1a'
                }
            },
            'nature': {
                name: 'Nature',
                colors: {
                    'nueve4_primary_color': '#2d5016',
                    'nueve4_secondary_color': '#8fbc8f',
                    'nueve4_accent_color': '#ff8c00',
                    'nueve4_text_color': '#2f4f2f',
                    'nueve4_background_color': '#f0fff0'
                }
            },
            'ocean': {
                name: 'Ocean',
                colors: {
                    'nueve4_primary_color': '#006994',
                    'nueve4_secondary_color': '#4682b4',
                    'nueve4_accent_color': '#ff7f50',
                    'nueve4_text_color': '#2f4f4f',
                    'nueve4_background_color': '#f0f8ff'
                }
            }
        };

        // Add palette selector to colors section
        var $colorsSection = $('#accordion-section-nueve4_global_colors');
        if ($colorsSection.length) {
            var $paletteContainer = $('<div class="color-palette-presets"><h4>Color Presets</h4></div>');
            var $paletteButtons = $('<div class="palette-buttons"></div>');
            
            Object.keys(colorPalettes).forEach(function(paletteKey) {
                var palette = colorPalettes[paletteKey];
                var $button = $('<button type="button" class="palette-button" data-palette="' + paletteKey + '">' + palette.name + '</button>');
                
                $button.on('click', function(e) {
                    e.preventDefault();
                    applyColorPalette(palette.colors);
                });
                
                $paletteButtons.append($button);
            });
            
            $paletteContainer.append($paletteButtons);
            $colorsSection.find('.accordion-section-content').prepend($paletteContainer);
        }
    }

    /**
     * Initialize responsive controls
     */
    function initializeResponsiveControls() {
        // Add responsive preview buttons
        var $responsiveButtons = $('<div class="responsive-preview-buttons">' +
            '<button type="button" class="preview-desktop active" data-device="desktop">Desktop</button>' +
            '<button type="button" class="preview-tablet" data-device="tablet">Tablet</button>' +
            '<button type="button" class="preview-mobile" data-device="mobile">Mobile</button>' +
        '</div>');
        
        $('#customize-header-actions').after($responsiveButtons);
        
        $responsiveButtons.on('click', 'button', function() {
            var device = $(this).data('device');
            $responsiveButtons.find('button').removeClass('active');
            $(this).addClass('active');
            
            // Trigger WordPress responsive preview
            wp.customize.previewedDevice(device);
        });
    }

    /**
     * Apply color palette
     */
    function applyColorPalette(colors) {
        Object.keys(colors).forEach(function(settingId) {
            var setting = wp.customize(settingId);
            if (setting) {
                setting.set(colors[settingId]);
            }
        });
    }

    /**
     * Load Google Font for preview
     */
    function loadGoogleFontPreview(fontName) {
        var fontUrl = 'https://fonts.googleapis.com/css2?family=' + fontName.replace(' ', '+') + ':wght@100;200;300;400;500;600;700;800;900&display=swap';
        
        if (!$('link[href="' + fontUrl + '"]').length) {
            $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
        }
    }

    /**
     * Check if font is a system font
     */
    function isSystemFont(fontName) {
        var systemFonts = ['Arial', 'Helvetica', 'Georgia', 'Times', 'Verdana', 'Tahoma'];
        return systemFonts.includes(fontName);
    }

    /**
     * Add search functionality to customizer
     */
    function initializeCustomizerSearch() {
        var $searchContainer = $('<div class="customizer-search-container">' +
            '<input type="text" id="customizer-search" placeholder="Search settings..." />' +
            '<button type="button" class="search-clear">×</button>' +
        '</div>');
        
        $('#customize-header-actions').before($searchContainer);
        
        var $searchInput = $('#customizer-search');
        var $clearButton = $('.search-clear');
        
        $searchInput.on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            filterCustomizerControls(searchTerm);
            $clearButton.toggle(searchTerm.length > 0);
        });
        
        $clearButton.on('click', function() {
            $searchInput.val('').trigger('input');
        });
    }

    /**
     * Filter customizer controls based on search
     */
    function filterCustomizerControls(searchTerm) {
        $('.customize-control').each(function() {
            var $control = $(this);
            var controlText = $control.find('label, .customize-control-title').text().toLowerCase();
            var controlDescription = $control.find('.description, .customize-control-description').text().toLowerCase();
            var matches = controlText.includes(searchTerm) || controlDescription.includes(searchTerm);
            
            $control.toggle(searchTerm === '' || matches);
        });
        
        // Show/hide sections based on visible controls
        $('.accordion-section').each(function() {
            var $section = $(this);
            var hasVisibleControls = $section.find('.customize-control:visible').length > 0;
            $section.toggle(hasVisibleControls);
        });
    }

    // Initialize search functionality
    initializeCustomizerSearch();

    /**
     * Add import/export functionality
     */
    function initializeImportExport() {
        var $importExportContainer = $('<div class="import-export-container">' +
            '<h3>Import/Export Settings</h3>' +
            '<button type="button" class="button export-settings">Export Settings</button>' +
            '<input type="file" id="import-file" accept=".json" style="display:none;" />' +
            '<button type="button" class="button import-settings">Import Settings</button>' +
        '</div>');
        
        // Add to a suitable location in customizer
        $('#customize-theme-controls').append($importExportContainer);
        
        $('.export-settings').on('click', function() {
            exportCustomizerSettings();
        });
        
        $('.import-settings').on('click', function() {
            $('#import-file').click();
        });
        
        $('#import-file').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                importCustomizerSettings(file);
            }
        });
    }

    /**
     * Export customizer settings
     */
    function exportCustomizerSettings() {
        var settings = {};
        
        // Get all Nueve4 customizer settings
        wp.customize.each(function(setting) {
            if (setting.id.startsWith('nueve4_')) {
                settings[setting.id] = setting.get();
            }
        });
        
        var dataStr = JSON.stringify(settings, null, 2);
        var dataBlob = new Blob([dataStr], {type: 'application/json'});
        var url = URL.createObjectURL(dataBlob);
        
        var link = document.createElement('a');
        link.href = url;
        link.download = 'nueve4-customizer-settings.json';
        link.click();
        
        URL.revokeObjectURL(url);
    }

    /**
     * Import customizer settings
     */
    function importCustomizerSettings(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            try {
                var settings = JSON.parse(e.target.result);
                
                Object.keys(settings).forEach(function(settingId) {
                    var setting = wp.customize(settingId);
                    if (setting) {
                        setting.set(settings[settingId]);
                    }
                });
                
                alert('Settings imported successfully!');
            } catch (error) {
                alert('Error importing settings: ' + error.message);
            }
        };
        reader.readAsText(file);
    }

    // Initialize import/export
    initializeImportExport();

})(jQuery);