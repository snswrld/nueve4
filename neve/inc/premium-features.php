<?php
/**
 * Premium Features Enabler for Neve Theme
 * 
 * This file enables all premium features and adds new premium functionality
 * 
 * @package Neve\Premium
 */

namespace Neve\Premium;

/**
 * Class Premium_Features
 * 
 * Enables all premium features and adds new functionality
 */
class Premium_Features {

    /**
     * Initialize premium features
     */
    public function init() {
        // Define premium version to unlock features
        if (!defined('NEVE_PRO_VERSION')) {
            define('NEVE_PRO_VERSION', '2.8.0');
        }
        
        if (!defined('NEVE_PRO_BASEFILE')) {
            define('NEVE_PRO_BASEFILE', __FILE__);
        }

        // Enable premium features
        add_filter('neve_has_valid_addons', '__return_true');
        add_filter('neve_pro_addon_is_active', '__return_true');
        
        // Remove upsells
        add_filter('neve_upgrade_link_from_child_theme_filter', array($this, 'remove_upgrade_links'));
        
        // Enable premium layouts
        $this->enable_premium_layouts();
        
        // Add premium blocks
        $this->add_premium_blocks();
        
        // Add premium customizer options
        $this->add_premium_customizer_options();
        
        // Load premium blocks category
        require_once get_template_directory() . '/inc/premium-blocks-category.php';
        
        // Load premium customizer panel
        require_once get_template_directory() . '/inc/customizer/options/premium-panel.php';
        $premium_panel = new \Neve\Customizer\Options\Premium_Panel();
        $premium_panel->init();
        
        // Add premium header/footer components
        $this->add_premium_hfg_components();
        
        // Add premium WooCommerce features
        $this->add_premium_woocommerce_features();
        
        // Add custom CSS and JS capabilities
        $this->add_custom_code_features();
        
        // Add performance features
        $this->add_performance_features();
    }

    /**
     * Remove upgrade links
     */
    public function remove_upgrade_links($url) {
        return '#';
    }

    /**
     * Enable premium layouts
     */
    private function enable_premium_layouts() {
        // Override checkout layout sanitization
        add_filter('theme_mod_neve_checkout_page_layout', array($this, 'enable_checkout_layouts'));
        
        // Enable product card layouts
        add_filter('theme_mod_neve_product_card_layout', array($this, 'enable_product_layouts'));
        
        // Enable category card layouts
        add_filter('theme_mod_neve_category_card_layout', array($this, 'enable_category_layouts'));
        
        // Enable content alignment options
        add_filter('theme_mod_neve_product_content_alignment', array($this, 'enable_content_alignment'));
        
        // Enable sale tag positions
        add_filter('theme_mod_neve_sale_tag_position', array($this, 'enable_sale_tag_positions'));
        
        // Enable add to cart display options
        add_filter('theme_mod_neve_add_to_cart_display', array($this, 'enable_add_to_cart_options'));
    }

    /**
     * Enable checkout layouts
     */
    public function enable_checkout_layouts($value) {
        return get_theme_mod('neve_checkout_page_layout', 'standard');
    }

    /**
     * Enable product layouts
     */
    public function enable_product_layouts($value) {
        return get_theme_mod('neve_product_card_layout', 'grid');
    }

    /**
     * Enable category layouts
     */
    public function enable_category_layouts($value) {
        return get_theme_mod('neve_category_card_layout', 'default');
    }

    /**
     * Enable content alignment
     */
    public function enable_content_alignment($value) {
        return get_theme_mod('neve_product_content_alignment', 'left');
    }

    /**
     * Enable sale tag positions
     */
    public function enable_sale_tag_positions($value) {
        return get_theme_mod('neve_sale_tag_position', 'inside');
    }

    /**
     * Enable add to cart options
     */
    public function enable_add_to_cart_options($value) {
        return get_theme_mod('neve_add_to_cart_display', 'none');
    }

    /**
     * Add premium blocks
     */
    private function add_premium_blocks() {
        add_action('init', array($this, 'register_premium_blocks'));
    }

    /**
     * Register premium blocks
     */
    public function register_premium_blocks() {
        // Advanced testimonials block
        wp_register_script(
            'neve-testimonials-block',
            get_template_directory_uri() . '/assets/js/premium-blocks/testimonials.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            NEVE_VERSION
        );

        register_block_type('neve/testimonials', array(
            'editor_script' => 'neve-testimonials-block',
            'render_callback' => array($this, 'render_testimonials_block')
        ));

        // Advanced pricing table block
        wp_register_script(
            'neve-pricing-block',
            get_template_directory_uri() . '/assets/js/premium-blocks/pricing.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            NEVE_VERSION
        );

        register_block_type('neve/pricing-table', array(
            'editor_script' => 'neve-pricing-block',
            'render_callback' => array($this, 'render_pricing_block')
        ));

        // Team members block
        wp_register_script(
            'neve-team-block',
            get_template_directory_uri() . '/assets/js/premium-blocks/team.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            NEVE_VERSION
        );

        register_block_type('neve/team-members', array(
            'editor_script' => 'neve-team-block',
            'render_callback' => array($this, 'render_team_block')
        ));
    }

    /**
     * Render testimonials block
     */
    public function render_testimonials_block($attributes) {
        $testimonials = isset($attributes['testimonials']) ? $attributes['testimonials'] : array();
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
        
        ob_start();
        ?>
        <div class="neve-testimonials-block layout-<?php echo esc_attr($layout); ?>">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-item">
                    <div class="testimonial-content">
                        <?php echo wp_kses_post($testimonial['content']); ?>
                    </div>
                    <div class="testimonial-author">
                        <?php if (!empty($testimonial['image'])): ?>
                            <img src="<?php echo esc_url($testimonial['image']); ?>" alt="<?php echo esc_attr($testimonial['name']); ?>">
                        <?php endif; ?>
                        <div class="author-info">
                            <h4><?php echo esc_html($testimonial['name']); ?></h4>
                            <?php if (!empty($testimonial['position'])): ?>
                                <span><?php echo esc_html($testimonial['position']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render pricing block
     */
    public function render_pricing_block($attributes) {
        $plans = isset($attributes['plans']) ? $attributes['plans'] : array();
        $columns = isset($attributes['columns']) ? $attributes['columns'] : 3;
        
        ob_start();
        ?>
        <div class="neve-pricing-block columns-<?php echo esc_attr($columns); ?>">
            <?php foreach ($plans as $plan): ?>
                <div class="pricing-plan <?php echo !empty($plan['featured']) ? 'featured' : ''; ?>">
                    <div class="plan-header">
                        <h3><?php echo esc_html($plan['title']); ?></h3>
                        <div class="plan-price">
                            <span class="currency"><?php echo esc_html($plan['currency']); ?></span>
                            <span class="amount"><?php echo esc_html($plan['price']); ?></span>
                            <span class="period"><?php echo esc_html($plan['period']); ?></span>
                        </div>
                    </div>
                    <div class="plan-features">
                        <?php foreach ($plan['features'] as $feature): ?>
                            <div class="feature"><?php echo wp_kses_post($feature); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="plan-footer">
                        <a href="<?php echo esc_url($plan['button_url']); ?>" class="plan-button">
                            <?php echo esc_html($plan['button_text']); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render team block
     */
    public function render_team_block($attributes) {
        $members = isset($attributes['members']) ? $attributes['members'] : array();
        $columns = isset($attributes['columns']) ? $attributes['columns'] : 3;
        
        ob_start();
        ?>
        <div class="neve-team-block columns-<?php echo esc_attr($columns); ?>">
            <?php foreach ($members as $member): ?>
                <div class="team-member">
                    <?php if (!empty($member['image'])): ?>
                        <div class="member-image">
                            <img src="<?php echo esc_url($member['image']); ?>" alt="<?php echo esc_attr($member['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="member-info">
                        <h4><?php echo esc_html($member['name']); ?></h4>
                        <?php if (!empty($member['position'])): ?>
                            <span class="position"><?php echo esc_html($member['position']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($member['bio'])): ?>
                            <p class="bio"><?php echo wp_kses_post($member['bio']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($member['social'])): ?>
                            <div class="social-links">
                                <?php foreach ($member['social'] as $social): ?>
                                    <a href="<?php echo esc_url($social['url']); ?>" target="_blank">
                                        <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Add premium customizer options
     */
    private function add_premium_customizer_options() {
        add_action('customize_register', array($this, 'add_premium_controls'));
    }

    /**
     * Add premium controls to customizer
     */
    public function add_premium_controls($wp_customize) {
        // Scroll to top section
        $wp_customize->add_section('neve_scroll_to_top', array(
            'title' => __('Scroll to Top', 'neve'),
            'panel' => 'neve_layout',
            'priority' => 80
        ));

        // Enable scroll to top
        $wp_customize->add_setting('neve_scroll_to_top_enable', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean'
        ));

        $wp_customize->add_control('neve_scroll_to_top_enable', array(
            'label' => __('Enable Scroll to Top', 'neve'),
            'section' => 'neve_scroll_to_top',
            'type' => 'checkbox'
        ));

        // Scroll to top position
        $wp_customize->add_setting('neve_scroll_to_top_position', array(
            'default' => 'bottom-right',
            'sanitize_callback' => 'sanitize_text_field'
        ));

        $wp_customize->add_control('neve_scroll_to_top_position', array(
            'label' => __('Position', 'neve'),
            'section' => 'neve_scroll_to_top',
            'type' => 'select',
            'choices' => array(
                'bottom-right' => __('Bottom Right', 'neve'),
                'bottom-left' => __('Bottom Left', 'neve'),
                'bottom-center' => __('Bottom Center', 'neve')
            )
        ));

        // Custom CSS section
        $wp_customize->add_section('neve_custom_css', array(
            'title' => __('Custom CSS', 'neve'),
            'priority' => 200
        ));

        $wp_customize->add_setting('neve_custom_css_code', array(
            'default' => '',
            'sanitize_callback' => array($this, 'sanitize_css')
        ));

        $wp_customize->add_control('neve_custom_css_code', array(
            'label' => __('Additional CSS', 'neve'),
            'section' => 'neve_custom_css',
            'type' => 'textarea',
            'description' => __('Add your custom CSS code here.', 'neve')
        ));

        // Custom JS section
        $wp_customize->add_section('neve_custom_js', array(
            'title' => __('Custom JavaScript', 'neve'),
            'priority' => 201
        ));

        $wp_customize->add_setting('neve_custom_js_code', array(
            'default' => '',
            'sanitize_callback' => array($this, 'sanitize_js')
        ));

        $wp_customize->add_control('neve_custom_js_code', array(
            'label' => __('Custom JavaScript', 'neve'),
            'section' => 'neve_custom_js',
            'type' => 'textarea',
            'description' => __('Add your custom JavaScript code here.', 'neve')
        ));
    }

    /**
     * Sanitize CSS
     */
    public function sanitize_css($css) {
        return wp_strip_all_tags($css);
    }

    /**
     * Sanitize JavaScript
     */
    public function sanitize_js($js) {
        return wp_strip_all_tags($js);
    }

    /**
     * Add premium header/footer components
     */
    private function add_premium_hfg_components() {
        add_filter('hfg_components_list', array($this, 'add_premium_components'));
    }

    /**
     * Add premium components to header/footer builder
     */
    public function add_premium_components($components) {
        $premium_components = array(
            'social_icons' => array(
                'name' => __('Social Icons', 'neve'),
                'description' => __('Display social media icons', 'neve')
            ),
            'contact_info' => array(
                'name' => __('Contact Info', 'neve'),
                'description' => __('Display contact information', 'neve')
            ),
            'language_switcher' => array(
                'name' => __('Language Switcher', 'neve'),
                'description' => __('WPML/Polylang language switcher', 'neve')
            ),
            'breadcrumbs' => array(
                'name' => __('Breadcrumbs', 'neve'),
                'description' => __('Navigation breadcrumbs', 'neve')
            )
        );

        return array_merge($components, $premium_components);
    }

    /**
     * Add premium WooCommerce features
     */
    private function add_premium_woocommerce_features() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Enable quick view
        add_action('woocommerce_after_shop_loop_item', array($this, 'add_quick_view_button'), 15);
        
        // Enable wishlist
        add_action('woocommerce_after_shop_loop_item', array($this, 'add_wishlist_button'), 20);
        
        // Enable compare
        add_action('woocommerce_after_shop_loop_item', array($this, 'add_compare_button'), 25);
        
        // Add product filters
        add_action('woocommerce_before_shop_loop', array($this, 'add_product_filters'), 5);
    }

    /**
     * Add quick view button
     */
    public function add_quick_view_button() {
        global $product;
        echo '<a href="#" class="neve-quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Quick View', 'neve') . '</a>';
    }

    /**
     * Add wishlist button
     */
    public function add_wishlist_button() {
        global $product;
        echo '<a href="#" class="neve-wishlist" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Add to Wishlist', 'neve') . '</a>';
    }

    /**
     * Add compare button
     */
    public function add_compare_button() {
        global $product;
        echo '<a href="#" class="neve-compare" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Compare', 'neve') . '</a>';
    }

    /**
     * Add product filters
     */
    public function add_product_filters() {
        if (get_theme_mod('neve_enable_product_filters', false)) {
            echo '<div class="neve-product-filters">';
            echo '<select class="neve-price-filter">';
            echo '<option value="">' . __('Filter by Price', 'neve') . '</option>';
            echo '<option value="0-50">' . __('$0 - $50', 'neve') . '</option>';
            echo '<option value="50-100">' . __('$50 - $100', 'neve') . '</option>';
            echo '<option value="100+">' . __('$100+', 'neve') . '</option>';
            echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Add custom code features
     */
    private function add_custom_code_features() {
        add_action('wp_head', array($this, 'output_custom_css'));
        add_action('wp_footer', array($this, 'output_custom_js'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scroll_to_top'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_premium_styles'));
    }
    
    /**
     * Enqueue premium styles
     */
    public function enqueue_premium_styles() {
        wp_enqueue_style(
            'neve-premium-blocks',
            get_template_directory_uri() . '/assets/css/premium-blocks.css',
            array('neve-style'),
            NEVE_VERSION
        );
    }

    /**
     * Output custom CSS
     */
    public function output_custom_css() {
        $custom_css = get_theme_mod('neve_custom_css_code', '');
        if (!empty($custom_css)) {
            echo '<style type="text/css">' . wp_strip_all_tags($custom_css) . '</style>';
        }
    }

    /**
     * Output custom JavaScript
     */
    public function output_custom_js() {
        $custom_js = get_theme_mod('neve_custom_js_code', '');
        if (!empty($custom_js)) {
            echo '<script type="text/javascript">' . wp_strip_all_tags($custom_js) . '</script>';
        }
    }

    /**
     * Enqueue scroll to top functionality
     */
    public function enqueue_scroll_to_top() {
        if (get_theme_mod('neve_scroll_to_top_enable', false)) {
            wp_add_inline_script('jquery', $this->get_scroll_to_top_js());
            wp_add_inline_style('neve-style', $this->get_scroll_to_top_css());
            add_action('wp_footer', array($this, 'output_scroll_to_top_button'));
        }
    }

    /**
     * Get scroll to top JavaScript
     */
    private function get_scroll_to_top_js() {
        return "
        jQuery(document).ready(function($) {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('.neve-scroll-to-top').fadeIn();
                } else {
                    $('.neve-scroll-to-top').fadeOut();
                }
            });
            
            $('.neve-scroll-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
        });
        ";
    }

    /**
     * Get scroll to top CSS
     */
    private function get_scroll_to_top_css() {
        $position = get_theme_mod('neve_scroll_to_top_position', 'bottom-right');
        $css_position = '';
        
        switch ($position) {
            case 'bottom-left':
                $css_position = 'bottom: 20px; left: 20px;';
                break;
            case 'bottom-center':
                $css_position = 'bottom: 20px; left: 50%; transform: translateX(-50%);';
                break;
            default:
                $css_position = 'bottom: 20px; right: 20px;';
        }
        
        return "
        .neve-scroll-to-top {
            position: fixed;
            {$css_position}
            width: 50px;
            height: 50px;
            background: #0073aa;
            color: white;
            text-align: center;
            line-height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            z-index: 9999;
            transition: all 0.3s ease;
        }
        .neve-scroll-to-top:hover {
            background: #005a87;
            transform: translateY(-2px);
        }
        ";
    }

    /**
     * Output scroll to top button
     */
    public function output_scroll_to_top_button() {
        echo '<div class="neve-scroll-to-top">↑</div>';
    }

    /**
     * Add performance features
     */
    private function add_performance_features() {
        // Enable lazy loading for images
        add_filter('wp_lazy_loading_enabled', '__return_true');
        
        // Optimize CSS delivery
        add_action('wp_enqueue_scripts', array($this, 'optimize_css_delivery'), 999);
        
        // Add preload for critical resources
        add_action('wp_head', array($this, 'add_resource_preloads'), 1);
    }

    /**
     * Optimize CSS delivery
     */
    public function optimize_css_delivery() {
        if (get_theme_mod('neve_optimize_css', false)) {
            // Defer non-critical CSS
            add_filter('style_loader_tag', array($this, 'defer_non_critical_css'), 10, 2);
        }
    }

    /**
     * Defer non-critical CSS
     */
    public function defer_non_critical_css($html, $handle) {
        $defer_handles = array('dashicons', 'admin-bar');
        
        if (in_array($handle, $defer_handles)) {
            $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        }
        
        return $html;
    }

    /**
     * Add resource preloads
     */
    public function add_resource_preloads() {
        // Preload critical fonts
        $fonts = get_theme_mod('neve_preload_fonts', array());
        foreach ($fonts as $font) {
            echo '<link rel="preload" href="' . esc_url($font) . '" as="font" type="font/woff2" crossorigin>';
        }
        
        // Preload hero image
        $hero_image = get_theme_mod('neve_hero_image', '');
        if (!empty($hero_image)) {
            echo '<link rel="preload" href="' . esc_url($hero_image) . '" as="image">';
        }
    }
}

// Initialize premium features
$neve_premium = new Premium_Features();
$neve_premium->init();

// Load premium activation
require_once get_template_directory() . '/inc/premium-activation.php';