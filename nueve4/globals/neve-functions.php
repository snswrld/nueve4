<?php
/**
 * Additional Nueve4 compatibility functions
 *
 * @package Nueve4
 */

if (!function_exists('nueve4_is_new_widget_editor')) {
    /**
     * Check if new widget editor is available
     *
     * @return bool
     */
    function nueve4_is_new_widget_editor() {
        global $wp_version;
        return version_compare($wp_version, '5.8', '>=');
    }
}

if (!function_exists('nueve4_is_new_widget_editor')) {
    function nueve4_is_new_widget_editor() {
        return nueve4_is_new_widget_editor();
    }
}

if (!function_exists('tsdk_utmify')) {
    /**
     * Add UTM parameters to URL
     *
     * @param string $url Base URL
     * @param string $source UTM source
     * @param string $medium UTM medium
     * @return string
     */
    function tsdk_utmify($url, $source = '', $medium = '') {
        $utm_params = array(
            'utm_source' => $source ?: 'nueve4',
            'utm_medium' => $medium ?: 'theme',
            'utm_campaign' => 'nueve4-theme'
        );
        
        return add_query_arg($utm_params, $url);
    }
}

if (!function_exists('nueve4_get_google_fonts')) {
    /**
     * Get Google Fonts list
     *
     * @return array
     */
    function nueve4_get_google_fonts() {
        return array(
            'System Stack' => array(
                'family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                'variants' => array('regular')
            ),
            'Arial' => array(
                'family' => 'Arial, sans-serif',
                'variants' => array('regular', 'italic', 'bold')
            ),
            'Roboto' => array(
                'family' => 'Roboto, sans-serif',
                'variants' => array('100', '300', 'regular', '500', '700', '900')
            ),
            'Open Sans' => array(
                'family' => '"Open Sans", sans-serif',
                'variants' => array('300', 'regular', '600', '700', '800')
            ),
            'Lato' => array(
                'family' => 'Lato, sans-serif',
                'variants' => array('100', '300', 'regular', '700', '900')
            ),
            'Montserrat' => array(
                'family' => 'Montserrat, sans-serif',
                'variants' => array('100', '200', '300', 'regular', '500', '600', '700', '800', '900')
            ),
            'Poppins' => array(
                'family' => 'Poppins, sans-serif',
                'variants' => array('100', '200', '300', 'regular', '500', '600', '700', '800', '900')
            )
        );
    }
}