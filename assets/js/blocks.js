/**
 * Retail Locations - Gutenberg Blocks
 */
(function(blocks, element, components, blockEditor, i18n) {
    'use strict';

    var el = element.createElement;
    var __ = i18n.__;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;

    // Map Block
    blocks.registerBlockType('retail-locations/map', {
        title: __('Locations Map', 'retail-locations'),
        icon: 'location-alt',
        category: 'widgets',
        attributes: {
            category: { type: 'string', default: '' },
            width: { type: 'string', default: '100%' },
            height: { type: 'string', default: '500px' },
            map_controls: { type: 'string', default: 'yes' },
            scrollwheel: { type: 'string', default: 'no' },
            mobile_draggable: { type: 'string', default: 'no' },
            sticky: { type: 'string', default: 'no' }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', { className: 'retail-locations-block-wrapper' },
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Map Settings', 'retail-locations'), initialOpen: true },
                        el(TextControl, {
                            label: __('Category Slug', 'retail-locations'),
                            value: attributes.category,
                            onChange: function(val) { setAttributes({ category: val }); }
                        }),
                        el(TextControl, {
                            label: __('Width', 'retail-locations'),
                            value: attributes.width,
                            onChange: function(val) { setAttributes({ width: val }); }
                        }),
                        el(TextControl, {
                            label: __('Height', 'retail-locations'),
                            value: attributes.height,
                            onChange: function(val) { setAttributes({ height: val }); }
                        }),
                        el(ToggleControl, {
                            label: __('Map Controls', 'retail-locations'),
                            checked: attributes.map_controls === 'yes',
                            onChange: function(val) { setAttributes({ map_controls: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Scrollwheel Zoom', 'retail-locations'),
                            checked: attributes.scrollwheel === 'yes',
                            onChange: function(val) { setAttributes({ scrollwheel: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Mobile Draggable', 'retail-locations'),
                            checked: attributes.mobile_draggable === 'yes',
                            onChange: function(val) { setAttributes({ mobile_draggable: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Sticky Position', 'retail-locations'),
                            checked: attributes.sticky === 'yes',
                            onChange: function(val) { setAttributes({ sticky: val ? 'yes' : 'no' }); },
                            help: __('Map stays fixed while scrolling the list', 'retail-locations')
                        })
                    )
                ),
                el('div', {
                    className: 'retail-locations-map-preview',
                    style: {
                        width: '100%',
                        height: '200px',
                        backgroundColor: '#e8e8e8',
                        border: '2px dashed #ccc',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        flexDirection: 'column'
                    }
                },
                    el('span', { className: 'dashicons dashicons-location-alt', style: { fontSize: '48px', color: '#666' } }),
                    el('p', { style: { margin: '10px 0 0', color: '#666' } }, __('Locations Map', 'retail-locations')),
                    el('small', { style: { color: '#999' } }, attributes.width + ' × ' + attributes.height)
                )
            );
        },

        save: function() {
            return null; // Server-side render
        }
    });

    // Stores List Block
    blocks.registerBlockType('retail-locations/stores', {
        title: __('Locations List', 'retail-locations'),
        icon: 'list-view',
        category: 'widgets',
        attributes: {
            category: { type: 'string', default: '' },
            posts_per_page: { type: 'string', default: '-1' },
            layout: { type: 'string', default: 'fullwidth' },
            show_hours: { type: 'string', default: 'yes' },
            show_contact: { type: 'string', default: 'yes' },
            show_description: { type: 'string', default: 'yes' },
            show_image: { type: 'string', default: 'yes' },
            group_by_category: { type: 'string', default: 'no' },
            group_by_area: { type: 'string', default: 'no' }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', { className: 'retail-locations-block-wrapper' },
                el(InspectorControls, {},
                    el(PanelBody, { title: __('List Settings', 'retail-locations'), initialOpen: true },
                        el(TextControl, {
                            label: __('Category Slug', 'retail-locations'),
                            value: attributes.category,
                            onChange: function(val) { setAttributes({ category: val }); }
                        }),
                        el(TextControl, {
                            label: __('Posts Per Page', 'retail-locations'),
                            value: attributes.posts_per_page,
                            onChange: function(val) { setAttributes({ posts_per_page: val }); },
                            help: __('-1 for all posts', 'retail-locations')
                        }),
                        el(SelectControl, {
                            label: __('Layout', 'retail-locations'),
                            value: attributes.layout,
                            options: [
                                { label: 'Full Width', value: 'fullwidth' },
                                { label: 'Grid 2', value: 'grid2' },
                                { label: 'Grid 3', value: 'grid3' },
                                { label: 'Grid 4', value: 'grid4' }
                            ],
                            onChange: function(val) { setAttributes({ layout: val }); }
                        })
                    ),
                    el(PanelBody, { title: __('Display Options', 'retail-locations'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Show Image', 'retail-locations'),
                            checked: attributes.show_image === 'yes',
                            onChange: function(val) { setAttributes({ show_image: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Description', 'retail-locations'),
                            checked: attributes.show_description === 'yes',
                            onChange: function(val) { setAttributes({ show_description: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Contact', 'retail-locations'),
                            checked: attributes.show_contact === 'yes',
                            onChange: function(val) { setAttributes({ show_contact: val ? 'yes' : 'no' }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Hours', 'retail-locations'),
                            checked: attributes.show_hours === 'yes',
                            onChange: function(val) { setAttributes({ show_hours: val ? 'yes' : 'no' }); }
                        })
                    ),
                    el(PanelBody, { title: __('Grouping', 'retail-locations'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Group by Area', 'retail-locations'),
                            checked: attributes.group_by_area === 'yes',
                            onChange: function(val) { setAttributes({ group_by_area: val ? 'yes' : 'no' }); },
                            help: __('Group stores by geographic area', 'retail-locations')
                        }),
                        el(ToggleControl, {
                            label: __('Group by Category', 'retail-locations'),
                            checked: attributes.group_by_category === 'yes',
                            onChange: function(val) { setAttributes({ group_by_category: val ? 'yes' : 'no' }); }
                        })
                    )
                ),
                el('div', {
                    className: 'retail-locations-list-preview',
                    style: {
                        width: '100%',
                        minHeight: '150px',
                        backgroundColor: '#f9f9f9',
                        border: '1px solid #ddd',
                        padding: '20px',
                        borderRadius: '4px'
                    }
                },
                    el('span', { className: 'dashicons dashicons-list-view', style: { fontSize: '32px', color: '#666' } }),
                    el('p', { style: { fontWeight: 'bold', margin: '10px 0 5px' } }, __('Locations List', 'retail-locations')),
                    el('small', { style: { color: '#666' } },
                        attributes.group_by_area === 'yes' ? __('Grouped by Area', 'retail-locations') :
                        attributes.group_by_category === 'yes' ? __('Grouped by Category', 'retail-locations') :
                        __('Layout: ', 'retail-locations') + attributes.layout
                    )
                )
            );
        },

        save: function() {
            return null; // Server-side render
        }
    });

})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.i18n);
