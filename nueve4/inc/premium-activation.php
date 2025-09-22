<?php
/**
 * Premium Features Activation Script
 * 
 * @package Nueve4\Premium
 */

namespace Nueve4\Premium;

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
        add_theme_support('nueve4-premium-features');
        add_theme_support('woocommerce-premium');
        add_theme_support('custom-blocks');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        
        // Set premium status
        update_option('nueve4_premium_active', true);
        update_option('nueve4_premium_version', defined('NUEVE4_VERSION') ? NUEVE4_VERSION : '1.0.0');
        
        // Flush rewrite rules if needed
        if (get_option('nueve4_premium_flush_rules')) {
            flush_rewrite_rules();
            delete_option('nueve4_premium_flush_rules');
        }
    }

    /**
     * Check premium status
     */
    public function check_premium_status() {
        // Verify premium features are active
        if (!get_option('nueve4_premium_active')) {
            update_option('nueve4_premium_active', true);
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
                    echo sprintf(__('Premium feature file missing: %s', 'nueve4'), basename($file));
                    echo '</p></div>';
                });
            }
        }
    }

    /**
     * Show premium activation notice
     */
    public function show_premium_notice() {
        if (!get_option('nueve4_premium_notice_dismissed') && current_user_can('manage_options')) {
            ?>
            <div class="notice notice-success is-dismissible" id="nueve4-premium-notice">
                <h3><?php _e('🎉 Nueve4 Premium Features Activated!', 'nueve4'); ?></h3>
                <p><?php _e('All premium features have been successfully enabled. You now have access to:', 'nueve4'); ?></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php _e('Premium WooCommerce layouts and features', 'nueve4'); ?></li>
                    <li><?php _e('Advanced header/footer builder components', 'nueve4'); ?></li>
                    <li><?php _e('Premium blocks: Testimonials, Pricing Tables, Team Members', 'nueve4'); ?></li>
                    <li><?php _e('Performance optimization tools', 'nueve4'); ?></li>
                    <li><?php _e('Custom CSS and JavaScript options', 'nueve4'); ?></li>
                    <li><?php _e('Scroll to top functionality', 'nueve4'); ?></li>
                </ul>
                <p>
                    <a href="<?php echo admin_url('customize.php?autofocus[panel]=nueve4_premium_features'); ?>" class="button button-primary">
                        <?php _e('Explore Premium Features', 'nueve4'); ?>
                    </a>
                    <button type="button" class="notice-dismiss" onclick="nueve4DismissPremiumNotice()">
                        <span class="screen-reader-text"><?php _e('Dismiss this notice.', 'nueve4'); ?></span>
                    </button>
                </p>
            </div>
            <script>
            function nueve4DismissPremiumNotice() {
                document.getElementById('nueve4-premium-notice').style.display = 'none';
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=nueve4_dismiss_premium_notice&nonce=<?php echo wp_create_nonce('nueve4_premium_nonce'); ?>'
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
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'nueve4_premium_nonce') && current_user_can('manage_options')) {
            update_option('nueve4_premium_notice_dismissed', true);
        }
        wp_die();
    }
}

// Handle AJAX notice dismissal
add_action('wp_ajax_nueve4_dismiss_premium_notice', function() {
    if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'nueve4_premium_nonce') && current_user_can('manage_options')) {
        update_option('nueve4_premium_notice_dismissed', true);
    }
    wp_die();
});

// Initialize activation
$nueve4_premium_activation = new Premium_Activation();
$nueve4_premium_activation->init();