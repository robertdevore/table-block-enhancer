document.addEventListener('DOMContentLoaded', function () {
    const tables = document.querySelectorAll('.wp-block-table table, table.wp-block-table');

    if (tables.length && typeof tableBlockEnhancer !== 'undefined') {
        tables.forEach(function (table) {
            jQuery(table).DataTable({
                paging: tableBlockEnhancer.paging,
                searching: tableBlockEnhancer.searching,
                ordering: tableBlockEnhancer.ordering,
            });
        });
    }
});