<?php
/**
 * Premium Blocks Category for Neve Theme
 * 
 * @package Neve\Premium
 */

namespace Neve\Premium;

/**
 * Class Premium_Blocks_Category
 * 
 * Registers and manages premium block categories
 */
class Premium_Blocks_Category {

    /**
     * Initialize the class
     */
    public function init() {
        add_action('block_categories_all', array($this, 'register_block_category'), 10, 2);
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    /**
     * Register custom block category
     */
    public function register_block_category($categories, $post) {
        return array_merge(
            array(
                array(
                    'slug' => 'neve-blocks',
                    'title' => __('Neve Premium Blocks', 'neve'),
                    'icon' => 'star-filled'
                )
            ),
            $categories
        );
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        // Enqueue block editor styles
        wp_enqueue_style(
            'neve-blocks-editor',
            get_template_directory_uri() . '/assets/css/premium-blocks.css',
            array(),
            NEVE_VERSION
        );

        // Enqueue block editor scripts
        wp_enqueue_script(
            'neve-blocks-editor',
            get_template_directory_uri() . '/assets/js/premium-blocks/editor.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            NEVE_VERSION,
            true
        );

        // Localize script with data
        wp_localize_script('neve-blocks-editor', 'neveBlocks', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('neve_blocks_nonce'),
            'strings' => array(
                'addItem' => __('Add Item', 'neve'),
                'removeItem' => __('Remove Item', 'neve'),
                'selectImage' => __('Select Image', 'neve'),
                'changeImage' => __('Change Image', 'neve'),
                'noImageSelected' => __('No image selected', 'neve')
            )
        ));
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if blocks are present on the page
        if (has_block('neve/testimonials') || has_block('neve/pricing-table') || has_block('neve/team-members')) {
            wp_enqueue_style(
                'neve-premium-blocks',
                get_template_directory_uri() . '/assets/css/premium-blocks.css',
                array(),
                NEVE_VERSION
            );

            wp_enqueue_script(
                'neve-premium-blocks',
                get_template_directory_uri() . '/assets/js/premium-blocks/frontend.js',
                array('jquery'),
                NEVE_VERSION,
                true
            );
        }
    }
}

// Initialize the class
$neve_blocks_category = new Premium_Blocks_Category();
$neve_blocks_category->init();