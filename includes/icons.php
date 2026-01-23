<?php
/**
 * SVG Icon helpers for Retail Locations
 */

if (!defined('ABSPATH')) {
    exit;
}

<?php
/**
 * SVG Icon helpers for Retail Locations
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output SVG symbols footer
 */
function retail_locations_render_svg_symbols()
{
    ?>
    <svg style="display: none;" aria-hidden="true" width="0" height="0">
        <defs>
            <symbol id="retail-icon-map" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </symbol>
            <symbol id="retail-icon-website" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </symbol>
            <symbol id="retail-icon-instagram" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </symbol>
            <symbol id="retail-icon-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </symbol>
            <symbol id="retail-icon-email" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </symbol>
            <symbol id="retail-icon-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </symbol>
        </defs>
    </svg>
    <?php
}

/**
 * Helper to get SVG icon markup referencing symbol
 */
function retail_locations_get_icon_markup($id, $size = '1em') {
    return '<svg class="retail-locations-icon retail-locations-icon--' . esc_attr(str_replace('retail-icon-', '', $id)) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" aria-hidden="true"><use href="#' . esc_attr($id) . '"></use></svg>';
}

function retail_locations_icon_map($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-map', $size);
}

function retail_locations_icon_website($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-website', $size);
}

function retail_locations_icon_instagram($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-instagram', $size);
}

function retail_locations_icon_phone($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-phone', $size);
}

function retail_locations_icon_email($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-email', $size);
}

function retail_locations_icon_clock($size = '1em') {
    return retail_locations_get_icon_markup('retail-icon-clock', $size);
}
