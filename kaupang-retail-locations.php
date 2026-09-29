<?php
/**
 * Plugin Name: Kaupang Retail Locations
 * Plugin URI:  https://github.com/nytafar/kaupang-retail-locations
 * Description: Display retail locations on a Google Map with filtering by category and area.
 * Version: 2.2.3
 * Author: Lasse Jellum
 * Author URI: https://jellum.net
 * Text Domain: kaupang-retail-locations
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 *
 * @package Kaupang\RetailLocations
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KAUPANG_RETAIL_LOCATIONS_VERSION', '2.2.3');
define('KAUPANG_RETAIL_LOCATIONS_FILE', __FILE__);
define('KAUPANG_RETAIL_LOCATIONS_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('KAUPANG_RETAIL_LOCATIONS_URI', trailingslashit(plugin_dir_url(__FILE__)));

require_once KAUPANG_RETAIL_LOCATIONS_DIR . 'includes/class-kaupang-retail-locations.php';
require_once KAUPANG_RETAIL_LOCATIONS_DIR . 'includes/icons.php';

function kaupang_retail_locations()
{
    return Kaupang_Retail_Locations::get_instance();
}

add_action('plugins_loaded', 'kaupang_retail_locations');

register_activation_hook(__FILE__, function () {
    delete_option('retail_locations_flush_rewrite'); // pre-3.0.0 flag
    update_option('kaupang_retail_locations_flush_rewrite', true);
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
