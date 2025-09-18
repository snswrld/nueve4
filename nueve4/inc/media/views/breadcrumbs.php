<?php
<script type="text/html" id="tmpl-nueve4-folder-breadcrumbs">
    <div class="nueve4-folder-breadcrumbs">
        <# _.each(data.breadcrumbs, function(crumb, index) { #>
            <span class="nueve4-breadcrumb-item" data-folder-id="{{ crumb.id }}">
                {{ crumb.name }}
            </span>
            <# if (index < data.breadcrumbs.length - 1) { #>
                <span class="nueve4-breadcrumb-separator">/</span>
            <# } #>
        <# }); #>
    </div>
</script>