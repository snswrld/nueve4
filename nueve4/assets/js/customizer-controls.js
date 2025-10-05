/**
 * Customizer Controls Enhancement
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Add tooltips to controls
        $('.customize-control').each(function() {
            var $control = $(this);
            var $label = $control.find('.customize-control-title');
            
            if ($label.length) {
                $label.attr('title', $label.text() + ' - Click to expand options');
            }
        });

        // Enhance range controls with value display
        $('.customize-control-range input[type="range"]').each(function() {
            var $range = $(this);
            var $wrapper = $range.closest('.customize-control');
            
            // Add value display
            if (!$wrapper.find('.range-value').length) {
                $range.after('<span class="range-value">' + $range.val() + '</span>');
            }
            
            // Update value display on change
            $range.on('input', function() {
                $(this).siblings('.range-value').text($(this).val());
            });
        });

        // Add reset buttons to controls
        $('.customize-control').each(function() {
            var $control = $(this);
            var $input = $control.find('input, select, textarea').first();
            
            if ($input.length && !$control.find('.reset-control').length) {
                var defaultValue = $input.data('default') || '';
                
                $control.find('.customize-control-title').append(
                    '<button type="button" class="reset-control" title="Reset to default">↺</button>'
                );
                
                $control.find('.reset-control').on('click', function(e) {
                    e.preventDefault();
                    $input.val(defaultValue).trigger('change');
                });
            }
        });

        // Improve color picker accessibility
        $('.wp-color-picker').each(function() {
            var $picker = $(this);
            var $button = $picker.siblings('.wp-picker-container').find('.wp-color-result');
            
            $button.attr('aria-label', 'Choose color');
        });
    });

    // Add custom CSS for enhanced controls
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .customize-control .range-value {
                display: inline-block;
                margin-left: 10px;
                padding: 2px 6px;
                background: #f1f1f1;
                border-radius: 3px;
                font-size: 11px;
                min-width: 30px;
                text-align: center;
            }
            
            .customize-control .reset-control {
                float: right;
                background: none;
                border: none;
                color: #666;
                cursor: pointer;
                font-size: 14px;
                padding: 0;
                margin: 0;
                line-height: 1;
            }
            
            .customize-control .reset-control:hover {
                color: #0073aa;
            }
            
            .customize-control-title {
                position: relative;
            }
            
            .customize-control input[type="range"] {
                width: 70%;
            }
            
            .wp-picker-container .wp-color-result {
                border-radius: 3px;
            }
        `)
        .appendTo('head');

})(jQuery);