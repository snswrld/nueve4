<?php

namespace Nueve4\DocumentGallery\Engine\Blocks;

class Blocks {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_blocks']);
    }

    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('nueve4/document-gallery', [
            'attributes' => [
                'folderId' => [
                    'type' => 'number',
                    'default' => 0
                ],
                'galleryId' => [
                    'type' => 'number', 
                    'default' => 0
                ],
                'layout' => [
                    'type' => 'string',
                    'default' => 'grid'
                ]
            ],
            'render_callback' => [$this, 'render_block'],
            'editor_script' => 'nueve4-document-gallery-block'
        ]);

        $this->enqueue_block_assets();
    }

    public function render_block($attributes) {
        $folder_id = isset($attributes['folderId']) ? $attributes['folderId'] : 0;
        $gallery_id = isset($attributes['galleryId']) ? $attributes['galleryId'] : 0;
        
        if (!$folder_id && !$gallery_id) {
            return '<p>' . __('Please select a folder or gallery.', 'nueve4') . '</p>';
        }

        $shortcode_attrs = [];
        if ($folder_id) {
            $shortcode_attrs['folder'] = $folder_id;
        }
        if ($gallery_id) {
            $shortcode_attrs['id'] = $gallery_id;
        }

        return do_shortcode('[nueve4_document_gallery ' . $this->build_shortcode_attrs($shortcode_attrs) . ']');
    }

    private function build_shortcode_attrs($attrs) {
        $parts = [];
        foreach ($attrs as $key => $value) {
            $parts[] = $key . '="' . esc_attr($value) . '"';
        }
        return implode(' ', $parts);
    }

    private function enqueue_block_assets() {
        wp_register_script(
            'nueve4-document-gallery-block',
            NUEVE4_DG_URL . 'assets/js/block.js',
            ['wp-blocks', 'wp-element', 'wp-editor'],
            NUEVE4_DG_VERSION,
            true
        );
    }
}