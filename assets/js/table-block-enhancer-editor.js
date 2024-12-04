(function (wp) {
    const { registerPlugin } = wp.plugins;
    const { withSelect, withDispatch } = wp.data;
    const { Fragment } = wp.element;
    const { addFilter } = wp.hooks;

    // Add a custom class to the Table block's save element for targeting.
    addFilter(
        'blocks.getSaveElement',
        'table-block-enhancer/add-class',
        (element, blockType) => {
            if (blockType.name === 'core/table') {
                return wp.element.cloneElement(element, {
                    className: `${element.props.className || ''} enhanced-table`
                });
            }
            return element;
        }
    );

    // Hook to extend functionality on the frontend.
    document.addEventListener('DOMContentLoaded', function () {
        const tables = document.querySelectorAll('.wp-block-table.enhanced-table table');
        tables.forEach((table) => {
            // Apply DataTables to each table.
            jQuery(table).DataTable({
                paging: true,
                searching: true,
                ordering: true
            });
        });
    });

    // Register the plugin (placeholder for further customization if needed).
    registerPlugin('table-block-enhancer', {
        render: () => null,
    });
})(window.wp);