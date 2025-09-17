<?php
/**
 * Theme Dependencies Manager
 *
 * @package Nueve4
 */

class Nueve4_Dependencies {

    private $required_plugins = array(
        'elementor' => array(
            'name' => 'Elementor',
            'slug' => 'elementor',
            'file' => 'elementor/elementor.php',
            'version' => '3.5.0',
            'required' => true,
            'url' => 'https://wordpress.org/plugins/elementor/'
        )
    );

    public function __construct() {
        add_action('admin_notices', array($this, 'check_dependencies'));
        add_action('wp_ajax_install_elementor', array($this, 'install_elementor'));
    }

    public function check_dependencies() {
        if (!current_user_can('install_plugins')) {
            return;
        }

        foreach ($this->required_plugins as $plugin) {
            if (!$this->is_plugin_installed($plugin['file'])) {
                $this->show_install_notice($plugin);
            } elseif (!$this->is_plugin_active($plugin['file'])) {
                $this->show_activate_notice($plugin);
            }
        }
    }

    private function is_plugin_installed($plugin_file) {
        return file_exists(WP_PLUGIN_DIR . '/' . $plugin_file);
    }

    private function is_plugin_active($plugin_file) {
        return is_plugin_active($plugin_file);
    }

    private function show_install_notice($plugin) {
        $install_url = wp_nonce_url(
            add_query_arg(array(
                'action' => 'install-plugin',
                'plugin' => $plugin['slug']
            ), admin_url('update.php')),
            'install-plugin_' . $plugin['slug']
        );

        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>' . esc_html($plugin['name']) . '</strong> is required for full theme functionality.</p>';
        echo '<p><a href="' . esc_url($install_url) . '" class="button button-primary">Install ' . esc_html($plugin['name']) . '</a></p>';
        echo '</div>';
    }

    private function show_activate_notice($plugin) {
        $activate_url = wp_nonce_url(
            add_query_arg(array(
                'action' => 'activate',
                'plugin' => $plugin['file']
            ), admin_url('plugins.php')),
            'activate-plugin_' . $plugin['file']
        );

        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>' . esc_html($plugin['name']) . '</strong> is installed but not activated.</p>';
        echo '<p><a href="' . esc_url($activate_url) . '" class="button button-primary">Activate ' . esc_html($plugin['name']) . '</a></p>';
        echo '</div>';
    }

    public function install_elementor() {
        if (!current_user_can('install_plugins')) {
            wp_die('Insufficient permissions');
        }

        check_ajax_referer('install_elementor', 'nonce');

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        $plugin_slug = 'elementor';
        $plugin_zip = download_url('https://downloads.wordpress.org/plugin/elementor.latest-stable.zip');

        if (is_wp_error($plugin_zip)) {
            wp_die('Download failed');
        }

        $upgrader = new Plugin_Upgrader();
        $result = $upgrader->install($plugin_zip);

        unlink($plugin_zip);

        if ($result) {
            activate_plugin('elementor/elementor.php');
            wp_send_json_success('Elementor installed and activated');
        } else {
            wp_send_json_error('Installation failed');
        }
    }

    public static function is_elementor_active() {
        return is_plugin_active('elementor/elementor.php');
    }
}

new Nueve4_Dependencies();