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



if (!function_exists('nueve4_is_amp')) {
    /**
     * Check if AMP is active
     *
     * @return bool
     */
    function nueve4_is_amp() {
        return function_exists('is_amp_endpoint') && is_amp_endpoint();
    }
}

if (!function_exists('nueve4_get_global_colors_default')) {
    /**
     * Get default global colors configuration
     *
     * @param bool $with_palettes Whether to include palettes
     * @return array
     */
    function nueve4_get_global_colors_default($with_palettes = false) {
        $default_colors = array(
            'nv-primary-accent' => '#0366d6',
            'nv-secondary-accent' => '#0e509a',
            'nv-site-bg' => '#ffffff',
            'nv-light-bg' => '#f8f9fa',
            'nv-dark-bg' => '#14171c',
            'nv-text-color' => '#393939',
            'nv-text-dark-bg' => '#ffffff',
            'nv-c-1' => '#77b978',
            'nv-c-2' => '#f37262'
        );
        
        $config = array(
            'activePalette' => 'base',
            'palettes' => array(
                'base' => array(
                    'name' => 'Base',
                    'colors' => $default_colors
                )
            )
        );
        
        if ($with_palettes) {
            $config['palettes']['darkMode'] = array(
                'name' => 'Dark Mode',
                'colors' => array(
                    'nv-primary-accent' => '#0366d6',
                    'nv-secondary-accent' => '#0e509a', 
                    'nv-site-bg' => '#14171c',
                    'nv-light-bg' => '#1e2328',
                    'nv-dark-bg' => '#14171c',
                    'nv-text-color' => '#ffffff',
                    'nv-text-dark-bg' => '#ffffff',
                    'nv-c-1' => '#77b978',
                    'nv-c-2' => '#f37262'
                )
            );
        }
        
        return $config;
    }
}

if (!function_exists('nueve4_body_attrs')) {
    /**
     * Add body attributes
     *
     * @return void
     */
    function nueve4_body_attrs() {
        $attrs = array();
        
        // Add data attributes for theme customization
        if (is_customize_preview()) {
            $attrs[] = 'data-customizer-preview="true"';
        }
        
        // Add AMP attribute if AMP is active
        if (nueve4_is_amp()) {
            $attrs[] = 'amp';
        }
        
        // Output attributes
        if (!empty($attrs)) {
            echo ' ' . implode(' ', $attrs);
        }
    }
}

if (!function_exists('nueve4_can_use_conditional_header')) {
    /**
     * Check if conditional header can be used
     *
     * @return bool
     */
    function nueve4_can_use_conditional_header() {
        return apply_filters('nueve4_has_valid_addons', false);
    }
}

if (!function_exists('nueve4_get_default_meta_value')) {
    /**
     * Get default meta value
     *
     * @param string $meta_key Meta key to get default for
     * @return mixed Default meta value
     */
    function nueve4_get_default_meta_value($meta_key) {
        $defaults = array(
            'nueve4_meta_sidebar' => 'default',
            'nueve4_meta_container' => 'default',
            'nueve4_meta_enable_content_width' => false,
            'nueve4_meta_content_width' => 70,
            'nueve4_meta_title_alignment' => 'default',
            'nueve4_meta_author_avatar' => 'default',
            'nueve4_meta_disable_header' => false,
            'nueve4_meta_disable_footer' => false,
            'nueve4_meta_disable_title' => false
        );
        
        return isset($defaults[$meta_key]) ? $defaults[$meta_key] : '';
    }
}

if (!function_exists('nueve4_get_headings_selectors')) {
    /**
     * Get headings selectors
     *
     * @return array
     */
    function nueve4_get_headings_selectors() {
        return array(
            'h1' => 'h1, .single h1.entry-title',
            'h2' => 'h2',
            'h3' => 'h3, .woocommerce-checkout h3',
            'h4' => 'h4',
            'h5' => 'h5',
            'h6' => 'h6'
        );
    }
}

if (!function_exists('nueve4_external_link')) {
    /**
     * Create external link with UTM parameters
     *
     * @param string $url Link URL
     * @param string $text Link text
     * @return string
     */
    function nueve4_external_link($url, $text) {
        if (function_exists('tsdk_utmify')) {
            $url = tsdk_utmify($url, 'nueve4', 'customizer');
        }
        return sprintf('<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url($url), esc_html($text));
    }
}

if (!function_exists('nueve4_get_standard_fonts')) {
    /**
     * Get standard system fonts
     *
     * @param bool $with_variants Whether to include font variants
     * @return array
     */
    function nueve4_get_standard_fonts($with_variants = false) {
        $fonts = array(
            'Arial' => 'Arial, sans-serif',
            'Helvetica' => 'Helvetica, sans-serif',
            'Times New Roman' => '"Times New Roman", serif',
            'Georgia' => 'Georgia, serif',
            'Verdana' => 'Verdana, sans-serif',
            'Trebuchet MS' => '"Trebuchet MS", sans-serif',
            'Impact' => 'Impact, sans-serif',
            'Courier New' => '"Courier New", monospace'
        );
        
        if ($with_variants) {
            $fonts_with_variants = array();
            foreach ($fonts as $name => $family) {
                $fonts_with_variants[$name] = array(
                    'family' => $family,
                    'variants' => array('regular', 'italic', 'bold', 'bold-italic')
                );
            }
            return $fonts_with_variants;
        }
        
        return $fonts;
    }
}

if (!function_exists('nueve4_get_google_fonts')) {
    /**
     * Get Google Fonts list
     *
     * @param bool $with_variants Whether to include font variants
     * @return array
     */
    function nueve4_get_google_fonts($with_variants = false) {
        $fonts_file = get_template_directory() . '/globals/google-fonts.php';
        if (!file_exists($fonts_file)) {
            return array();
        }
        
        $fonts = include $fonts_file;
        
        if (!$with_variants) {
            return array_keys($fonts);
        }
        
        return $fonts;
    }
}



if (!function_exists('nueve4_get_button_appearance_default')) {
    /**
     * Get default button appearance settings
     *
     * @param string $type Button type (primary|secondary)
     * @return array Default button settings
     */
    function nueve4_get_button_appearance_default($type = 'primary') {
        $defaults = array(
            'borderRadius' => array(
                'top' => 3,
                'right' => 3,
                'bottom' => 3,
                'left' => 3
            ),
            'borderWidth' => array(
                'top' => 1,
                'right' => 1,
                'bottom' => 1,
                'left' => 1
            ),
            'useShadow' => false,
            'useShadowHover' => false,
            'shadowColor' => 'rgba(0,0,0,0.5)',
            'shadowColorHover' => 'rgba(0,0,0,0.5)',
            'shadowProperties' => array(
                'blur' => 5,
                'width' => 0,
                'height' => 0
            )
        );
        
        return $defaults;
    }
}

if (!function_exists('tsdk_utmify')) {
    /**
     * Add UTM parameters to URL (fallback if SDK not loaded)
     *
     * @param string $url URL to modify
     * @param string $source UTM source
     * @param string $medium UTM medium
     * @return string Modified URL
     */
    function tsdk_utmify($url, $source = '', $medium = '') {
        if (empty($source)) {
            return $url;
        }
        
        $params = array(
            'utm_source' => $source,
            'utm_medium' => $medium ?: 'theme',
            'utm_campaign' => 'nueve4'
        );
        
        return add_query_arg($params, $url);
    }
}

if (!function_exists('nueve4_value_is_zero')) {
    /**
     * Check if value is zero
     *
     * @param mixed $value Value to check
     * @return bool True if value is zero
     */
    function nueve4_value_is_zero($value) {
        return $value === 0 || $value === '0' || $value === 0.0;
    }
}