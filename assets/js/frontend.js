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

                // Build category tags
                var categoryHtml = '';
                if (data.categories && data.categories.length > 0) {
                    categoryHtml = '<div class="retail-locations-info-tags">';
                    data.categories.forEach(function (cat) {
                        categoryHtml += '<span class="retail-locations-info-tag">' + cat + '</span>';
                    });
                    categoryHtml += '</div>';
                }

                // Build hours
                var hoursHtml = '';
                if (data.hours && data.hours.length > 0) {
                    hoursHtml = '<div class="retail-locations-info-hours">';
                    data.hours.forEach(function (hour) {
                        if (hour.day || hour.open) { // Only show if data exists
                            hoursHtml += '<div class="retail-locations-info-hour">' +
                                '<span class="hour-day">' + hour.day + '</span> ' +
                                '<span class="hour-time">' + hour.open + ' – ' + hour.close + '</span>' +
                                '</div>';
                        }
                    });
                    hoursHtml += '</div>';
                }

                var infoContent = '<div class="retail-locations-info">' +
                    '<h4>' + data.title + '</h4>' +
                    categoryHtml +
                    (data.address ? '<p class="retail-locations-info-address">' + data.address + '</p>' : '') +
                    hoursHtml +
                    '<a href="' + data.link + '" class="retail-locations-info-link">' + 'View Details →' + '</a>' +
                    '</div>';

                var infoWindow = new google.maps.InfoWindow({
                    content: infoContent,
                    maxWidth: 320
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

        // Animate accordion open/close with smooth height transition
        animateAccordion: function ($content, expanding, callback) {
            if (expanding) {
                // Expanding: behavior depends on current state
                // If it was "hidden" via class, we need to prepare it for animation
                $content.css('height', '0');

                // Get the natural height by temporarily setting auto
                var currentStyle = $content.attr('style');
                $content.css({ position: 'absolute', visibility: 'hidden', height: 'auto', display: 'block' });
                var targetHeight = $content.outerHeight();

                // Reset to start state
                $content.attr('style', currentStyle || '');
                $content.css('height', '0');
                // Ensure display block is set if the class removal didn't trigger it yet
                // But we handle class removal in the caller usually.
                // If CSS hides it via display:none, we need to show it.
                // Our CSS currently uses height:0, so it's already "visible" but 0 height.

                // Force reflow
                $content[0].offsetHeight;

                // Animate
                $content.css('height', targetHeight + 'px');

                setTimeout(function () {
                    $content.css('height', 'auto');
                    if (callback) callback();
                }, 350);
            } else {
                // Collapsing
                var currentHeight = $content.outerHeight();
                $content.css('height', currentHeight + 'px');
                $content[0].offsetHeight; // Force reflow
                $content.css('height', 0);
                setTimeout(function () {
                    if (callback) callback();
                }, 350);
            }
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
                var $content = $group.find('.retail-locations-group-content');
                var $list = $group.closest('.retail-locations-list');
                var isExclusive = $list.attr('data-exclusive') === 'yes';
                var isCollapsed = $group.hasClass('is-collapsed');
                var willExpand = isCollapsed;

                // If exclusive mode and we're expanding, collapse all others first
                if (isExclusive && willExpand) {
                    $list.find('.retail-locations-group').not($group).each(function () {
                        var $otherGroup = $(this);
                        if (!$otherGroup.hasClass('is-collapsed')) {
                            // Collapse other group
                            self.animateAccordion($otherGroup.find('.retail-locations-group-content'), false, function () {
                                $otherGroup.addClass('is-collapsed');
                            });
                            $otherGroup.find('.retail-locations-group-title').attr('aria-expanded', 'false');
                        }
                    });
                }

                if (willExpand) {
                    // EXPANDING:
                    // 1. Remove collapsed class immediately so content is theoretically visible (height: auto via CSS if not overwritten)
                    // But our JS animateAccordion handles setting it to 0 first.
                    $group.removeClass('is-collapsed');
                    $title.attr('aria-expanded', 'true');
                    self.animateAccordion($content, true);

                    // If this has map coordinates, focus the map
                    if ($title.data('lat') && $title.data('lng')) {
                        self.focusArea($title.data('lat'), $title.data('lng'), $title.data('zoom'));
                    }
                } else {
                    // COLLAPSING:
                    // 1. Animate to 0
                    // 2. Add class after animation
                    $title.attr('aria-expanded', 'false');
                    self.animateAccordion($content, false, function () {
                        $group.addClass('is-collapsed');
                    });
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
