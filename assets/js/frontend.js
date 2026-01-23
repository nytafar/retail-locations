(function ($) {
    'use strict';

    window.RetailLocations = {
        maps: [],
        markers: [],
        markersMap: {},
        geocoder: null,
        geocodeQueue: [],
        isGeocoding: false,
        mapId: null, // Will be set from localized data

        init: async function () {
            this.mapId = retailLocations.mapId || 'DEMO_MAP_ID';
            await this.initMaps();
            this.bindEvents();
        },

        initMaps: async function () {
            var self = this;
            var $maps = $('.retail-locations-map');

            if (!$maps.length) return;

            // Load libraries
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            self.AdvancedMarkerElement = AdvancedMarkerElement; // Store for later use

            $maps.each(function () {
                var $container = $(this);
                if ($container.data('initialized')) return;

                var settings = $container.data('settings') || {};
                var $canvas = $container.find('.retail-locations-map-canvas');

                if (!$canvas.length) return;

                var mapOptions = {
                    zoom: 12,
                    center: { lat: 59.9139, lng: 10.7522 },
                    disableDefaultUI: !settings.mapControls,
                    scrollwheel: settings.scrollwheel,
                    draggable: self.isMobile() ? settings.mobileDraggable : true,
                    mapId: self.mapId, // Required for AdvancedMarkerElement
                };

                var map = new Map($canvas[0], mapOptions);
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
            var hasBounds = false;

            markersData.forEach(function (data) {
                // Determine position or queue for geocoding
                if (data.lat && data.lng) {
                    self.createMarker(map, data, bounds);
                    hasBounds = true;
                } else if (data.address) {
                    // No coords, but address exists. Queue it.
                    self.queueGeocode(map, data);
                }
            });

            if (hasBounds) {
                map.fitBounds(bounds);
                // Avoid too much zoom if only one marker
                var listener = google.maps.event.addListener(map, "idle", function () {
                    if (map.getZoom() > 15) map.setZoom(15);
                    google.maps.event.removeListener(listener);
                });
            }
        },

        createMarker: function (map, data, bounds) {
            var self = this;
            var position = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) };

            // AdvancedMarkerElement usage
            var marker = new self.AdvancedMarkerElement({
                map: map,
                position: position,
                title: data.title,
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
                    if (hour.day || hour.open) {
                        hoursHtml += '<div class="retail-locations-info-hour">' +
                            '<span class="hour-day">' + hour.day + '</span> ' +
                            '<span class="hour-time">' + hour.open + ' – ' + hour.close + '</span>' +
                            '</div>';
                    }
                });
                hoursHtml += '</div>';
            }

            // Directions link
            var directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(data.address || (data.lat + ',' + data.lng));

            var infoContent = '<div class="retail-locations-info">' +
                '<h4>' + data.title + '</h4>' +
                categoryHtml +
                (data.address ? '<p class="retail-locations-info-address">' + data.address + '</p>' : '') +
                hoursHtml +
                '<div class="retail-locations-info-actions">' +
                '<a href="' + directionsUrl + '" class="retail-locations-info-link" target="_blank" rel="noopener noreferrer">' + 'Get Directions' + '</a>' +
                (data.link ? ' <a href="' + data.link + '" class="retail-locations-info-link-secondary" style="margin-left:8px;font-size:0.9em;">' + 'View Details' + '</a>' : '') +
                '</div>' +
                '</div>';

            var infoWindow = new google.maps.InfoWindow({
                content: infoContent,
                maxWidth: 320,
                ariaLabel: data.title
            });

            marker.addListener('click', function () {
                self.closeAllInfoWindows();
                infoWindow.open({
                    anchor: marker,
                    map: map,
                });
            });

            self.markers.push({ marker: marker, infoWindow: infoWindow });

            // Store by ID for external access
            if (data.id) {
                self.markersMap[data.id] = { marker: marker, infoWindow: infoWindow };
            }

            if (bounds) {
                bounds.extend(position);
            }
        },

        queueGeocode: function (map, data) {
            this.geocodeQueue.push({ map: map, data: data });
            this.processGeocodeQueue();
        },

        processGeocodeQueue: function () {
            var self = this;
            if (self.isGeocoding || self.geocodeQueue.length === 0) return;

            self.isGeocoding = true;
            var item = self.geocodeQueue.shift();

            if (!self.geocoder) {
                self.geocoder = new google.maps.Geocoder();
            }

            self.geocoder.geocode({ 'address': item.data.address }, function (results, status) {
                if (status === 'OK') {
                    var location = results[0].geometry.location;
                    item.data.lat = location.lat();
                    item.data.lng = location.lng();

                    // Create marker seamlessly
                    self.createMarker(item.map, item.data, null);
                } else {
                    console.warn('Geocode was not successful for the following reason: ' + status);
                }

                // Rate limiting - wait a bit before next request
                setTimeout(function () {
                    self.isGeocoding = false;
                    self.processGeocodeQueue();
                }, 600);
            });
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

        openMarker: function (id) {
            var item = this.markersMap[id];
            if (item) {
                var map = item.marker.map; // AdvancedMarkerElement property
                map.panTo(item.marker.position); // AdvancedMarkerElement position property
                map.setZoom(15);
                google.maps.event.trigger(item.marker, 'click');
            }
        },

        isMobile: function () {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },

        animateAccordion: function ($content, expanding, callback) {
            if (expanding) {
                $content.css('height', '0');
                var currentStyle = $content.attr('style');
                $content.css({ position: 'absolute', visibility: 'hidden', height: 'auto', display: 'block' });
                var targetHeight = $content.outerHeight();
                $content.attr('style', currentStyle || '');
                $content.css('height', '0');
                $content[0].offsetHeight;
                $content.css('height', targetHeight + 'px');
                setTimeout(function () {
                    $content.css('height', 'auto');
                    if (callback) callback();
                }, 350);
            } else {
                var currentHeight = $content.outerHeight();
                $content.css('height', currentHeight + 'px');
                $content[0].offsetHeight;
                $content.css('height', 0);
                setTimeout(function () {
                    if (callback) callback();
                }, 350);
            }
        },

        bindEvents: function () {
            var self = this;

            $(document).on('click', '.retail-locations-group-title[data-lat], .js-focus-location', function (e) {
                var $el = $(this);
                var $list = $el.closest('.retail-locations-list');

                if ($el.hasClass('retail-locations-group-title') && $list.attr('data-collapsible') === 'yes') {
                    return;
                }

                e.preventDefault();

                var id = $el.data('id');
                if (id && self.markersMap[id]) {
                    self.openMarker(id);
                } else if ($el.data('lat') && $el.data('lng')) {
                    self.focusArea($el.data('lat'), $el.data('lng'), $el.data('zoom'));
                } else if ($el.data('address')) {
                    // Start geocode process handled in addMarkers queue?
                    // If it's in the list, it's already queued. 
                    // We can just wait a bit or alert user?
                    // Silently try to find it after a delay.
                    if (id) {
                        $el.css('opacity', '0.5'); // visual feedback?
                        var check = setInterval(function () {
                            if (self.markersMap[id]) {
                                clearInterval(check);
                                $el.css('opacity', '1');
                                self.openMarker(id);
                            }
                        }, 500);
                        // timeout after 5s
                        setTimeout(function () { clearInterval(check); $el.css('opacity', '1'); }, 5000);
                    }
                }
            });

            $(document).on('click keydown', '.retail-locations-list[data-collapsible="yes"] .retail-locations-group-title', function (e) {
                if (e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32) return;
                if (e.type === 'keydown') e.preventDefault();

                var $title = $(this);
                var $group = $title.closest('.retail-locations-group');
                var $content = $group.find('.retail-locations-group-content');
                var $list = $group.closest('.retail-locations-list');
                var isExclusive = $list.attr('data-exclusive') === 'yes';
                var isCollapsed = $group.hasClass('is-collapsed');
                var willExpand = isCollapsed;

                if (isExclusive && willExpand) {
                    $list.find('.retail-locations-group').not($group).each(function () {
                        var $otherGroup = $(this);
                        if (!$otherGroup.hasClass('is-collapsed')) {
                            self.animateAccordion($otherGroup.find('.retail-locations-group-content'), false, function () {
                                $otherGroup.addClass('is-collapsed');
                            });
                            $otherGroup.find('.retail-locations-group-title').attr('aria-expanded', 'false');
                        }
                    });
                }

                // Always focus map if coordinates exist, regardless of expand/collapse state
                if ($title.data('lat') && $title.data('lng')) {
                    self.focusArea($title.data('lat'), $title.data('lng'), $title.data('zoom'));
                }

                if (willExpand) {
                    $group.removeClass('is-collapsed');
                    $title.attr('aria-expanded', 'true');
                    self.animateAccordion($content, true);
                } else {
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
