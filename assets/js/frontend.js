/**
 * Retail Locations - Frontend JavaScript
 */
(function ($) {
    'use strict';

    window.RetailLocations = {
        maps: [],
        markers: [],

        init: function () {
            this.initMaps();
            this.bindEvents();
        },

        initMaps: function () {
            var self = this;
            $('.retail-locations-map').each(function () {
                var $container = $(this);
                if ($container.data('initialized')) return;

                var settings = $container.data('settings') || {};
                var $canvas = $container.find('.retail-locations-map-canvas');

                if (!$canvas.length || typeof google === 'undefined') return;

                var mapOptions = {
                    zoom: 12,
                    center: { lat: 59.9139, lng: 10.7522 },
                    disableDefaultUI: !settings.mapControls,
                    scrollwheel: settings.scrollwheel,
                    draggable: self.isMobile() ? settings.mobileDraggable : true,
                };

                var map = new google.maps.Map($canvas[0], mapOptions);
                self.maps.push(map);
                $container.data('map', map);
                $container.data('initialized', true);

                self.loadMarkers(map, settings.category);
            });
        },

        loadMarkers: function (map, category) {
            var self = this;

            $.ajax({
                url: retailLocations.ajaxurl,
                type: 'POST',
                data: {
                    action: 'retail_locations_get_stores',
                    category: category || ''
                },
                success: function (response) {
                    if (response.success && response.data.markers) {
                        self.addMarkers(map, response.data.markers);
                    }
                }
            });
        },

        addMarkers: function (map, markersData) {
            var self = this;
            var bounds = new google.maps.LatLngBounds();

            markersData.forEach(function (data) {
                var position = { lat: data.lat, lng: data.lng };

                var marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: data.title,
                    animation: google.maps.Animation.DROP
                });

                var infoContent = '<div class="retail-locations-info">' +
                    '<h4>' + data.title + '</h4>' +
                    (data.address ? '<p>' + data.address + '</p>' : '') +
                    '<a href="' + data.link + '">' + 'View Details' + '</a>' +
                    '</div>';

                var infoWindow = new google.maps.InfoWindow({
                    content: infoContent,
                    maxWidth: 300
                });

                marker.addListener('click', function () {
                    self.closeAllInfoWindows();
                    infoWindow.open(map, marker);
                });

                self.markers.push({ marker: marker, infoWindow: infoWindow });
                bounds.extend(position);
            });

            if (markersData.length > 0) {
                map.fitBounds(bounds);
                if (markersData.length === 1) {
                    map.setZoom(15);
                }
            }
        },

        closeAllInfoWindows: function () {
            this.markers.forEach(function (item) {
                item.infoWindow.close();
            });
        },

        focusArea: function (lat, lng, zoom) {
            if (this.maps.length > 0) {
                var map = this.maps[0];
                map.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
                map.setZoom(parseInt(zoom) || 12);
            }
        },

        isMobile: function () {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },

        bindEvents: function () {
            var self = this;

            // Area focus click (only when not in collapsible mode)
            $(document).on('click', '.retail-locations-group-title[data-lat]', function (e) {
                var $list = $(this).closest('.retail-locations-list');
                // If collapsible, the accordion handler takes precedence - don't focus map on accordion toggle
                if ($list.attr('data-collapsible') === 'yes') {
                    return; // Let the accordion handler below deal with it
                }
                var $el = $(this);
                self.focusArea($el.data('lat'), $el.data('lng'), $el.data('zoom'));
            });

            // Accordion toggle for collapsible groups
            $(document).on('click keydown', '.retail-locations-list[data-collapsible="yes"] .retail-locations-group-title', function (e) {
                // For keydown, only respond to Enter or Space
                if (e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32) {
                    return;
                }
                if (e.type === 'keydown') {
                    e.preventDefault();
                }

                var $title = $(this);
                var $group = $title.closest('.retail-locations-group');
                var $list = $group.closest('.retail-locations-list');
                var isExclusive = $list.attr('data-exclusive') === 'yes';
                var isCollapsed = $group.hasClass('is-collapsed');

                // If exclusive mode and we're expanding, collapse all others first
                if (isExclusive && isCollapsed) {
                    $list.find('.retail-locations-group').not($group).each(function () {
                        var $otherGroup = $(this);
                        $otherGroup.addClass('is-collapsed');
                        $otherGroup.find('.retail-locations-group-title').attr('aria-expanded', 'false');
                    });
                }

                // Toggle the clicked group
                $group.toggleClass('is-collapsed');
                var nowExpanded = !$group.hasClass('is-collapsed');
                $title.attr('aria-expanded', nowExpanded ? 'true' : 'false');

                // If this has map coordinates and we're expanding, focus the map
                if (nowExpanded && $title.data('lat') && $title.data('lng')) {
                    self.focusArea($title.data('lat'), $title.data('lng'), $title.data('zoom'));
                }
            });
        }
    };

    $(document).ready(function () {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            RetailLocations.init();
        } else {
            $(window).on('load', function () {
                if (typeof google !== 'undefined') {
                    RetailLocations.init();
                }
            });
        }
    });

})(jQuery);
