<?php

namespace Nueve4\DocumentGallery\Engine;

class RestAPI {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('nueve4/v1', '/gallery/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_gallery_files'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('nueve4/v1', '/folder/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_folder_files'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function get_gallery_files($request) {
        $gallery_id = $request->get_param('id');
        
        $args = [
            'post_type' => 'nueve4_gallery',
            'p' => $gallery_id,
            'post_status' => 'publish'
        ];
        
        $gallery = get_posts($args);
        if (empty($gallery)) {
            return new \WP_Error('not_found', 'Gallery not found', ['status' => 404]);
        }
        
        $files = get_post_meta($gallery_id, '_gallery_files', true);
        return rest_ensure_response($files ?: []);
    }

    public function get_folder_files($request) {
        $folder_id = $request->get_param('id');
        
        $args = [
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'wcp_folder',
                    'value' => $folder_id,
                    'compare' => '='
                ]
            ]
        ];
        
        $attachments = get_posts($args);
        $files = [];
        
        foreach ($attachments as $attachment) {
            $files[] = [
                'id' => $attachment->ID,
                'title' => $attachment->post_title,
                'url' => wp_get_attachment_url($attachment->ID),
                'size' => filesize(get_attached_file($attachment->ID)),
                'type' => wp_check_filetype($attachment->post_title)['ext']
            ];
        }
        
        return rest_ensure_response($files);
    }
}