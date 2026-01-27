<?php
/**
 * Plugin Name: Retail Locations
 * Plugin URI: https://jellum.net/retail-locations
 * Description: Display retail locations on a Google Map with filtering by category and area.
 * Version: 2.2.2
 * Author: Lasse Jellum
 * Author URI: https://jellum.net
 * Text Domain: retail-locations
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RETAIL_LOCATIONS_VERSION', '2.2.2');
define('RETAIL_LOCATIONS_FILE', __FILE__);
define('RETAIL_LOCATIONS_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('RETAIL_LOCATIONS_URI', trailingslashit(plugin_dir_url(__FILE__)));

require_once RETAIL_LOCATIONS_DIR . 'includes/class-retail-locations.php';
require_once RETAIL_LOCATIONS_DIR . 'includes/icons.php';

function retail_locations()
{
    return Retail_Locations::get_instance();
}

add_action('plugins_loaded', 'retail_locations');

register_activation_hook(__FILE__, function () {
    update_option('retail_locations_flush_rewrite', true);
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
