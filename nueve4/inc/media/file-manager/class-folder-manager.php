<?php
<?php

namespace Nueve4\Media;

class Folder_Manager {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action('add_attachment', [$this, 'process_new_attachment']);
        add_filter('attachment_fields_to_edit', [$this, 'add_folder_field'], 10, 2);
        add_filter('attachment_fields_to_save', [$this, 'save_folder_field'], 10, 2);
    }

    public function process_new_attachment($post_id) {
        $folder = isset($_REQUEST['folder']) ? sanitize_text_field($_REQUEST['folder']) : 'root';
        update_post_meta($post_id, 'nueve4_folder', $folder);
    }

    public function add_folder_field($form_fields, $post) {
        $folders = $this->get_folders_list();
        $current = get_post_meta($post->ID, 'nueve4_folder', true);
        
        $form_fields['nueve4_folder'] = array(
            'label' => __('Folder', 'nueve4'),
            'input' => 'html',
            'html'  => $this->get_folder_select_html($folders, $current),
            'helps' => __('Select a folder for this media item.', 'nueve4')
        );
        
        return $form_fields;
    }

    public function save_folder_field($post, $attachment) {
        if (isset($attachment['nueve4_folder'])) {
            update_post_meta($post['ID'], 'nueve4_folder', 
                           sanitize_text_field($attachment['nueve4_folder']));
        }
        return $post;
    }

    private function get_folders_list() {
        $folders = get_option('nueve4_media_folders', array());
        return array_merge(
            array(array('id' => 'root', 'name' => __('Root', 'nueve4'))), 
            $folders
        );
    }

    private function get_folder_select_html($folders, $current) {
        $html = '<select name="attachments[' . get_the_ID() . '][nueve4_folder]">';
        foreach ($folders as $folder) {
            $selected = selected($current, $folder['id'], false);
            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr($folder['id']),
                $selected,
                esc_html($folder['name'])
            );
        }
        $html .= '</select>';
        return $html;
    }
}