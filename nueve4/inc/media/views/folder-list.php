<script type="text/html" id="tmpl-nueve4-folder-list">
    <div class="nueve4-folders-container">
        <div class="nueve4-folders-header">
            <h3><?php esc_html_e('Folders', 'nueve4'); ?></h3>
            <# if ( data.allowFolderCreation ) { #>
                <button class="button nueve4-create-folder">
                    <?php esc_html_e('New Folder', 'nueve4'); ?>
                </button>
            <# } #>
        </div>
        <div class="nueve4-folders-list">
            <div class="nueve4-folder-item" data-folder-id="root">
                <?php esc_html_e('All Files', 'nueve4'); ?>
            </div>
            <# _.each(data.folders, function(folder) { #>
                <div class="nueve4-folder-item" data-folder-id="{{ folder.id }}">
                    {{ folder.name }}
                </div>
            <# }); #>
        </div>
    </div>
</script>