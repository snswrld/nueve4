(function($) {
    'use strict';

    class Nueve4DocumentGallery {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            $(document).on('input', '.gallery-search', this.handleSearch.bind(this));
            $(document).on('change', '.gallery-filter', this.handleFilter.bind(this));
        }

        handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            const gallery = $(e.target).closest('.nueve4-document-gallery');
            const items = gallery.find('.gallery-item');

            items.each(function() {
                const title = $(this).find('.item-title').text().toLowerCase();
                const visible = title.includes(searchTerm);
                $(this).toggle(visible);
            });
        }

        handleFilter(e) {
            const filterType = e.target.value;
            const gallery = $(e.target).closest('.nueve4-document-gallery');
            const items = gallery.find('.gallery-item');

            items.each(function() {
                const itemType = $(this).data('type');
                const visible = !filterType || itemType === filterType;
                $(this).toggle(visible);
            });
        }
    }

    $(document).ready(function() {
        new Nueve4DocumentGallery();
    });

})(jQuery);