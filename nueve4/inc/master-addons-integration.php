<?php
/**
 * Master Addons Integration
 *
 * @package Nueve4
 */

class Nueve4_Master_Addons_Integration {

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        if (!$this->is_elementor_active()) {
            return;
        }

        // Enable all Master Addons features
        add_filter('jltma_has_valid_license', '__return_true');
        add_filter('jltma_pro_addon_is_active', '__return_true');
        
        // Load Master Addons if available
        $this->load_master_addons();
    }

    private function is_elementor_active() {
        return class_exists('Elementor\Plugin');
    }

    private function load_master_addons() {
        $master_addons_path = get_template_directory() . '/master-addons/master-addons/master-addons.php';
        
        if (file_exists($master_addons_path)) {
            // Define Master Addons constants
            if (!defined('JLTMA_VER')) {
                define('JLTMA_VER', '2.0.7.6');
            }
            if (!defined('JLTMA_BASE')) {
                define('JLTMA_BASE', plugin_basename($master_addons_path));
            }
            if (!defined('JLTMA_FILE')) {
                define('JLTMA_FILE', $master_addons_path);
            }
            
            // Override Freemius to return premium
            add_filter('pre_option_fs_accounts', array($this, 'fake_freemius_account'));
            
            require_once $master_addons_path;
        }
    }

    public function fake_freemius_account($accounts) {
        return array(
            'sites' => array(
                '4015' => array(
                    'user_id' => '1',
                    'site_id' => '1',
                    'public_key' => 'pk_premium',
                    'secret_key' => 'sk_premium',
                    'is_premium' => true,
                    'license' => array(
                        'id' => '1',
                        'plan' => 'premium',
                        'is_active' => true
                    )
                )
            )
        );
    }
}

new Nueve4_Master_Addons_Integration();