<?php
/**
 * Premium Blocks Category for Nueve4 Theme
 * 
 * @package Nueve4\Premium
 */

namespace Nueve4\Premium;

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
                    'slug' => 'nueve4-blocks',
                    'title' => __('Nueve4 Premium Blocks', 'nueve4'),
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
            'nueve4-blocks-editor',
            get_template_directory_uri() . '/assets/css/premium-blocks.css',
            array(),
            NUEVE4_VERSION
        );

        // Enqueue block editor scripts
        wp_enqueue_script(
            'nueve4-blocks-editor',
            get_template_directory_uri() . '/assets/js/premium-blocks/editor.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            NUEVE4_VERSION,
            true
        );

        // Localize script with data
        wp_localize_script('nueve4-blocks-editor', 'nueve4Blocks', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nueve4_blocks_nonce'),
            'strings' => array(
                'addItem' => __('Add Item', 'nueve4'),
                'removeItem' => __('Remove Item', 'nueve4'),
                'selectImage' => __('Select Image', 'nueve4'),
                'changeImage' => __('Change Image', 'nueve4'),
                'noImageSelected' => __('No image selected', 'nueve4')
            )
        ));
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if blocks are present on the page
        if (has_block('nueve4/testimonials') || has_block('nueve4/pricing-table') || has_block('nueve4/team-members')) {
            wp_enqueue_style(
                'nueve4-premium-blocks',
                get_template_directory_uri() . '/assets/css/premium-blocks.css',
                array(),
                NUEVE4_VERSION
            );

            wp_enqueue_script(
                'nueve4-premium-blocks',
                get_template_directory_uri() . '/assets/js/premium-blocks/frontend.js',
                array('jquery'),
                NUEVE4_VERSION,
                true
            );
        }
    }
}

// Initialize the class
$nueve4_blocks_category = new Premium_Blocks_Category();
$nueve4_blocks_category->init();