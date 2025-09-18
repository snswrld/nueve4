(function($) {
    'use strict';

    const Nueve4FileManager = {
        init: function() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                return;
            }

            this.initDragDrop();
            this.initFolderView();
            this.bindEvents();
        },

        initDragDrop: function() {
            $('.nueve4-folders-list').sortable({
                items: '.nueve4-folder-item:not([data-folder-id="root"])',
                cursor: 'move',
                axis: 'y',
                update: this.handleFolderReorder
            });

            $('.nueve4-folder-item').droppable({
                accept: '.attachment',
                hoverClass: 'nueve4-folder-hover',
                drop: this.handleItemDrop
            });
        },

        handleItemDrop: function(event, ui) {
            const itemId = ui.draggable.data('id');
            const targetFolder = $(this).data('folder-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: 'drag_drop',
                    item_id: itemId,
                    target_folder: targetFolder,
                    nonce: nueve4FileManager.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wp.media.frame.content.get().collection.props.set({
                            folder: response.data.folder
                        });
                    } else {
                        alert(nueve4FileManager.strings.error);
                    }
                }
            });
        },

        handleFolderReorder: function(event, ui) {
            const folderOrder = $(this).sortable('toArray', {
                attribute: 'data-folder-id'
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: 'update_order',
                    folder_order: folderOrder,
                    nonce: nueve4FileManager.nonce
                },
                success: function(response) {
                    if (!response.success) {
                        alert(nueve4FileManager.strings.error);
                    }
                }
            });
        },

        initFolderView: function() {
            const FolderView = wp.Backbone.View.extend({
                className: 'nueve4-folder-view',
                template: wp.template('nueve4-folder-list'),

                events: {
                    'click .nueve4-create-folder': 'createFolder',
                    'click .nueve4-folder-item': 'selectFolder'
                },

                initialize: function() {
                    this.render();
                },

                render: function() {
                    this.$el.html(this.template({
                        folders: wp.media.view.settings.nueve4Folders
                    }));
                    return this;
                },

                createFolder: function(e) {
                    e.preventDefault();
                    // Folder creation logic
                },

                selectFolder: function(e) {
                    e.preventDefault();
                    // Folder selection logic
                }
            });

            // Extend wp.media.view.AttachmentsBrowser
            const oldAttachmentsBrowser = wp.media.view.AttachmentsBrowser;
            wp.media.view.AttachmentsBrowser = oldAttachmentsBrowser.extend({
                initialize: function() {
                    oldAttachmentsBrowser.prototype.initialize.apply(this, arguments);
                    this.createFolderView();
                },

                createFolderView: function() {
                    this.folderView = new FolderView({
                        controller: this.controller
                    });

                    this.$el.prepend(this.folderView.el);
                }
            });
        },

        bindEvents: function() {
            $(document).on('click', '.nueve4-create-folder', this.handleCreateFolder);
            $(document).on('dragstart', '.attachment', this.handleDragStart);
            $(document).on('drop', '.nueve4-folder-item', this.handleDrop);
            $(document).on('click', '.nueve4-folder-breadcrumb a', this.handleBreadcrumbClick);
        },

        performFolderAction: function(action, data, successCallback) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: action,
                    nonce: nueve4FileManager.nonce,
                    ...data
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof successCallback === 'function') {
                            successCallback(response.data);
                        }
                    } else {
                        alert(nueve4FileManager.strings.error);
                    }
                }
            });
        },

        handleCreateFolder: function(e) {
            e.preventDefault();
            
            const folderName = prompt(wp.media.view.l10n.folderName);
            if (!folderName) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: 'create',
                    name: folderName,
                    nonce: nueve4FileManager.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wp.media.frame.content.get().collection.reset();
                    }
                }
            });
        },

        handleDragStart: function(e) {
            e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
        },

        handleDrop: function(e) {
            e.preventDefault();
            
            const itemId = e.originalEvent.dataTransfer.getData('text/plain');
            const folderId = $(this).data('folder-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: 'move',
                    items: [itemId],
                    target: folderId,
                    nonce: nueve4FileManager.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wp.media.frame.content.get().collection.reset();
                    }
                }
            });
        },

        handleBreadcrumbClick: function(e) {
            e.preventDefault();
            const folderId = $(e.currentTarget).data('folder-id');
            this.navigateToFolder(folderId);
        },

        navigateToFolder: function(folderId) {
            wp.media.frame.content.get().collection.props.set({
                folder: folderId
            });
            
            this.updateBreadcrumbs(folderId);
            this.highlightActiveFolder(folderId);
        },

        updateBreadcrumbs: function(folderId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nueve4_file_manager_action',
                    folder_action: 'get_breadcrumbs',
                    folder_id: folderId,
                    nonce: nueve4FileManager.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const template = wp.template('nueve4-folder-breadcrumbs');
                        $('.nueve4-folder-breadcrumbs').replaceWith(
                            template({ breadcrumbs: response.data })
                        );
                    }
                }
            });
        },

        highlightActiveFolder: function(folderId) {
            $('.nueve4-folder-item').removeClass('is-active');
            $(`.nueve4-folder-item[data-folder-id="${folderId}"]`).addClass('is-active');
        },

        refreshMediaGrid: function() {
            if (wp.media.frame && wp.media.frame.content.get()) {
                wp.media.frame.content.get().collection.props.set({
                    ignore: Date.now()
                });
            }
        },

        extendMediaFrame: function() {
            const oldFrame = wp.media.view.MediaFrame.Select;
            wp.media.view.MediaFrame.Select = oldFrame.extend({
                initialize: function() {
                    oldFrame.prototype.initialize.apply(this, arguments);
                    this.on('content:create:browse', this.createFolderBrowser, this);
                },

                createFolderBrowser: function(browserView) {
                    browserView.sidebar = new wp.media.view.Sidebar({
                        controller: this
                    });

                    browserView.sidebar.set('folders', new FolderView({
                        controller: this,
                        priority: 40
                    }));

                    browserView.$el.addClass('has-folders-sidebar');
                }
            });
        },

        handleFolderContextMenu: function(e) {
            e.preventDefault();
            const $folder = $(e.currentTarget);
            const folderId = $folder.data('folder-id');

            if (folderId === 'root') return;

            this.showContextMenu(e, [
                {
                    text: nueve4FileManager.strings.rename,
                    action: () => this.renameFolder($folder)
                },
                {
                    text: nueve4FileManager.strings.delete,
                    action: () => this.deleteFolder(folderId)
                }
            ]);
        },

        showContextMenu: function(e, items) {
            const $menu = $('<div class="nueve4-context-menu"></div>');
            
            items.forEach(item => {
                $('<div class="nueve4-context-menu-item"></div>')
                    .text(item.text)
                    .on('click', () => {
                        item.action();
                        $menu.remove();
                    })
                    .appendTo($menu);
            });

            $('body')
                .append($menu)
                .one('click', () => $menu.remove());

            $menu.css({
                top: e.pageY,
                left: e.pageX
            });
        },

        createFolderBrowser: function(browserView) {
            const FolderBrowser = wp.Backbone.View.extend({
                className: 'nueve4-folder-browser',
                template: wp.template('nueve4-folder-browser'),

                events: {
                    'click .nueve4-folder-item': 'selectFolder',
                    'contextmenu .nueve4-folder-item': 'showFolderMenu',
                    'click .nueve4-create-folder': 'createFolder'
                },

                initialize: function() {
                    this.render();
                    this.listenTo(this.collection, 'reset', this.render);
                },

                render: function() {
                    this.$el.html(this.template({
                        folders: wp.media.view.settings.nueve4Folders,
                        currentFolder: this.collection.props.get('folder') || 'root'
                    }));
                    return this;
                },

                selectFolder: function(e) {
                    e.preventDefault();
                    const folderId = $(e.currentTarget).data('folder-id');
                    this.collection.props.set('folder', folderId);
                    this.trigger('folder:selected', folderId);
                },

                showFolderMenu: function(e) {
                    e.preventDefault();
                    Nueve4FileManager.handleFolderContextMenu.call(this, e);
                }
            });

            browserView.toolbar.set('folderBrowser', new FolderBrowser({
                controller: this,
                collection: browserView.collection
            }));
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        Nueve4FileManager.init();
    });
})(jQuery);