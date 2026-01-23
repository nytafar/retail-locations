/**
 * Retail Locations - Admin JavaScript
 */
(function($) {
    'use strict';

    var Admin = {
        map: null,
        marker: null,

        init: function() {
            this.initMap();
            this.bindEvents();
            this.initRepeaterFields();
        },

        initMap: function() {
            var $mapPreview = $('#location-map-preview');
            if (!$mapPreview.length || typeof google === 'undefined') return;

            var lat = parseFloat($('#location_lat').val()) || 59.9139;
            var lng = parseFloat($('#location_lng').val()) || 10.7522;

            this.map = new google.maps.Map($mapPreview[0], {
                zoom: 14,
                center: { lat: lat, lng: lng }
            });

            if ($('#location_lat').val() && $('#location_lng').val()) {
                this.setMarker({ lat: lat, lng: lng });
            }

            var self = this;
            this.map.addListener('click', function(e) {
                self.setMarker(e.latLng);
                $('#location_lat').val(e.latLng.lat().toFixed(6));
                $('#location_lng').val(e.latLng.lng().toFixed(6));
            });
        },

        setMarker: function(position) {
            if (this.marker) {
                this.marker.setPosition(position);
            } else {
                this.marker = new google.maps.Marker({
                    position: position,
                    map: this.map,
                    draggable: true
                });

                var self = this;
                this.marker.addListener('dragend', function() {
                    var pos = self.marker.getPosition();
                    $('#location_lat').val(pos.lat().toFixed(6));
                    $('#location_lng').val(pos.lng().toFixed(6));
                });
            }
            this.map.setCenter(position);
        },

        geocodeAddress: function() {
            var address = $('#location_address').val();
            if (!address || typeof google === 'undefined') return;

            var geocoder = new google.maps.Geocoder();
            var self = this;

            geocoder.geocode({ address: address }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    var location = results[0].geometry.location;
                    self.setMarker(location);
                    $('#location_lat').val(location.lat().toFixed(6));
                    $('#location_lng').val(location.lng().toFixed(6));
                }
            });
        },

        bindEvents: function() {
            var self = this;

            $('#location_address').on('blur', function() {
                if (!$('#location_lat').val() || !$('#location_lng').val()) {
                    self.geocodeAddress();
                }
            });

            $('#location_lat, #location_lng').on('change', function() {
                var lat = parseFloat($('#location_lat').val());
                var lng = parseFloat($('#location_lng').val());
                if (lat && lng && self.map) {
                    self.setMarker({ lat: lat, lng: lng });
                }
            });
        },

        initRepeaterFields: function() {
            var contactIndex = $('#location-contacts .contact-row').length;
            var hoursIndex = $('#location-hours .hours-row').length;

            $('#add-contact').on('click', function() {
                var html = '<div class="contact-row">' +
                    '<input type="text" name="location_contacts[' + contactIndex + '][label]" placeholder="Label" />' +
                    '<input type="text" name="location_contacts[' + contactIndex + '][value]" placeholder="Value" />' +
                    '<input type="url" name="location_contacts[' + contactIndex + '][link]" placeholder="Link (optional)" />' +
                    '<button type="button" class="button remove-row">&times;</button>' +
                    '</div>';
                $('#location-contacts').append(html);
                contactIndex++;
            });

            $('#add-hours').on('click', function() {
                var html = '<div class="hours-row">' +
                    '<input type="text" name="location_hours[' + hoursIndex + '][day]" placeholder="Day" />' +
                    '<input type="text" name="location_hours[' + hoursIndex + '][open]" placeholder="Open" />' +
                    '<input type="text" name="location_hours[' + hoursIndex + '][close]" placeholder="Close" />' +
                    '<button type="button" class="button remove-row">&times;</button>' +
                    '</div>';
                $('#location-hours').append(html);
                hoursIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('.contact-row, .hours-row').remove();
            });
        }
    };

    $(document).ready(function() {
        Admin.init();
    });

})(jQuery);
