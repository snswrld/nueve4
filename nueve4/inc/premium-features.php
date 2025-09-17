<?php
/**
 * Premium Features Enabler for Nueve4 Theme
 * 
 * @package Nueve4\Premium
 */

namespace Nueve4\Premium;

class Premium_Features {

    private static $instance = null;
    private $loaded_files = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        if (!defined('NUEVE4_PRO_VERSION')) {
            define('NUEVE4_PRO_VERSION', '2.8.0');
        }
        
        if (!defined('NUEVE4_PRO_BASEFILE')) {
            define('NUEVE4_PRO_BASEFILE', __FILE__);
        }

        add_filter('nueve4_has_valid_addons', '__return_true');
        add_filter('nueve4_pro_addon_is_active', '__return_true');
        add_filter('nueve4_upgrade_link_from_child_theme_filter', array($this, 'remove_upgrade_links'));
        
        $this->enable_premium_layouts();
        $this->add_premium_blocks();
        $this->add_premium_customizer_options();
        $this->load_premium_files();
        $this->add_premium_hfg_components();
        $this->add_premium_woocommerce_features();
        $this->add_custom_code_features();
        $this->add_performance_features();
        $this->load_master_addons_integration();
    }
    
    /**
     * Load Master Addons Integration
     */
    private function load_master_addons_integration() {
        $this->safe_require('inc/nueve4-master-addons.php');
    }

    public function remove_upgrade_links($url) {
        return '#';
    }

    private function enable_premium_layouts() {
        $filters = array(
            'theme_mod_nueve4_checkout_page_layout' => 'standard',
            'theme_mod_nueve4_product_card_layout' => 'grid',
            'theme_mod_nueve4_category_card_layout' => 'default',
            'theme_mod_nueve4_product_content_alignment' => 'left',
            'theme_mod_nueve4_sale_tag_position' => 'inside',
            'theme_mod_nueve4_add_to_cart_display' => 'none'
        );

        foreach ($filters as $filter => $default) {
            add_filter($filter, function($value) use ($default, $filter) {
                $key = str_replace('theme_mod_', '', $filter);
                return $value ?: get_theme_mod($key, $default);
            });
        }
    }

    private function load_premium_files() {
        $files = array(
            'inc/premium-blocks-category.php',
            'inc/customizer/options/premium-panel.php'
        );

        foreach ($files as $file) {
            $this->safe_require($file);
        }

        if (class_exists('\Nueve4\Customizer\Options\Premium_Panel')) {
            $panel = new \Nueve4\Customizer\Options\Premium_Panel();
            $panel->init();
        }
    }

    private function safe_require($relative_path) {
        if (isset($this->loaded_files[$relative_path])) {
            return;
        }

        $file = get_template_directory() . '/' . ltrim($relative_path, '/');
        if (file_exists($file) && is_readable($file)) {
            $real_path = realpath($file);
            $theme_root = realpath(get_template_directory());
            
            if ($real_path && $theme_root && strpos($real_path, $theme_root) === 0) {
                require_once $real_path;
                $this->loaded_files[$relative_path] = true;
            }
        }
    }

    private function add_premium_blocks() {
        add_action('init', array($this, 'register_premium_blocks'));
    }

    public function register_premium_blocks() {
        $blocks = array(
            'testimonials' => 'render_testimonials_block',
            'pricing-table' => 'render_pricing_block',
            'team-members' => 'render_team_block'
        );

        foreach ($blocks as $block => $callback) {
            wp_register_script(
                "nueve4-{$block}-block",
                get_template_directory_uri() . "/assets/js/premium-blocks/{$block}.js",
                array('wp-blocks', 'wp-element', 'wp-editor'),
                defined('NUEVE4_VERSION') ? NUEVE4_VERSION : '1.0.0'
            );

            register_block_type("nueve4/{$block}", array(
                'editor_script' => "nueve4-{$block}-block",
                'render_callback' => array($this, $callback)
            ));
        }
    }

    public function render_testimonials_block($attributes) {
        $testimonials = isset($attributes['testimonials']) && is_array($attributes['testimonials']) ? $attributes['testimonials'] : array();
        $layout = isset($attributes['layout']) ? sanitize_text_field($attributes['layout']) : 'grid';
        
        if (empty($testimonials)) {
            return '';
        }

        ob_start();
        echo '<div class="nueve4-testimonials-block layout-' . esc_attr($layout) . '">';
        foreach ($testimonials as $testimonial) {
            if (!is_array($testimonial)) continue;
            
            echo '<div class="testimonial-item">';
            echo '<div class="testimonial-content">' . wp_kses_post($testimonial['content'] ?? '') . '</div>';
            echo '<div class="testimonial-author">';
            
            if (!empty($testimonial['image'])) {
                echo '<img src="' . esc_url($testimonial['image']) . '" alt="' . esc_attr($testimonial['name'] ?? '') . '">';
            }
            
            echo '<div class="author-info">';
            echo '<h4>' . esc_html($testimonial['name'] ?? '') . '</h4>';
            if (!empty($testimonial['position'])) {
                echo '<span>' . esc_html($testimonial['position']) . '</span>';
            }
            echo '</div></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function render_pricing_block($attributes) {
        $plans = isset($attributes['plans']) && is_array($attributes['plans']) ? $attributes['plans'] : array();
        $columns = isset($attributes['columns']) ? absint($attributes['columns']) : 3;
        
        if (empty($plans)) {
            return '';
        }

        ob_start();
        echo '<div class="nueve4-pricing-block columns-' . esc_attr($columns) . '">';
        foreach ($plans as $plan) {
            if (!is_array($plan)) continue;
            
            $featured = !empty($plan['featured']) ? ' featured' : '';
            echo '<div class="pricing-plan' . $featured . '">';
            echo '<div class="plan-header">';
            echo '<h3>' . esc_html($plan['title'] ?? '') . '</h3>';
            echo '<div class="plan-price">';
            echo '<span class="currency">' . esc_html($plan['currency'] ?? '') . '</span>';
            echo '<span class="amount">' . esc_html($plan['price'] ?? '') . '</span>';
            echo '<span class="period">' . esc_html($plan['period'] ?? '') . '</span>';
            echo '</div></div>';
            
            if (!empty($plan['features']) && is_array($plan['features'])) {
                echo '<div class="plan-features">';
                foreach ($plan['features'] as $feature) {
                    echo '<div class="feature">' . wp_kses_post($feature) . '</div>';
                }
                echo '</div>';
            }
            
            echo '<div class="plan-footer">';
            echo '<a href="' . esc_url($plan['button_url'] ?? '#') . '" class="plan-button">';
            echo esc_html($plan['button_text'] ?? '');
            echo '</a></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function render_team_block($attributes) {
        $members = isset($attributes['members']) && is_array($attributes['members']) ? $attributes['members'] : array();
        $columns = isset($attributes['columns']) ? absint($attributes['columns']) : 3;
        
        if (empty($members)) {
            return '';
        }

        ob_start();
        echo '<div class="nueve4-team-block columns-' . esc_attr($columns) . '">';
        foreach ($members as $member) {
            if (!is_array($member)) continue;
            
            echo '<div class="team-member">';
            if (!empty($member['image'])) {
                echo '<div class="member-image">';
                echo '<img src="' . esc_url($member['image']) . '" alt="' . esc_attr($member['name'] ?? '') . '">';
                echo '</div>';
            }
            
            echo '<div class="member-info">';
            echo '<h4>' . esc_html($member['name'] ?? '') . '</h4>';
            if (!empty($member['position'])) {
                echo '<span class="position">' . esc_html($member['position']) . '</span>';
            }
            if (!empty($member['bio'])) {
                echo '<p class="bio">' . wp_kses_post($member['bio']) . '</p>';
            }
            
            if (!empty($member['social']) && is_array($member['social'])) {
                echo '<div class="social-links">';
                foreach ($member['social'] as $social) {
                    if (isset($social['url'], $social['icon'])) {
                        echo '<a href="' . esc_url($social['url']) . '" target="_blank" rel="noopener">';
                        echo '<i class="' . esc_attr($social['icon']) . '"></i></a>';
                    }
                }
                echo '</div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    private function add_premium_customizer_options() {
        add_action('customize_register', array($this, 'add_premium_controls'));
    }

    public function add_premium_controls($wp_customize) {
        if (!is_a($wp_customize, 'WP_Customize_Manager')) {
            return;
        }

        $wp_customize->add_section('nueve4_scroll_to_top', array(
            'title' => __('Scroll to Top', 'nueve4'),
            'panel' => 'nueve4_layout',
            'priority' => 80
        ));

        $wp_customize->add_setting('nueve4_scroll_to_top_enable', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean'
        ));

        $wp_customize->add_control('nueve4_scroll_to_top_enable', array(
            'label' => __('Enable Scroll to Top', 'nueve4'),
            'section' => 'nueve4_scroll_to_top',
            'type' => 'checkbox'
        ));

        $wp_customize->add_setting('nueve4_scroll_to_top_position', array(
            'default' => 'bottom-right',
            'sanitize_callback' => 'sanitize_text_field'
        ));

        $wp_customize->add_control('nueve4_scroll_to_top_position', array(
            'label' => __('Position', 'nueve4'),
            'section' => 'nueve4_scroll_to_top',
            'type' => 'select',
            'choices' => array(
                'bottom-right' => __('Bottom Right', 'nueve4'),
                'bottom-left' => __('Bottom Left', 'nueve4'),
                'bottom-center' => __('Bottom Center', 'nueve4')
            )
        ));

        $wp_customize->add_section('nueve4_custom_css', array(
            'title' => __('Custom CSS', 'nueve4'),
            'priority' => 200
        ));

        $wp_customize->add_setting('nueve4_custom_css_code', array(
            'default' => '',
            'sanitize_callback' => array($this, 'sanitize_css')
        ));

        $wp_customize->add_control('nueve4_custom_css_code', array(
            'label' => __('Additional CSS', 'nueve4'),
            'section' => 'nueve4_custom_css',
            'type' => 'textarea'
        ));

        $wp_customize->add_section('nueve4_custom_js', array(
            'title' => __('Custom JavaScript', 'nueve4'),
            'priority' => 201
        ));

        $wp_customize->add_setting('nueve4_custom_js_code', array(
            'default' => '',
            'sanitize_callback' => array($this, 'sanitize_js')
        ));

        $wp_customize->add_control('nueve4_custom_js_code', array(
            'label' => __('Custom JavaScript', 'nueve4'),
            'section' => 'nueve4_custom_js',
            'type' => 'textarea'
        ));
    }

    public function sanitize_css($css) {
        if (!current_user_can('unfiltered_html')) {
            return '';
        }
        return wp_strip_all_tags($css);
    }

    public function sanitize_js($js) {
        if (!current_user_can('unfiltered_html')) {
            return '';
        }
        return wp_strip_all_tags($js);
    }

    private function add_premium_hfg_components() {
        add_filter('hfg_components_list', array($this, 'add_premium_components'));
    }

    public function add_premium_components($components) {
        if (!is_array($components)) {
            $components = array();
        }
        
        return array_merge($components, array(
            'social_icons' => array(
                'name' => __('Social Icons', 'nueve4'),
                'description' => __('Display social media icons', 'nueve4')
            ),
            'contact_info' => array(
                'name' => __('Contact Info', 'nueve4'),
                'description' => __('Display contact information', 'nueve4')
            ),
            'language_switcher' => array(
                'name' => __('Language Switcher', 'nueve4'),
                'description' => __('WPML/Polylang language switcher', 'nueve4')
            ),
            'breadcrumbs' => array(
                'name' => __('Breadcrumbs', 'nueve4'),
                'description' => __('Navigation breadcrumbs', 'nueve4')
            )
        ));
    }

    private function add_premium_woocommerce_features() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        add_action('woocommerce_after_shop_loop_item', array($this, 'add_quick_view_button'), 15);
        add_action('woocommerce_after_shop_loop_item', array($this, 'add_wishlist_button'), 20);
        add_action('woocommerce_after_shop_loop_item', array($this, 'add_compare_button'), 25);
        add_action('woocommerce_before_shop_loop', array($this, 'add_product_filters'), 5);
    }

    public function add_quick_view_button() {
        global $product;
        if ($product && is_a($product, 'WC_Product')) {
            echo '<a href="#" class="nueve4-quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Quick View', 'nueve4') . '</a>';
        }
    }

    public function add_wishlist_button() {
        global $product;
        if ($product && is_a($product, 'WC_Product')) {
            echo '<a href="#" class="nueve4-wishlist" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Add to Wishlist', 'nueve4') . '</a>';
        }
    }

    public function add_compare_button() {
        global $product;
        if ($product && is_a($product, 'WC_Product')) {
            echo '<a href="#" class="nueve4-compare" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Compare', 'nueve4') . '</a>';
        }
    }

    public function add_product_filters() {
        if (get_theme_mod('nueve4_enable_product_filters', false)) {
            echo '<div class="nueve4-product-filters">';
            echo '<select class="nueve4-price-filter">';
            echo '<option value="">' . __('Filter by Price', 'nueve4') . '</option>';
            echo '<option value="0-50">' . __('$0 - $50', 'nueve4') . '</option>';
            echo '<option value="50-100">' . __('$50 - $100', 'nueve4') . '</option>';
            echo '<option value="100+">' . __('$100+', 'nueve4') . '</option>';
            echo '</select></div>';
        }
    }

    private function add_custom_code_features() {
        add_action('wp_head', array($this, 'output_custom_css'));
        add_action('wp_footer', array($this, 'output_custom_js'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scroll_to_top'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_premium_styles'));
    }
    
    public function enqueue_premium_styles() {
        wp_enqueue_style(
            'nueve4-premium-blocks',
            get_template_directory_uri() . '/assets/css/premium-blocks.css',
            array('nueve4-style'),
            defined('NUEVE4_VERSION') ? NUEVE4_VERSION : '1.0.0'
        );
    }

    public function output_custom_css() {
        $custom_css = get_theme_mod('nueve4_custom_css_code', '');
        if (!empty($custom_css)) {
            echo '<style type="text/css">' . $this->sanitize_css($custom_css) . '</style>';
        }
    }

    public function output_custom_js() {
        if (!current_user_can('unfiltered_html')) {
            return;
        }
        $custom_js = get_theme_mod('nueve4_custom_js_code', '');
        if (!empty($custom_js)) {
            echo '<script type="text/javascript">' . $this->sanitize_js($custom_js) . '</script>';
        }
    }

    public function enqueue_scroll_to_top() {
        if (get_theme_mod('nueve4_scroll_to_top_enable', false)) {
            wp_add_inline_script('jquery', $this->get_scroll_to_top_js());
            wp_add_inline_style('nueve4-style', $this->get_scroll_to_top_css());
            add_action('wp_footer', array($this, 'output_scroll_to_top_button'));
        }
    }

    private function get_scroll_to_top_js() {
        return "jQuery(document).ready(function($){$(window).scroll(function(){if($(this).scrollTop()>100){$('.nueve4-scroll-to-top').fadeIn();}else{$('.nueve4-scroll-to-top').fadeOut();}});$('.nueve4-scroll-to-top').click(function(){$('html, body').animate({scrollTop:0},800);return false;});});";
    }

    private function get_scroll_to_top_css() {
        $position = get_theme_mod('nueve4_scroll_to_top_position', 'bottom-right');
        $css_position = 'bottom:20px;right:20px;';
        
        if ($position === 'bottom-left') {
            $css_position = 'bottom:20px;left:20px;';
        } elseif ($position === 'bottom-center') {
            $css_position = 'bottom:20px;left:50%;transform:translateX(-50%);';
        }
        
        return ".nueve4-scroll-to-top{position:fixed;{$css_position}width:50px;height:50px;background:#0073aa;color:white;text-align:center;line-height:50px;border-radius:50%;cursor:pointer;display:none;z-index:9999;transition:all 0.3s ease;}.nueve4-scroll-to-top:hover{background:#005a87;transform:translateY(-2px);}";
    }

    public function output_scroll_to_top_button() {
        echo '<div class="nueve4-scroll-to-top">↑</div>';
    }

    private function add_performance_features() {
        add_filter('wp_lazy_loading_enabled', '__return_true');
        add_action('wp_enqueue_scripts', array($this, 'optimize_css_delivery'), 999);
        add_action('wp_head', array($this, 'add_resource_preloads'), 1);
    }

    public function optimize_css_delivery() {
        if (get_theme_mod('nueve4_optimize_css', false)) {
            add_filter('style_loader_tag', array($this, 'defer_non_critical_css'), 10, 2);
        }
    }

    public function defer_non_critical_css($html, $handle) {
        if (in_array($handle, array('dashicons', 'admin-bar'))) {
            $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        }
        return $html;
    }

    public function add_resource_preloads() {
        $fonts = get_theme_mod('nueve4_preload_fonts', array());
        if (is_array($fonts)) {
            foreach ($fonts as $font) {
                echo '<link rel="preload" href="' . esc_url($font) . '" as="font" type="font/woff2" crossorigin>';
            }
        }
        
        $hero_image = get_theme_mod('nueve4_hero_image', '');
        if (!empty($hero_image)) {
            echo '<link rel="preload" href="' . esc_url($hero_image) . '" as="image">';
        }
    }
}

// Initialize premium features
$nueve4_premium = Premium_Features::get_instance();
$nueve4_premium->init();

// Load premium activation
$activation_file = get_template_directory() . '/inc/premium-activation.php';
if (file_exists($activation_file) && is_readable($activation_file)) {
    require_once $activation_file;
}