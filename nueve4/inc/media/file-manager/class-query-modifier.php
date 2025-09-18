<?php
<?php

namespace Nueve4\Media;

class Query_Modifier {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action('pre_get_posts', [$this, 'modify_media_query']);
        add_filter('ajax_query_attachments_args', [$this, 'filter_media_query']);
    }

    public function modify_media_query($query) {
        if (!$query->is_main_query() || !is_admin()) {
            return;
        }

        if ($query->get('post_type') !== 'attachment') {
            return;
        }

        $folder = isset($_REQUEST['folder']) ? sanitize_text_field($_REQUEST['folder']) : null;
        
        if ($folder) {
            $query->set('meta_key', 'nueve4_folder');
            $query->set('meta_value', $folder);
        }
    }

    public function filter_media_query($args) {
        $folder = isset($_REQUEST['folder']) ? sanitize_text_field($_REQUEST['folder']) : null;
        
        if ($folder) {
            $args['meta_query'] = array(
                array(
                    'key'     => 'nueve4_folder',
                    'value'   => $folder,
                    'compare' => '='
                )
            );
        }
        
        return $args;
    }
}