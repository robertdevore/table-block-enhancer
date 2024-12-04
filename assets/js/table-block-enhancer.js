document.addEventListener('DOMContentLoaded', function () {
    var tables = document.querySelectorAll('.wp-block-table');

    if (tables.length) {
        tables.forEach(function (tableWrapper) {
            var table = tableWrapper.querySelector('table');
            if (!table) return;

            // Initialize settings to default values (true)
            var enablePaging = true;
            var enableSearching = true;
            var enableOrdering = true;

            // Override settings based on classes
            if (tableWrapper.classList.contains('enable-paging')) {
                enablePaging = true;
            } else if (tableWrapper.classList.contains('disable-paging')) {
                enablePaging = false;
            }

            if (tableWrapper.classList.contains('enable-searching')) {
                enableSearching = true;
            } else if (tableWrapper.classList.contains('disable-searching')) {
                enableSearching = false;
            }

            if (tableWrapper.classList.contains('enable-ordering')) {
                enableOrdering = true;
            } else if (tableWrapper.classList.contains('disable-ordering')) {
                enableOrdering = false;
            }

            // Initialize DataTable with settings
            jQuery(table).DataTable({
                paging: enablePaging,
                searching: enableSearching,
                ordering: enableOrdering,
            });
        });
    }
});