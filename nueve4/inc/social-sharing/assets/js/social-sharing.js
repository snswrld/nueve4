/**
 * Nueve4 Social Sharing JavaScript
 */

(function($) {
    'use strict';

    const Nueve4SocialSharing = {
        init: function() {
            this.bindEvents();
            this.initAutoPost();
        },

        bindEvents: function() {
            $('.nueve4-social-btn').on('click', this.handleSocialShare);
            $('.nueve4-auto-post-toggle').on('change', this.toggleAutoPost);
        },

        handleSocialShare: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const network = $btn.data('network');
            const url = $btn.attr('href');
            
            if (network && url) {
                window.open(url, 'social-share', 'width=600,height=400,scrollbars=yes,resizable=yes');
            }
        },

        toggleAutoPost: function() {
            const $toggle = $(this);
            const isEnabled = $toggle.is(':checked');
            const network = $toggle.data('network');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_toggle_auto_post',
                    network: network,
                    enabled: isEnabled,
                    nonce: nueve4_social.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Auto-post setting updated for ' + network);
                    }
                }
            });
        },

        initAutoPost: function() {
            // Initialize auto-post scheduling interface
            $('.nueve4-schedule-picker').each(function() {
                const $picker = $(this);
                // Initialize date/time picker functionality
            });
        }
    };

    $(document).ready(function() {
        Nueve4SocialSharing.init();
    });

})(jQuery);