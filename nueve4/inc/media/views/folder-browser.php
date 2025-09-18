<?php
<script type="text/html" id="tmpl-nueve4-folder-browser">
    <div class="nueve4-folder-browser-container">
        <div class="nueve4-folder-browser-header">
            <button type="button" class="button nueve4-create-folder">
                <span class="dashicons dashicons-plus-alt2"></span>
                <?php esc_html_e('New Folder', 'nueve4'); ?>
            </button>
        </div>
        
        <div class="nueve4-folder-browser-list">
            <div class="nueve4-folder-item<# if (data.currentFolder === 'root') { #> is-active<# } #>"
                 data-folder-id="root">
                <span class="dashicons dashicons-admin-home"></span>
                <?php esc_html_e('All Files', 'nueve4'); ?>
            </div>
            
            <# _.each(data.folders, function(folder) { #>
                <div class="nueve4-folder-item<# if (data.currentFolder === folder.id) { #> is-active<# } #>"
                     data-folder-id="{{ folder.id }}">
                    <span class="dashicons dashicons-category"></span>
                    <span class="nueve4-folder-name">{{ folder.name }}</span>
                    <span class="nueve4-folder-count">{{ folder.count || 0 }}</span>
                </div>
            <# }); #>
        </div>
    </div>
</script>