<?php
<?php

namespace Nueve4\Media;

class File_Manager {
    private static $instance = null;
    private $query_modifier;
    private $folder_manager;

    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the file manager
     */
    public function init() {
        // Initialize dependencies
        $this->query_modifier = Query_Modifier::get_instance();
        $this->folder_manager = Folder_Manager::get_instance();

        $this->query_modifier->init();
        $this->folder_manager->init();

        // Add core hooks
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('print_media_templates', [$this, 'print_media_templates']);
        add_filter('media_view_strings', [$this, 'modify_media_strings']);
        add_filter('media_view_settings', [$this, 'modify_media_settings']);
        add_action('wp_ajax_nueve4_file_manager_action', [$this, 'handle_ajax']);
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php', 'upload.php'], true)) {
            return;
        }

        wp_enqueue_style(
            'nueve4-file-manager',
            get_template_directory_uri() . '/assets/css/file-manager.css',
            [],
            NUEVE4_VERSION
        );

        wp_enqueue_script(
            'nueve4-file-manager',
            get_template_directory_uri() . '/assets/js/file-manager.js',
            ['jquery', 'media-views'],
            NUEVE4_VERSION,
            true
        );

        wp_localize_script('nueve4-file-manager', 'nueve4FileManager', [
            'nonce' => wp_create_nonce('nueve4_file_manager_nonce'),
            'strings' => [
                'newFolder' => __('New Folder', 'nueve4'),
                'folderName' => __('Enter folder name', 'nueve4'),
                'error' => __('An error occurred', 'nueve4'),
                'confirmDelete' => __('Are you sure you want to delete this folder?', 'nueve4')
            ]
        ]);
    }

    /**
     * Print media templates for the folder browser
     */
    public function print_media_templates() {
        ?>
        <script type="text/html" id="tmpl-nueve4-folders-browser">
            <div class="nueve4-folders-container">
                <div class="nueve4-folders-header">
                    <h3><?php esc_html_e('Media Folders', 'nueve4'); ?></h3>
                    <button type="button" class="button nueve4-create-folder">
                        <?php esc_html_e('New Folder', 'nueve4'); ?>
                    </button>
                </div>
                <div class="nueve4-folders-list">
                    <# _.each(data.folders, function(folder) { #>
                        <div class="nueve4-folder-item" data-folder-id="{{ folder.id }}">
                            <span class="dashicons dashicons-category"></span>
                            {{ folder.name }}
                        </div>
                    <# }); #>
                </div>
            </div>
        </script>
        <?php
    }

    /**
     * Modify media strings
     */
    public function modify_media_strings($strings) {
        $strings['createNewFolder'] = __('Create Folder', 'nueve4');
        $strings['folderName'] = __('Folder Name', 'nueve4');
        return $strings;
    }

    /**
     * Modify media settings
     */
    public function modify_media_settings($settings) {
        $settings['nueve4Folders'] = $this->get_folders();
        $settings['allowFolderCreation'] = current_user_can('upload_files');
        return $settings;
    }

    /**
     * Handle AJAX requests
     */
    public function handle_ajax() {
        check_ajax_referer('nueve4_file_manager_nonce', 'nonce');

        $action = isset($_POST['folder_action']) ? sanitize_text_field($_POST['folder_action']) : '';

        switch ($action) {
            case 'get_breadcrumbs':
                $folder_id = isset($_POST['folder_id']) ? sanitize_text_field($_POST['folder_id']) : 'root';
                wp_send_json_success($this->get_folder_breadcrumbs($folder_id));
                break;
            case 'create':
                $this->create_folder();
                break;
            case 'rename':
                $this->rename_folder();
                break;
            case 'delete':
                $this->delete_folder();
                break;
            case 'drag_drop':
                $this->handle_drag_drop();
                break;
            case 'update_order':
                $this->update_folder_order();
                break;
            default:
                wp_send_json_error(__('Invalid action', 'nueve4'));
                break;
        }

        wp_die();
    }

    /**
     * Get folders
     */
    private function get_folders() {
        $folders = get_option('nueve4_media_folders', []);
        if (empty($folders)) {
            $folders = [
                [
                    'id' => 'root',
                    'name' => __('Root', 'nueve4'),
                    'parent' => 0
                ]
            ];
        }
        return $folders;
    }

    private function handle_folder_action($action, $data) {
        $folders = $this->get_folders();

        switch ($action) {
            case 'create':
                $new_folder = [
                    'id' => uniqid('folder_'),
                    'name' => sanitize_text_field($data['name']),
                    'parent' => $data['parent'] ?? 'root'
                ];
                $folders[] = $new_folder;
                update_option('nueve4_media_folders', $folders);
                return $new_folder;

            case 'rename':
                foreach ($folders as &$folder) {
                    if ($folder['id'] === $data['folder_id']) {
                        $folder['name'] = sanitize_text_field($data['name']);
                        update_option('nueve4_media_folders', $folders);
                        return $folder;
                    }
                }
                break;

            case 'delete':
                $folders = array_filter($folders, function($folder) use ($data) {
                    return $folder['id'] !== $data['folder_id'];
                });
                update_option('nueve4_media_folders', $folders);
                return true;
        }

        return false;
    }

    // Refactor create_folder
    private function create_folder() {
        $data = [
            'name' => $_POST['name'] ?? '',
            'parent' => $_POST['parent'] ?? 'root'
        ];
        $result = $this->handle_folder_action('create', $data);

        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Failed to create folder', 'nueve4'));
        }
    }

    // Refactor rename_folder
    private function rename_folder() {
        $data = [
            'folder_id' => $_POST['folder_id'] ?? '',
            'name' => $_POST['name'] ?? ''
        ];
        $result = $this->handle_folder_action('rename', $data);

        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Failed to rename folder', 'nueve4'));
        }
    }

    // Refactor delete_folder
    private function delete_folder() {
        $data = [
            'folder_id' => $_POST['folder_id'] ?? ''
        ];
        $result = $this->handle_folder_action('delete', $data);

        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to delete folder', 'nueve4'));
        }
    }

    public function register_meta() {
        register_meta('post', 'nueve4_folder', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'auth_callback' => function() {
                return current_user_can('upload_files');
            }
        ));
    }

    private function get_folder_breadcrumbs($folder_id) {
        $breadcrumbs = array();
        $folders = $this->get_folders();
        
        while ($folder_id && $folder_id !== 'root') {
            $folder = $this->find_folder($folders, $folder_id);
            if (!$folder) break;
            
            array_unshift($breadcrumbs, $folder);
            $folder_id = $folder['parent'];
        }
        
        array_unshift($breadcrumbs, array(
            'id' => 'root',
            'name' => __('Root', 'nueve4')
        ));
        
        return $breadcrumbs;
    }

    private function find_folder($folders, $folder_id) {
        foreach ($folders as $folder) {
            if ($folder['id'] === $folder_id) {
                return $folder;
            }
        }
        return null;
    }

    public function __construct() {
        add_action('init', [$this, 'register_meta']);
    }

    private function handle_drag_drop() {
        check_ajax_referer('nueve4_file_manager_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_send_json_error('Permission denied');
        }

        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $target_folder = isset($_POST['target_folder']) ? sanitize_text_field($_POST['target_folder']) : '';
        
        if (!$item_id || empty($target_folder)) {
            wp_send_json_error('Invalid parameters');
        }

        // Update the attachment's folder
        $result = update_post_meta($item_id, 'nueve4_folder', $target_folder);
        
        if ($result) {
            wp_send_json_success([
                'message' => __('Item moved successfully', 'nueve4'),
                'folder' => $target_folder
            ]);
        } else {
            wp_send_json_error(__('Failed to move item', 'nueve4'));
        }
    }

    private function update_folder_order() {
        check_ajax_referer('nueve4_file_manager_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_send_json_error('Permission denied');
        }

        $folder_order = isset($_POST['folder_order']) ? (array)$_POST['folder_order'] : array();
        
        if (empty($folder_order)) {
            wp_send_json_error('No folder order provided');
        }

        $folders = $this->get_folders();
        $updated_folders = array();

        foreach ($folder_order as $index => $folder_id) {
            foreach ($folders as $folder) {
                if ($folder['id'] === $folder_id) {
                    $folder['order'] = $index;
                    $updated_folders[] = $folder;
                    break;
                }
            }
        }

        if (!empty($updated_folders)) {
            update_option('nueve4_media_folders', $updated_folders);
            wp_send_json_success([
                'message' => __('Folder order updated', 'nueve4'),
                'folders' => $updated_folders
            ]);
        } else {
            wp_send_json_error(__('Failed to update folder order', 'nueve4'));
        }
    }
}