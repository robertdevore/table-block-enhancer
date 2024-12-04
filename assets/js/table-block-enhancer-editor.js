(function (wp) {
    var addFilter = wp.hooks.addFilter;
    var createElement = wp.element.createElement;

    /**
     * Add new attributes to the Table block.
     *
     * @param {Object} settings The block settings.
     * @param {string} name     The block name.
     *
     * @return {Object} Modified block settings.
     */
    function addAttributes(settings, name) {
        if (name !== 'core/table') {
            return settings;
        }

        // Add new attributes without default values.
        settings.attributes = Object.assign({}, settings.attributes, {
            enablePaging: {
                type: 'boolean',
            },
            enableSearching: {
                type: 'boolean',
            },
            enableOrdering: {
                type: 'boolean',
            },
        });

        return settings;
    }
    addFilter(
        'blocks.registerBlockType',
        'table-block-enhancer/add-attributes',
        addAttributes
    );

    /**
     * Add controls to the Table block's Inspector panel.
     *
     * @param {Function} BlockEdit The original block edit component.
     *
     * @return {Function} Wrapped block edit component.
     */
    function addInspectorControls(BlockEdit) {
        return function (props) {
            if (props.name !== 'core/table') {
                return createElement(BlockEdit, props);
            }

            var Fragment = wp.element.Fragment;
            var InspectorControls = wp.blockEditor.InspectorControls || wp.editor.InspectorControls;
            var PanelBody = wp.components.PanelBody;
            var ToggleControl = wp.components.ToggleControl;

            // Use the attribute if set, otherwise default to true
            var enablePaging = props.attributes.enablePaging !== undefined ? props.attributes.enablePaging : true;
            var enableSearching = props.attributes.enableSearching !== undefined ? props.attributes.enableSearching : true;
            var enableOrdering = props.attributes.enableOrdering !== undefined ? props.attributes.enableOrdering : true;

            // Return the modified block edit component.
            return createElement(
                Fragment,
                {},
                createElement(BlockEdit, props),
                createElement(
                    InspectorControls,
                    {},
                    createElement(
                        PanelBody,
                        { title: 'Table Enhancer Settings', initialOpen: true },
                        createElement(ToggleControl, {
                            label: 'Enable Paging',
                            checked: enablePaging,
                            onChange: function (value) {
                                props.setAttributes({ enablePaging: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: 'Enable Searching',
                            checked: enableSearching,
                            onChange: function (value) {
                                props.setAttributes({ enableSearching: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: 'Enable Ordering',
                            checked: enableOrdering,
                            onChange: function (value) {
                                props.setAttributes({ enableOrdering: value });
                            },
                        })
                    )
                )
            );
        };
    }
    addFilter(
        'editor.BlockEdit',
        'table-block-enhancer/add-inspector-controls',
        addInspectorControls
    );

    /**
     * Add custom classes to the block's saved content.
     *
     * @param {Object} extraProps Additional properties applied to the block's save element.
     * @param {Object} blockType  The block type.
     * @param {Object} attributes The block's attributes.
     *
     * @return {Object} Modified extra properties.
     */
    function addCustomClassName(extraProps, blockType, attributes) {
        if (blockType.name !== 'core/table') {
            return extraProps;
        }
    
        var className = extraProps.className || '';
    
        // Only add classes if the attribute is set
        if (attributes.enablePaging !== undefined) {
            if (attributes.enablePaging === true) {
                className += ' enable-paging';
            } else {
                className += ' disable-paging';
            }
        }
    
        if (attributes.enableSearching !== undefined) {
            if (attributes.enableSearching === true) {
                className += ' enable-searching';
            } else {
                className += ' disable-searching';
            }
        }
    
        if (attributes.enableOrdering !== undefined) {
            if (attributes.enableOrdering === true) {
                className += ' enable-ordering';
            } else {
                className += ' disable-ordering';
            }
        }
    
        extraProps.className = className;
    
        return extraProps;
    }
    addFilter(
        'blocks.getSaveContent.extraProps',
        'table-block-enhancer/add-custom-classname',
        addCustomClassName
    );
})(window.wp);