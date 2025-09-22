<?php

namespace Nueve4\DocumentGallery\Engine;

class Admin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_gallery_meta']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'nueve4_gallery_files',
            __('Gallery Files', 'nueve4'),
            [$this, 'render_gallery_files_meta_box'],
            'nueve4_gallery',
            'normal',
            'high'
        );
    }

    public function render_gallery_files_meta_box($post) {
        wp_nonce_field('nueve4_gallery_meta', 'nueve4_gallery_nonce');
        
        $files = get_post_meta($post->ID, '_gallery_files', true) ?: [];
        
        echo '<div id="nueve4-gallery-manager">';
        echo '<p><button type="button" class="button" id="add-gallery-files">' . __('Add Files', 'nueve4') . '</button></p>';
        echo '<div id="gallery-files-list">';
        
        foreach ($files as $index => $file) {
            $this->render_file_row($file, $index);
        }
        
        echo '</div>';
        echo '</div>';
        
        echo '<script type="text/template" id="file-row-template">';
        $this->render_file_row(['id' => '', 'title' => '', 'url' => ''], '{{INDEX}}');
        echo '</script>';
    }

    private function render_file_row($file, $index) {
        echo '<div class="gallery-file-row" data-index="' . esc_attr($index) . '">';
        echo '<input type="hidden" name="gallery_files[' . $index . '][id]" value="' . esc_attr($file['id']) . '">';
        echo '<input type="text" name="gallery_files[' . $index . '][title]" value="' . esc_attr($file['title']) . '" placeholder="' . __('File Title', 'nueve4') . '">';
        echo '<input type="url" name="gallery_files[' . $index . '][url]" value="' . esc_attr($file['url']) . '" placeholder="' . __('File URL', 'nueve4') . '">';
        echo '<button type="button" class="button remove-file">' . __('Remove', 'nueve4') . '</button>';
        echo '</div>';
    }

    public function save_gallery_meta($post_id) {
        if (!isset($_POST['nueve4_gallery_nonce']) || !wp_verify_nonce($_POST['nueve4_gallery_nonce'], 'nueve4_gallery_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['gallery_files'])) {
            $files = array_filter($_POST['gallery_files'], function($file) {
                return !empty($file['title']) && !empty($file['url']);
            });
            update_post_meta($post_id, '_gallery_files', $files);
        }
    }

    public function enqueue_admin_scripts($hook) {
        global $post_type;
        
        if ($post_type !== 'nueve4_gallery') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'nueve4-gallery-admin',
            NUEVE4_DG_URL . 'assets/js/admin.js',
            ['jquery', 'media-upload'],
            NUEVE4_DG_VERSION,
            true
        );
    }
}