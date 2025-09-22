<?php
/**
 * Test theme loading
 */

// Simulate WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

// Mock WordPress functions that might be called
if (!function_exists('get_template_directory')) {
    function get_template_directory() {
        return dirname(__FILE__);
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri() {
        return 'http://localhost/nueve4';
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1) {
        // Mock implementation
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $args = 1) {
        // Mock implementation
        return true;
    }
}

if (!function_exists('get_theme_mod')) {
    function get_theme_mod($name, $default = false) {
        return $default;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value) {
        return $value;
    }
}

if (!function_exists('did_action')) {
    function did_action($hook) {
        return false;
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return htmlspecialchars($text);
    }
}

if (!function_exists('load_theme_textdomain')) {
    function load_theme_textdomain($domain, $path) {
        return true;
    }
}

if (!function_exists('is_customize_preview')) {
    function is_customize_preview() {
        return false;
    }
}

// Test basic theme loading
echo "Testing Nueve4 theme loading...\n";

try {
    // Test if functions.php can be loaded
    require_once dirname(__FILE__) . '/functions.php';
    echo "✓ functions.php loaded successfully\n";
    
    // Test if constants are defined
    if (defined('NUEVE4_VERSION')) {
        echo "✓ NUEVE4_VERSION defined: " . NUEVE4_VERSION . "\n";
    } else {
        echo "✗ NUEVE4_VERSION not defined\n";
    }
    
    if (defined('NUEVE4_ASSETS_URL')) {
        echo "✓ NUEVE4_ASSETS_URL defined: " . NUEVE4_ASSETS_URL . "\n";
    } else {
        echo "✗ NUEVE4_ASSETS_URL not defined\n";
    }
    
    // Test if classes can be autoloaded
    if (class_exists('\\Nueve4\\Core\\Bootstrap')) {
        echo "✓ Bootstrap class exists\n";
    } else {
        echo "✗ Bootstrap class not found\n";
    }
    
    if (class_exists('\\Nueve4\\Customizer\\Assets_Manager')) {
        echo "✓ Assets_Manager class exists\n";
    } else {
        echo "✗ Assets_Manager class not found\n";
    }
    
    echo "✓ Theme loading test completed successfully\n";
    
} catch (Exception $e) {
    echo "✗ Error loading theme: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "✗ Fatal error loading theme: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}