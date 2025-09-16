<?php
/**
 * Nueve4 Master Addons Integration
 * 
 * @package Nueve4\MasterAddons
 */

namespace Nueve4\MasterAddons;

class Nueve4_Master_Addons {

    private static $instance = null;
    private $activated_widgets = array();
    private $activated_extensions = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        if (!defined('NUEVE4_MASTER_ADDONS_VERSION')) {
            define('NUEVE4_MASTER_ADDONS_VERSION', '2.0.7.6');
        }

        // Enable all pro features
        add_filter('nueve4_master_addons_pro_active', '__return_true');
        add_filter('nueve4_master_addons_premium_features', '__return_true');

        // Initialize components
        $this->load_dependencies();
        $this->register_elementor_widgets();
        $this->register_extensions();
        $this->add_admin_menu();
        $this->enqueue_assets();
    }

    private function load_dependencies() {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'elementor_missing_notice'));
            return;
        }

        // Add Elementor category
        add_action('elementor/elements/categories_registered', array($this, 'add_elementor_category'));
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
    }

    public function add_elementor_category($elements_manager) {
        $elements_manager->add_category('nueve4-addons', array(
            'title' => __('Nueve4 Master Addons', 'nueve4'),
            'icon' => 'fa fa-plug',
        ));
    }

    public function register_widgets() {
        $widgets = $this->get_available_widgets();
        
        foreach ($widgets as $widget_key => $widget_data) {
            if ($this->is_widget_active($widget_key)) {
                $this->register_single_widget($widget_data);
            }
        }
    }

    private function get_available_widgets() {
        return array(
            'accordion' => array(
                'title' => __('Accordion', 'nueve4'),
                'class' => 'Nueve4_Accordion_Widget',
                'icon' => 'eicon-accordion',
                'is_pro' => false
            ),
            'advanced-image' => array(
                'title' => __('Advanced Image', 'nueve4'),
                'class' => 'Nueve4_Advanced_Image_Widget',
                'icon' => 'eicon-image',
                'is_pro' => false
            ),
            'animated-headlines' => array(
                'title' => __('Animated Headlines', 'nueve4'),
                'class' => 'Nueve4_Animated_Headlines_Widget',
                'icon' => 'eicon-animated-headline',
                'is_pro' => false
            ),
            'blockquote' => array(
                'title' => __('Blockquote', 'nueve4'),
                'class' => 'Nueve4_Blockquote_Widget',
                'icon' => 'eicon-blockquote',
                'is_pro' => false
            ),
            'blog' => array(
                'title' => __('Blog Posts', 'nueve4'),
                'class' => 'Nueve4_Blog_Widget',
                'icon' => 'eicon-posts-grid',
                'is_pro' => false
            ),
            'business-hours' => array(
                'title' => __('Business Hours', 'nueve4'),
                'class' => 'Nueve4_Business_Hours_Widget',
                'icon' => 'eicon-clock',
                'is_pro' => false
            ),
            'call-to-action' => array(
                'title' => __('Call to Action', 'nueve4'),
                'class' => 'Nueve4_CTA_Widget',
                'icon' => 'eicon-call-to-action',
                'is_pro' => false
            ),
            'cards' => array(
                'title' => __('Cards', 'nueve4'),
                'class' => 'Nueve4_Cards_Widget',
                'icon' => 'eicon-posts-carousel',
                'is_pro' => false
            ),
            'countdown-timer' => array(
                'title' => __('Countdown Timer', 'nueve4'),
                'class' => 'Nueve4_Countdown_Widget',
                'icon' => 'eicon-countdown',
                'is_pro' => false
            ),
            'counter-up' => array(
                'title' => __('Counter Up', 'nueve4'),
                'class' => 'Nueve4_Counter_Widget',
                'icon' => 'eicon-counter',
                'is_pro' => false
            ),
            'creative-buttons' => array(
                'title' => __('Creative Buttons', 'nueve4'),
                'class' => 'Nueve4_Creative_Buttons_Widget',
                'icon' => 'eicon-button',
                'is_pro' => false
            ),
            'dual-heading' => array(
                'title' => __('Dual Heading', 'nueve4'),
                'class' => 'Nueve4_Dual_Heading_Widget',
                'icon' => 'eicon-heading',
                'is_pro' => false
            ),
            'flipbox' => array(
                'title' => __('Flip Box', 'nueve4'),
                'class' => 'Nueve4_Flipbox_Widget',
                'icon' => 'eicon-flip-box',
                'is_pro' => false
            ),
            'gradient-headline' => array(
                'title' => __('Gradient Headline', 'nueve4'),
                'class' => 'Nueve4_Gradient_Headline_Widget',
                'icon' => 'eicon-t-letter',
                'is_pro' => false
            ),
            'image-carousel' => array(
                'title' => __('Image Carousel', 'nueve4'),
                'class' => 'Nueve4_Image_Carousel_Widget',
                'icon' => 'eicon-media-carousel',
                'is_pro' => false
            ),
            'image-comparison' => array(
                'title' => __('Image Comparison', 'nueve4'),
                'class' => 'Nueve4_Image_Comparison_Widget',
                'icon' => 'eicon-image-before-after',
                'is_pro' => true
            ),
            'image-filter-gallery' => array(
                'title' => __('Image Filter Gallery', 'nueve4'),
                'class' => 'Nueve4_Image_Filter_Gallery_Widget',
                'icon' => 'eicon-gallery-grid',
                'is_pro' => true
            ),
            'infobox' => array(
                'title' => __('Info Box', 'nueve4'),
                'class' => 'Nueve4_Infobox_Widget',
                'icon' => 'eicon-info-box',
                'is_pro' => false
            ),
            'logo-slider' => array(
                'title' => __('Logo Slider', 'nueve4'),
                'class' => 'Nueve4_Logo_Slider_Widget',
                'icon' => 'eicon-slider-push',
                'is_pro' => false
            ),
            'pricing-table' => array(
                'title' => __('Pricing Table', 'nueve4'),
                'class' => 'Nueve4_Pricing_Table_Widget',
                'icon' => 'eicon-price-table',
                'is_pro' => false
            ),
            'progressbar' => array(
                'title' => __('Progress Bar', 'nueve4'),
                'class' => 'Nueve4_Progress_Bar_Widget',
                'icon' => 'eicon-skill-bar',
                'is_pro' => false
            ),
            'table' => array(
                'title' => __('Table', 'nueve4'),
                'class' => 'Nueve4_Table_Widget',
                'icon' => 'eicon-table',
                'is_pro' => false
            ),
            'tabs' => array(
                'title' => __('Tabs', 'nueve4'),
                'class' => 'Nueve4_Tabs_Widget',
                'icon' => 'eicon-tabs',
                'is_pro' => false
            ),
            'team-members' => array(
                'title' => __('Team Members', 'nueve4'),
                'class' => 'Nueve4_Team_Members_Widget',
                'icon' => 'eicon-person',
                'is_pro' => false
            ),
            'testimonials' => array(
                'title' => __('Testimonials', 'nueve4'),
                'class' => 'Nueve4_Testimonials_Widget',
                'icon' => 'eicon-testimonial',
                'is_pro' => false
            ),
            'timeline' => array(
                'title' => __('Timeline', 'nueve4'),
                'class' => 'Nueve4_Timeline_Widget',
                'icon' => 'eicon-time-line',
                'is_pro' => true
            ),
            'tooltip' => array(
                'title' => __('Tooltip', 'nueve4'),
                'class' => 'Nueve4_Tooltip_Widget',
                'icon' => 'eicon-help-o',
                'is_pro' => false
            )
        );
    }

    private function register_single_widget($widget_data) {
        // Create widget class dynamically
        $widget_class = $this->create_widget_class($widget_data);
        
        if (class_exists($widget_class)) {
            \Elementor\Plugin::instance()->widgets_manager->register(new $widget_class());
        }
    }

    private function create_widget_class($widget_data) {
        $class_name = $widget_data['class'];
        
        if (!class_exists($class_name)) {
            eval("
            class {$class_name} extends \\Elementor\\Widget_Base {
                public function get_name() {
                    return '" . sanitize_key($widget_data['title']) . "';
                }
                
                public function get_title() {
                    return '" . esc_html($widget_data['title']) . "';
                }
                
                public function get_icon() {
                    return '" . esc_attr($widget_data['icon']) . "';
                }
                
                public function get_categories() {
                    return ['nueve4-addons'];
                }
                
                protected function register_controls() {
                    \$this->start_controls_section(
                        'content_section',
                        [
                            'label' => __('Content', 'nueve4'),
                            'tab' => \\Elementor\\Controls_Manager::TAB_CONTENT,
                        ]
                    );
                    
                    \$this->add_control(
                        'title',
                        [
                            'label' => __('Title', 'nueve4'),
                            'type' => \\Elementor\\Controls_Manager::TEXT,
                            'default' => '" . esc_html($widget_data['title']) . "',
                        ]
                    );
                    
                    \$this->end_controls_section();
                }
                
                protected function render() {
                    \$settings = \$this->get_settings_for_display();
                    echo '<div class=\"nueve4-widget-' . sanitize_key($widget_data['title']) . '\">';
                    echo '<h3>' . esc_html(\$settings['title']) . '</h3>';
                    echo '<p>This is the ' . esc_html($widget_data['title']) . ' widget. Full implementation coming soon!</p>';
                    echo '</div>';
                }
            }
            ");
        }
        
        return $class_name;
    }

    private function is_widget_active($widget_key) {
        $active_widgets = get_option('nueve4_master_addons_widgets', array());
        return isset($active_widgets[$widget_key]) ? $active_widgets[$widget_key] : true;
    }

    private function register_extensions() {
        $extensions = array(
            'parallax-effects' => array(
                'title' => __('Parallax Effects', 'nueve4'),
                'description' => __('Add parallax scrolling effects to elements', 'nueve4'),
                'is_pro' => false
            ),
            'particles-effects' => array(
                'title' => __('Particles Effects', 'nueve4'),
                'description' => __('Add animated particle backgrounds', 'nueve4'),
                'is_pro' => true
            ),
            'reading-progress' => array(
                'title' => __('Reading Progress', 'nueve4'),
                'description' => __('Show reading progress indicator', 'nueve4'),
                'is_pro' => false
            ),
            'sticky-elements' => array(
                'title' => __('Sticky Elements', 'nueve4'),
                'description' => __('Make elements stick on scroll', 'nueve4'),
                'is_pro' => false
            ),
            'custom-css' => array(
                'title' => __('Custom CSS', 'nueve4'),
                'description' => __('Add custom CSS to elements', 'nueve4'),
                'is_pro' => false
            )
        );

        foreach ($extensions as $key => $extension) {
            if ($this->is_extension_active($key)) {
                $this->load_extension($key, $extension);
            }
        }
    }

    private function is_extension_active($extension_key) {
        $active_extensions = get_option('nueve4_master_addons_extensions', array());
        return isset($active_extensions[$extension_key]) ? $active_extensions[$extension_key] : true;
    }

    private function load_extension($key, $extension) {
        // Load extension functionality
        switch ($key) {
            case 'parallax-effects':
                add_action('wp_enqueue_scripts', array($this, 'enqueue_parallax_scripts'));
                break;
            case 'reading-progress':
                add_action('wp_footer', array($this, 'add_reading_progress'));
                break;
            case 'sticky-elements':
                add_action('wp_enqueue_scripts', array($this, 'enqueue_sticky_scripts'));
                break;
        }
    }

    public function enqueue_parallax_scripts() {
        wp_enqueue_script('nueve4-parallax', get_template_directory_uri() . '/assets/js/parallax.js', array('jquery'), NUEVE4_VERSION, true);
    }

    public function enqueue_sticky_scripts() {
        wp_enqueue_script('nueve4-sticky', get_template_directory_uri() . '/assets/js/sticky.js', array('jquery'), NUEVE4_VERSION, true);
    }

    public function add_reading_progress() {
        echo '<div id="nueve4-reading-progress"><div class="progress-bar"></div></div>';
        echo '<script>
        jQuery(document).ready(function($) {
            $(window).scroll(function() {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = (scroll / height) * 100;
                $("#nueve4-reading-progress .progress-bar").css("width", progress + "%");
            });
        });
        </script>';
        echo '<style>
        #nueve4-reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(0,0,0,0.1);
            z-index: 9999;
        }
        #nueve4-reading-progress .progress-bar {
            height: 100%;
            background: #0073aa;
            width: 0%;
            transition: width 0.3s ease;
        }
        </style>';
    }

    private function add_admin_menu() {
        add_action('admin_menu', array($this, 'create_admin_menu'));
    }

    public function create_admin_menu() {
        add_menu_page(
            __('Nueve4 Master Addons', 'nueve4'),
            __('Nueve4 Addons', 'nueve4'),
            'manage_options',
            'nueve4-master-addons',
            array($this, 'admin_page_content'),
            'dashicons-admin-plugins',
            58
        );
    }

    public function admin_page_content() {
        ?>
        <div class="wrap">
            <h1><?php _e('Nueve4 Master Addons', 'nueve4'); ?></h1>
            
            <div class="nueve4-admin-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#widgets" class="nav-tab nav-tab-active"><?php _e('Widgets', 'nueve4'); ?></a>
                    <a href="#extensions" class="nav-tab"><?php _e('Extensions', 'nueve4'); ?></a>
                    <a href="#settings" class="nav-tab"><?php _e('Settings', 'nueve4'); ?></a>
                </nav>
                
                <div id="widgets" class="tab-content active">
                    <h2><?php _e('Available Widgets', 'nueve4'); ?></h2>
                    <div class="widgets-grid">
                        <?php foreach ($this->get_available_widgets() as $key => $widget): ?>
                        <div class="widget-card">
                            <div class="widget-icon">
                                <i class="<?php echo esc_attr($widget['icon']); ?>"></i>
                            </div>
                            <h3><?php echo esc_html($widget['title']); ?></h3>
                            <label class="switch">
                                <input type="checkbox" name="widgets[<?php echo esc_attr($key); ?>]" <?php checked($this->is_widget_active($key)); ?>>
                                <span class="slider"></span>
                            </label>
                            <?php if ($widget['is_pro']): ?>
                            <span class="pro-badge"><?php _e('PRO', 'nueve4'); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div id="extensions" class="tab-content">
                    <h2><?php _e('Extensions', 'nueve4'); ?></h2>
                    <p><?php _e('Enhance your Elementor experience with these powerful extensions.', 'nueve4'); ?></p>
                </div>
                
                <div id="settings" class="tab-content">
                    <h2><?php _e('Settings', 'nueve4'); ?></h2>
                    <p><?php _e('Configure your Nueve4 Master Addons settings.', 'nueve4'); ?></p>
                </div>
            </div>
        </div>
        
        <style>
        .nueve4-admin-tabs .nav-tab-wrapper {
            border-bottom: 1px solid #ccd0d4;
            margin-bottom: 20px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .widgets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .widget-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            position: relative;
        }
        .widget-icon {
            font-size: 48px;
            color: #0073aa;
            margin-bottom: 10px;
        }
        .pro-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b35;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin-top: 10px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #0073aa;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        });
        </script>
        <?php
    }

    private function enqueue_assets() {
        add_action('wp_enqueue_scripts', array($this, 'frontend_assets'));
        add_action('elementor/editor/after_enqueue_scripts', array($this, 'editor_assets'));
    }

    public function frontend_assets() {
        wp_enqueue_style('nueve4-master-addons', get_template_directory_uri() . '/assets/css/nueve4-master-addons.css', array(), NUEVE4_VERSION);
        wp_enqueue_script('nueve4-master-addons', get_template_directory_uri() . '/assets/js/nueve4-master-addons.js', array('jquery'), NUEVE4_VERSION, true);
    }

    public function editor_assets() {
        wp_enqueue_style('nueve4-master-addons-editor', get_template_directory_uri() . '/assets/css/nueve4-master-addons-editor.css', array(), NUEVE4_VERSION);
    }

    public function elementor_missing_notice() {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>' . __('Nueve4 Master Addons requires Elementor to be installed and activated.', 'nueve4') . '</p>';
        echo '</div>';
    }
}

// Initialize the integration
add_action('init', function() {
    Nueve4_Master_Addons::get_instance()->init();
});