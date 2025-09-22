(function(blocks, element, editor) {
    const { registerBlockType } = blocks;
    const { createElement: el } = element;
    const { InspectorControls } = editor;
    const { PanelBody, SelectControl, TextControl } = wp.components;

    registerBlockType('nueve4/document-gallery', {
        title: 'Nueve4 Document Gallery',
        icon: 'media-document',
        category: 'media',
        attributes: {
            folderId: {
                type: 'number',
                default: 0
            },
            galleryId: {
                type: 'number',
                default: 0
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { folderId, galleryId } = attributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Gallery Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Folder ID',
                            value: folderId,
                            onChange: function(value) {
                                setAttributes({ folderId: parseInt(value) || 0 });
                            }
                        }),
                        el(TextControl, {
                            label: 'Gallery ID',
                            value: galleryId,
                            onChange: function(value) {
                                setAttributes({ galleryId: parseInt(value) || 0 });
                            }
                        })
                    )
                ),
                el('div', { 
                    key: 'preview',
                    className: 'nueve4-gallery-preview',
                    style: {
                        padding: '20px',
                        border: '2px dashed #ccc',
                        textAlign: 'center'
                    }
                },
                    el('p', {}, 'Nueve4 Document Gallery'),
                    el('small', {}, 
                        folderId ? `Folder ID: ${folderId}` : 
                        galleryId ? `Gallery ID: ${galleryId}` : 
                        'Please set a Folder ID or Gallery ID'
                    )
                )
            ];
        },

        save: function() {
            return null;
        }
    });

})(window.wp.blocks, window.wp.element, window.wp.editor);