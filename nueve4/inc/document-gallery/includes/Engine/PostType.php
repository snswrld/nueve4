<?php

namespace Nueve4\DocumentGallery\Engine;

class PostType {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type() {
        register_post_type('nueve4_gallery', [
            'labels' => [
                'name' => __('Document Galleries', 'nueve4'),
                'singular_name' => __('Document Gallery', 'nueve4'),
                'add_new' => __('Add New Gallery', 'nueve4'),
                'add_new_item' => __('Add New Document Gallery', 'nueve4'),
                'edit_item' => __('Edit Document Gallery', 'nueve4'),
                'new_item' => __('New Document Gallery', 'nueve4'),
                'view_item' => __('View Document Gallery', 'nueve4'),
                'search_items' => __('Search Document Galleries', 'nueve4'),
                'not_found' => __('No document galleries found', 'nueve4'),
                'not_found_in_trash' => __('No document galleries found in trash', 'nueve4')
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'upload.php',
            'capability_type' => 'post',
            'supports' => ['title', 'editor'],
            'show_in_rest' => true
        ]);
    }
}