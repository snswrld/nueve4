(function($) {
    'use strict';

    let fileIndex = 0;

    $(document).ready(function() {
        fileIndex = $('#gallery-files-list .gallery-file-row').length;

        $('#add-gallery-files').on('click', function() {
            const frame = wp.media({
                title: 'Select Files',
                multiple: true,
                library: { type: 'application' }
            });

            frame.on('select', function() {
                const selection = frame.state().get('selection');
                selection.each(function(attachment) {
                    addFileRow(attachment.toJSON());
                });
            });

            frame.open();
        });

        $(document).on('click', '.remove-file', function() {
            $(this).closest('.gallery-file-row').remove();
        });
    });

    function addFileRow(file) {
        const template = $('#file-row-template').html();
        const html = template
            .replace(/\{\{INDEX\}\}/g, fileIndex)
            .replace('value=""', 'value="' + file.id + '"')
            .replace('placeholder="File Title"', 'value="' + file.title + '" placeholder="File Title"')
            .replace('placeholder="File URL"', 'value="' + file.url + '" placeholder="File URL"');
        
        $('#gallery-files-list').append(html);
        fileIndex++;
    }

})(jQuery);