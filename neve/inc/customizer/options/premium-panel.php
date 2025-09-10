<?php
/**
 * Premium Panel for Neve Theme Customizer
 * 
 * @package Neve\Customizer\Options
 */

namespace Neve\Customizer\Options;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Panel;
use Neve\Customizer\Types\Section;
use Neve\Customizer\Types\Control;

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
                'neve_premium_features',
                array(
                    'priority' => 15,
                    'title' => __('Premium Features', 'neve'),
                    'description' => __('Advanced customization options for your website.', 'neve')
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
                'neve_performance',
                array(
                    'title' => __('Performance', 'neve'),
                    'panel' => 'neve_premium_features',
                    'priority' => 10
                )
            )
        );

        // Advanced Layout section
        $this->add_section(
            new Section(
                'neve_advanced_layout',
                array(
                    'title' => __('Advanced Layout', 'neve'),
                    'panel' => 'neve_premium_features',
                    'priority' => 20
                )
            )
        );

        // WooCommerce Premium section
        if (class_exists('WooCommerce')) {
            $this->add_section(
                new Section(
                    'neve_woocommerce_premium',
                    array(
                        'title' => __('WooCommerce Premium', 'neve'),
                        'panel' => 'neve_premium_features',
                        'priority' => 30
                    )
                )
            );
        }

        // Blog Premium section
        $this->add_section(
            new Section(
                'neve_blog_premium',
                array(
                    'title' => __('Blog Premium', 'neve'),
                    'panel' => 'neve_premium_features',
                    'priority' => 40
                )
            )
        );

        // Header/Footer Premium section
        $this->add_section(
            new Section(
                'neve_hfg_premium',
                array(
                    'title' => __('Header/Footer Premium', 'neve'),
                    'panel' => 'neve_premium_features',
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
                'neve_optimize_css',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Optimize CSS Delivery', 'neve'),
                    'description' => __('Defer non-critical CSS to improve page load speed.', 'neve'),
                    'section' => 'neve_performance',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable lazy loading
        $this->add_control(
            new Control(
                'neve_lazy_loading',
                array(
                    'default' => true,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Lazy Loading', 'neve'),
                    'description' => __('Load images only when they come into view.', 'neve'),
                    'section' => 'neve_performance',
                    'type' => 'checkbox'
                )
            )
        );

        // Preload fonts
        $this->add_control(
            new Control(
                'neve_preload_fonts',
                array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_textarea_field'
                ),
                array(
                    'label' => __('Preload Fonts', 'neve'),
                    'description' => __('Enter font URLs to preload (one per line).', 'neve'),
                    'section' => 'neve_performance',
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
                'neve_sticky_header',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Sticky Header', 'neve'),
                    'description' => __('Make header stick to top when scrolling.', 'neve'),
                    'section' => 'neve_advanced_layout',
                    'type' => 'checkbox'
                )
            )
        );

        // Header transparency
        $this->add_control(
            new Control(
                'neve_transparent_header',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Transparent Header', 'neve'),
                    'description' => __('Make header background transparent.', 'neve'),
                    'section' => 'neve_advanced_layout',
                    'type' => 'checkbox'
                )
            )
        );

        // Page transitions
        $this->add_control(
            new Control(
                'neve_page_transitions',
                array(
                    'default' => 'none',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'label' => __('Page Transitions', 'neve'),
                    'section' => 'neve_advanced_layout',
                    'type' => 'select',
                    'choices' => array(
                        'none' => __('None', 'neve'),
                        'fade' => __('Fade', 'neve'),
                        'slide' => __('Slide', 'neve'),
                        'zoom' => __('Zoom', 'neve')
                    )
                )
            )
        );

        // Custom 404 page
        $this->add_control(
            new Control(
                'neve_custom_404_page',
                array(
                    'default' => 0,
                    'sanitize_callback' => 'absint'
                ),
                array(
                    'label' => __('Custom 404 Page', 'neve'),
                    'description' => __('Select a page to use as custom 404 page.', 'neve'),
                    'section' => 'neve_advanced_layout',
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
                'neve_enable_quick_view',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Quick View', 'neve'),
                    'description' => __('Add quick view button to product cards.', 'neve'),
                    'section' => 'neve_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable wishlist
        $this->add_control(
            new Control(
                'neve_enable_wishlist',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Wishlist', 'neve'),
                    'description' => __('Add wishlist functionality to products.', 'neve'),
                    'section' => 'neve_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable product compare
        $this->add_control(
            new Control(
                'neve_enable_compare',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Product Compare', 'neve'),
                    'description' => __('Allow customers to compare products.', 'neve'),
                    'section' => 'neve_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Enable product filters
        $this->add_control(
            new Control(
                'neve_enable_product_filters',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Enable Product Filters', 'neve'),
                    'description' => __('Add advanced filtering options to shop page.', 'neve'),
                    'section' => 'neve_woocommerce_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Mini cart style
        $this->add_control(
            new Control(
                'neve_mini_cart_style',
                array(
                    'default' => 'dropdown',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'label' => __('Mini Cart Style', 'neve'),
                    'section' => 'neve_woocommerce_premium',
                    'type' => 'select',
                    'choices' => array(
                        'dropdown' => __('Dropdown', 'neve'),
                        'sidebar' => __('Sidebar', 'neve'),
                        'popup' => __('Popup', 'neve')
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
                'neve_show_reading_time',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Reading Time', 'neve'),
                    'description' => __('Display estimated reading time for posts.', 'neve'),
                    'section' => 'neve_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Post views
        $this->add_control(
            new Control(
                'neve_show_post_views',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Post Views', 'neve'),
                    'description' => __('Display view count for posts.', 'neve'),
                    'section' => 'neve_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Related posts
        $this->add_control(
            new Control(
                'neve_show_related_posts',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Related Posts', 'neve'),
                    'description' => __('Display related posts at the end of single posts.', 'neve'),
                    'section' => 'neve_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Author box
        $this->add_control(
            new Control(
                'neve_show_author_box',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Show Author Box', 'neve'),
                    'description' => __('Display author information box in single posts.', 'neve'),
                    'section' => 'neve_blog_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Infinite scroll
        $this->add_control(
            new Control(
                'neve_infinite_scroll',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Infinite Scroll', 'neve'),
                    'description' => __('Load more posts automatically when scrolling.', 'neve'),
                    'section' => 'neve_blog_premium',
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
                'neve_multiple_headers',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Multiple Headers', 'neve'),
                    'description' => __('Create different headers for different pages.', 'neve'),
                    'section' => 'neve_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Conditional display
        $this->add_control(
            new Control(
                'neve_conditional_headers',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Conditional Headers', 'neve'),
                    'description' => __('Show/hide headers based on conditions.', 'neve'),
                    'section' => 'neve_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Advanced components
        $this->add_control(
            new Control(
                'neve_advanced_components',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Advanced Components', 'neve'),
                    'description' => __('Enable social icons, contact info, and more components.', 'neve'),
                    'section' => 'neve_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );

        // Mega menu
        $this->add_control(
            new Control(
                'neve_mega_menu',
                array(
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean'
                ),
                array(
                    'label' => __('Mega Menu', 'neve'),
                    'description' => __('Enable mega menu functionality.', 'neve'),
                    'section' => 'neve_hfg_premium',
                    'type' => 'checkbox'
                )
            )
        );
    }
}