<?php
/**
 * Premium Features Activation Script
 * 
 * @package Neve\Premium
 */

namespace Neve\Premium;

/**
 * Class Premium_Activation
 * 
 * Handles activation and setup of premium features
 */
class Premium_Activation {

    /**
     * Initialize activation
     */
    public function init() {
        add_action('after_setup_theme', array($this, 'setup_premium_features'));
        add_action('wp_loaded', array($this, 'check_premium_status'));
        add_action('admin_notices', array($this, 'show_premium_notice'));
    }

    /**
     * Setup premium features
     */
    public function setup_premium_features() {
        // Add theme support for premium features
        add_theme_support('neve-premium-features');
        add_theme_support('woocommerce-premium');
        add_theme_support('custom-blocks');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        
        // Set premium status
        update_option('neve_premium_active', true);
        update_option('neve_premium_version', NEVE_VERSION);
        
        // Flush rewrite rules if needed
        if (get_option('neve_premium_flush_rules')) {
            flush_rewrite_rules();
            delete_option('neve_premium_flush_rules');
        }
    }

    /**
     * Check premium status
     */
    public function check_premium_status() {
        // Verify premium features are active
        if (!get_option('neve_premium_active')) {
            update_option('neve_premium_active', true);
        }

        // Check for required files
        $required_files = array(
            get_template_directory() . '/inc/premium-features.php',
            get_template_directory() . '/inc/premium-blocks-category.php',
            get_template_directory() . '/inc/customizer/options/premium-panel.php',
            get_template_directory() . '/assets/css/premium-blocks.css'
        );

        foreach ($required_files as $file) {
            if (!file_exists($file)) {
                add_action('admin_notices', function() use ($file) {
                    echo '<div class="notice notice-error"><p>';
                    echo sprintf(__('Premium feature file missing: %s', 'neve'), basename($file));
                    echo '</p></div>';
                });
            }
        }
    }

    /**
     * Show premium activation notice
     */
    public function show_premium_notice() {
        if (!get_option('neve_premium_notice_dismissed') && current_user_can('manage_options')) {
            ?>
            <div class="notice notice-success is-dismissible" id="neve-premium-notice">
                <h3><?php _e('🎉 Neve Premium Features Activated!', 'neve'); ?></h3>
                <p><?php _e('All premium features have been successfully enabled. You now have access to:', 'neve'); ?></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php _e('Premium WooCommerce layouts and features', 'neve'); ?></li>
                    <li><?php _e('Advanced header/footer builder components', 'neve'); ?></li>
                    <li><?php _e('Premium blocks: Testimonials, Pricing Tables, Team Members', 'neve'); ?></li>
                    <li><?php _e('Performance optimization tools', 'neve'); ?></li>
                    <li><?php _e('Custom CSS and JavaScript options', 'neve'); ?></li>
                    <li><?php _e('Scroll to top functionality', 'neve'); ?></li>
                </ul>
                <p>
                    <a href="<?php echo admin_url('customize.php?autofocus[panel]=neve_premium_features'); ?>" class="button button-primary">
                        <?php _e('Explore Premium Features', 'neve'); ?>
                    </a>
                    <button type="button" class="notice-dismiss" onclick="neveDissmissPremiumNotice()">
                        <span class="screen-reader-text"><?php _e('Dismiss this notice.', 'neve'); ?></span>
                    </button>
                </p>
            </div>
            <script>
            function neveDissmissPremiumNotice() {
                document.getElementById('neve-premium-notice').style.display = 'none';
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=neve_dismiss_premium_notice&nonce=<?php echo wp_create_nonce('neve_premium_nonce'); ?>'
                });
            }
            </script>
            <?php
        }
    }

    /**
     * Handle notice dismissal
     */
    public function dismiss_premium_notice() {
        if (wp_verify_nonce($_POST['nonce'], 'neve_premium_nonce')) {
            update_option('neve_premium_notice_dismissed', true);
        }
        wp_die();
    }
}

// Handle AJAX notice dismissal
add_action('wp_ajax_neve_dismiss_premium_notice', function() {
    if (wp_verify_nonce($_POST['nonce'], 'neve_premium_nonce')) {
        update_option('neve_premium_notice_dismissed', true);
    }
    wp_die();
});

// Initialize activation
$neve_premium_activation = new Premium_Activation();
$neve_premium_activation->init();