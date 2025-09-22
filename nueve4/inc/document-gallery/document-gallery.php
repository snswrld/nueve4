<?php
/**
 * Nueve4 Document Gallery Integration
 * 
 * @package Nueve4\DocumentGallery
 */

namespace Nueve4\DocumentGallery;

defined('ABSPATH') || exit;

if (!defined('NUEVE4_DG_DIR')) {
    define('NUEVE4_DG_DIR', __DIR__);
}

if (!defined('NUEVE4_DG_URL')) {
    define('NUEVE4_DG_URL', get_template_directory_uri() . '/inc/document-gallery/');
}

if (!defined('NUEVE4_DG_VERSION')) {
    define('NUEVE4_DG_VERSION', NUEVE4_VERSION);
}

spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__;
    $base_dir = __DIR__ . '/includes';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class_name = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class_name) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

class DocumentGallery {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function init() {
        $this->register_shortcode();
        $this->init_components();
    }

    private function register_shortcode() {
        add_shortcode('nueve4_document_gallery', [$this, 'render_gallery']);
    }

    private function init_components() {
        if (class_exists('\\Nueve4\\DocumentGallery\\Engine\\RestAPI')) {
            Engine\RestAPI::get_instance();
        }
        if (class_exists('\\Nueve4\\DocumentGallery\\Engine\\PostType')) {
            Engine\PostType::get_instance();
        }
        if (class_exists('\\Nueve4\\DocumentGallery\\Engine\\Blocks\\Blocks')) {
            Engine\Blocks\Blocks::get_instance();
        }
        if (is_admin() && class_exists('\\Nueve4\\DocumentGallery\\Engine\\Admin')) {
            Engine\Admin::get_instance();
        }
    }

    public function render_gallery($attrs) {
        if (!isset($attrs['folder']) && !isset($attrs['id'])) {
            return '<p>Please specify a folder or gallery ID.</p>';
        }

        $this->enqueue_gallery_assets();

        ob_start();
        $this->render_gallery_html($attrs);
        return ob_get_clean();
    }

    private function render_gallery_html($attrs) {
        $folder_id = isset($attrs['folder']) ? intval($attrs['folder']) : 0;
        $gallery_id = isset($attrs['id']) ? intval($attrs['id']) : 0;
        
        $files = $this->get_folder_files($folder_id, $gallery_id);
        
        echo '<div class="nueve4-document-gallery" data-folder="' . esc_attr($folder_id) . '">';
        echo '<div class="gallery-controls">';
        echo '<input type="search" class="gallery-search" placeholder="' . esc_attr__('Search documents...', 'nueve4') . '">';
        echo '<select class="gallery-filter"><option value="">' . esc_html__('All Types', 'nueve4') . '</option></select>';
        echo '</div>';
        echo '<div class="gallery-grid">';
        
        foreach ($files as $file) {
            $this->render_file_item($file);
        }
        
        echo '</div>';
        echo '</div>';
    }

    private function render_file_item($file) {
        $file_type = wp_check_filetype($file['url'])['ext'];
        $icon = $this->get_file_icon($file_type);
        
        echo '<div class="gallery-item" data-type="' . esc_attr($file_type) . '">';
        echo '<div class="item-icon">' . $icon . '</div>';
        echo '<div class="item-title">' . esc_html($file['title']) . '</div>';
        echo '<div class="item-size">' . size_format($file['size']) . '</div>';
        echo '<div class="item-actions">';
        echo '<a href="' . esc_url($file['url']) . '" class="download-btn" download>' . esc_html__('Download', 'nueve4') . '</a>';
        echo '</div>';
        echo '</div>';
    }

    private function get_folder_files($folder_id, $gallery_id) {
        if ($gallery_id) {
            return $this->get_gallery_files($gallery_id);
        }
        
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
                'size' => filesize(get_attached_file($attachment->ID))
            ];
        }
        
        return $files;
    }

    private function get_gallery_files($gallery_id) {
        // Implementation for custom gallery post type
        return [];
    }

    private function get_file_icon($file_type) {
        $icons = [
            'pdf' => '<svg viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
            'doc' => '<svg viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
            'xls' => '<svg viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
            'default' => '<svg viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>'
        ];
        
        return isset($icons[$file_type]) ? $icons[$file_type] : $icons['default'];
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'nueve4-document-gallery',
            NUEVE4_DG_URL . 'assets/css/gallery.css',
            [],
            NUEVE4_DG_VERSION
        );
    }

    private function enqueue_gallery_assets() {
        wp_enqueue_script(
            'nueve4-document-gallery',
            NUEVE4_DG_URL . 'assets/js/gallery.js',
            ['jquery'],
            NUEVE4_DG_VERSION,
            true
        );
        
        wp_localize_script('nueve4-document-gallery', 'nueve4Gallery', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nueve4_gallery_nonce')
        ]);
    }
}