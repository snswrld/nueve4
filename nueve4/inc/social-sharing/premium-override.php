<?php
/**
 * Premium Features Override
 * Enables all premium features by default in Nueve4
 *
 * @package Nueve4\SocialSharing
 */

// Remove all premium restrictions
add_filter('b2s_is_premium', '__return_true');
add_filter('b2s_show_premium_notice', '__return_false');
add_filter('b2s_premium_features_enabled', '__return_true');

// Override premium checks in original plugin
if (!function_exists('b2s_get_premium_status')) {
    function b2s_get_premium_status() {
        return true;
    }
}

// Remove upsell notices and premium prompts
add_action('init', function() {
    remove_all_actions('b2s_premium_notice');
    remove_all_actions('b2s_upsell_notice');
});

// Enable all networks without restrictions
add_filter('b2s_available_networks', function($networks) {
    $premium_networks = [
        'facebook_page',
        'facebook_group', 
        'twitter',
        'linkedin_page',
        'linkedin_profile',
        'instagram',
        'pinterest',
        'xing',
        'tumblr',
        'reddit',
        'medium',
        'telegram'
    ];
    
    return array_merge($networks, $premium_networks);
});

// Remove posting limits
add_filter('b2s_posting_limit', function() {
    return 999999; // Unlimited
});

// Enable advanced scheduling
add_filter('b2s_advanced_scheduling', '__return_true');

// Enable auto-posting
add_filter('b2s_auto_posting_enabled', '__return_true');

// Enable analytics and metrics
add_filter('b2s_analytics_enabled', '__return_true');

// Remove branding and attribution
add_filter('b2s_show_branding', '__return_false');
add_filter('b2s_powered_by_text', '__return_empty_string');

// Override license checks
if (!function_exists('b2s_check_license')) {
    function b2s_check_license() {
        return [
            'valid' => true,
            'premium' => true,
            'expires' => strtotime('+10 years')
        ];
    }
}