<?php
/**
 * Premium Panel for Nueve4 Theme Customizer
 * 
 * @package Nueve4\Customizer\Options
 */

namespace Nueve4\Customizer\Options;

use Nueve4\Customizer\Base_Customizer;
use Nueve4\Customizer\Types\Panel;
use Nueve4\Customizer\Types\Section;
use Nueve4\Customizer\Types\Control;

/**
 * Class Premium_Panel
 * 
 * Adds premium features panel to customizer
 */
class Premium_Panel extends Base_Customizer {

    /**
     * Add controls
     */
    public function add_controls() {
        $this->add_premium_panel();
        $this->add_premium_sections();
        $this->add_premium_controls();
    }

    /**
     * Add premium panel
     */
    private function add_premium_panel() {
        $this->add_panel(
            new Panel(
                'nueve4_premium_features',
                array(
                    'priority' => 15,
                    'title' => __('Premium Features', 'nueve4'),
                    'description' => __('Advanced customization options for your website.', 'nueve4')
                )
            )
        );
    }

    /**
     * Add premium sections
     */
    private function add_premium_sections() {
        // Performance section
        $this->add_section(
            new Section(
                'nueve4_performance',
                array(
                    'title' => __('Performance', 'nueve4'),
                    'panel' => 'nueve4_premium_features',
                    'priority' => 10
                )
            )
        );

        // Advanced Layout section
        $this->add_section(
            new Section(
                'nueve4_advanced_layout',
                array(
                    'title' => __('Advanced Layout', 'nueve4'),
                    'panel' => 'nueve4_premium_features',
                    'priority' => 20
                )
            )
        );

        // WooCommerce Premium section
        if (class_exists('WooCommerce')) {
            $this->add_section(
                new Section(
                    'nueve4_woocommerce_premium',
                    array(
                        'title' => __('WooCommerce Premium', 'nueve4'),
                        'panel' => 'nueve4_premium_features',
                        'priority' => 30
                    )
                )
            );
        }

        // Blog Premium section
        $this->add_section(
            new Section(
                'nueve4_blog_premium',
                array(
                    'title' => __('Blog Premium', 'nueve4'),
                    'panel' => 'nueve4_premium_features',
                    'priority' => 40
                )
            )
        );

        // Header/Footer Premium section
        $this->add_section(
            new Section(
                'nueve4_hfg_premium',
                array(
                    'title' => __('Header/Footer Premium', 'nueve4'),
                    'panel' => 'nueve4_premium_features',
                    'priority' => 50
                )
            )
        );
    }

    /**
     * Add premium controls
     */
    private function add_premium_controls() {
        $this->add_performance_controls();
        $this->add_advanced_layout_controls();
        $this->add_woocommerce_premium_controls();
        $this->add_blog_premium_controls();
        $this->add_hfg_premium_controls();
    }

    /**
     * Add performance controls
     */
    private function add_performance_controls() {
        // Enable CSS optimization
        $this->add_control(
            new Control(
                'nueve4_optimize_css',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Optimize CSS Delivery', 'nueve4'),
                    'description' => __('Defer non-critical CSS to improve page load speed.', 'nueve4'),
                    'section' => 'nueve4_performance',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable lazy loading
        $this->add_control(
            new Control(
                'nueve4_lazy_loading',
                array(
                    'default' => true,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Lazy Loading', 'nueve4'),
                    'description' => __('Load images only when they come into view.', 'nueve4'),
                    'section' => 'nueve4_performance',
                    'type' => 'checkbox'
                )
            )
        );

        // Preload fonts
        $this->add_control(
            new Control(
                'nueve4_preload_fonts',
                array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_textarea_field'
                ),
                array(
                    'label' => __('Preload Fonts', 'nueve4'),
                    'description' => __('Enter font URLs to preload (one per line).', 'nueve4'),
                    'section' => 'nueve4_performance',
                    'type' => 'textarea'
                )
            )
        );
    }

    /**
     * Add advanced layout controls
     */
    private function add_advanced_layout_controls() {
        // Enable sticky header
        $this->add_control(
            new Control(
                'nueve4_sticky_header',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Sticky Header', 'nueve4'),
                    'description' => __('Make header stick to top when scrolling.', 'nueve4'),
                    'section' => 'nueve4_advanced_layout',
                    'type' => 'checkbox'
                )
            )
        );

        // Header transparency
        $this->add_control(
            new Control(
                'nueve4_transparent_header',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Transparent Header', 'nueve4'),
                    'description' => __('Make header background transparent.', 'nueve4'),
                    'section' => 'nueve4_advanced_layout',
                    'type' => 'checkbox'
                )
            )
        );

        // Page transitions
        $this->add_control(
            new Control(
                'nueve4_page_transitions',
                array(
                    'default' => 'none',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'label' => __('Page Transitions', 'nueve4'),
                    'section' => 'nueve4_advanced_layout',
                    'type' => 'select',
                    'choices' => array(
                        'none' => __('None', 'nueve4'),
                        'fade' => __('Fade', 'nueve4'),
                        'slide' => __('Slide', 'nueve4'),
                        'zoom' => __('Zoom', 'nueve4')
                    )
                )
            )
        );

        // Custom 404 page
        $this->add_control(
            new Control(
                'nueve4_custom_404_page',
                array(
                    'default' => 0,
                    'sanitize_callback' => 'absint'
                ),
                array(
                    'label' => __('Custom 404 Page', 'nueve4'),
                    'description' => __('Select a page to use as custom 404 page.', 'nueve4'),
                    'section' => 'nueve4_advanced_layout',
                    'type' => 'dropdown-pages'
                )
            )
        );
    }

    /**
     * Add WooCommerce premium controls
     */
    private function add_woocommerce_premium_controls() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Enable quick view
        $this->add_control(
            new Control(
                'nueve4_enable_quick_view',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Quick View', 'nueve4'),
                    'description' => __('Add quick view button to product cards.', 'nueve4'),
                    'section' => 'nueve4_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable wishlist
        $this->add_control(
            new Control(
                'nueve4_enable_wishlist',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Wishlist', 'nueve4'),
                    'description' => __('Add wishlist functionality to products.', 'nueve4'),
                    'section' => 'nueve4_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable product compare
        $this->add_control(
            new Control(
                'nueve4_enable_compare',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Product Compare', 'nueve4'),
                    'description' => __('Allow customers to compare products.', 'nueve4'),
                    'section' => 'nueve4_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable product filters
        $this->add_control(
            new Control(
                'nueve4_enable_product_filters',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Product Filters', 'nueve4'),
                    'description' => __('Add advanced filtering options to shop page.', 'nueve4'),
                    'section' => 'nueve4_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Mini cart style
        $this->add_control(
            new Control(
                'nueve4_mini_cart_style',
                array(
                    'default' => 'dropdown',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'label' => __('Mini Cart Style', 'nueve4'),
                    'section' => 'nueve4_woocommerce_premium',
                    'type' => 'select',
                    'choices' => array(
                        'dropdown' => __('Dropdown', 'nueve4'),
                        'sidebar' => __('Sidebar', 'nueve4'),
                        'popup' => __('Popup', 'nueve4')
                    )
                )
            )
        );
    }

    /**
     * Add blog premium controls
     */
    private function add_blog_premium_controls() {
        // Reading time
        $this->add_control(
            new Control(
                'nueve4_show_reading_time',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Reading Time', 'nueve4'),
                    'description' => __('Display estimated reading time for posts.', 'nueve4'),
                    'section' => 'nueve4_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Post views
        $this->add_control(
            new Control(
                'nueve4_show_post_views',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Post Views', 'nueve4'),
                    'description' => __('Display view count for posts.', 'nueve4'),
                    'section' => 'nueve4_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Related posts
        $this->add_control(
            new Control(
                'nueve4_show_related_posts',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Related Posts', 'nueve4'),
                    'description' => __('Display related posts at the end of single posts.', 'nueve4'),
                    'section' => 'nueve4_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Author box
        $this->add_control(
            new Control(
                'nueve4_show_author_box',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Author Box', 'nueve4'),
                    'description' => __('Display author information box in single posts.', 'nueve4'),
                    'section' => 'nueve4_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Infinite scroll
        $this->add_control(
            new Control(
                'nueve4_infinite_scroll',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Infinite Scroll', 'nueve4'),
                    'description' => __('Load more posts automatically when scrolling.', 'nueve4'),
                    'section' => 'nueve4_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );
    }

    /**
     * Add header/footer premium controls
     */
    private function add_hfg_premium_controls() {
        // Multiple headers
        $this->add_control(
            new Control(
                'nueve4_multiple_headers',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Multiple Headers', 'nueve4'),
                    'description' => __('Create different headers for different pages.', 'nueve4'),
                    'section' => 'nueve4_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Conditional display
        $this->add_control(
            new Control(
                'nueve4_conditional_headers',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Conditional Headers', 'nueve4'),
                    'description' => __('Show/hide headers based on conditions.', 'nueve4'),
                    'section' => 'nueve4_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Advanced components
        $this->add_control(
            new Control(
                'nueve4_advanced_components',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Advanced Components', 'nueve4'),
                    'description' => __('Enable social icons, contact info, and more components.', 'nueve4'),
                    'section' => 'nueve4_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Mega menu
        $this->add_control(
            new Control(
                'nueve4_mega_menu',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Mega Menu', 'nueve4'),
                    'description' => __('Enable mega menu functionality.', 'nueve4'),
                    'section' => 'nueve4_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );
    }
}