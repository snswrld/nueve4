<?php
/**
 * Utility functions for Nueve4 theme
 *
 * @package Nueve4
 */

if (!function_exists('nueve4_was_auto_migrated_to_new')) {
    /**
     * Check if theme was auto migrated to new skin
     *
     * @return bool
     */
    function nueve4_was_auto_migrated_to_new() {
        return get_theme_mod('nueve4_auto_migrated_to_new_skin', false);
    }
}

if (!function_exists('nueve4_had_old_hfb')) {
    /**
     * Check if theme had old header footer builder
     *
     * @return bool
     */
    function nueve4_had_old_hfb() {
        return get_theme_mod('nueve4_had_old_hfb', false);
    }
}

if (!function_exists('nueve4_is_new_skin')) {
    /**
     * Check if using new skin
     *
     * @return bool
     */
    function nueve4_is_new_skin() {
        return get_theme_mod('nueve4_new_skin', 'new') === 'new';
    }
}

if (!function_exists('nueve4_is_new_builder')) {
    /**
     * Check if using new builder
     *
     * @return bool
     */
    function nueve4_is_new_builder() {
        return get_theme_mod('nueve4_migrated_builders', true);
    }
}



if (!function_exists('nueve4_is_using_wp_version')) {
    /**
     * Check WordPress version
     *
     * @param string $version Version to check
     * @return bool
     */
    function nueve4_is_using_wp_version($version) {
        global $wp_version;
        return version_compare($wp_version, $version, '>=');
    }
}

// Legacy compatibility functions
if (!function_exists('neve_was_auto_migrated_to_new')) {
    function neve_was_auto_migrated_to_new() {
        return nueve4_was_auto_migrated_to_new();
    }
}

if (!function_exists('neve_had_old_hfb')) {
    function neve_had_old_hfb() {
        return nueve4_had_old_hfb();
    }
}

if (!function_exists('neve_is_new_skin')) {
    function neve_is_new_skin() {
        return nueve4_is_new_skin();
    }
}

if (!function_exists('neve_is_new_builder')) {
    function neve_is_new_builder() {
        return nueve4_is_new_builder();
    }
}



if (!function_exists('neve_is_using_wp_version')) {
    function neve_is_using_wp_version($version) {
        return nueve4_is_using_wp_version($version);
    }
}