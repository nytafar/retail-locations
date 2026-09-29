=== Kaupang Retail Locations ===
Contributors: lassejellum
Tags: store locator, google maps, locations, stores, map
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display retail locations on a Google Map with filtering by category and area.

== Description ==

Retail Locations is a clean, professional store locator plugin designed for businesses that need to display their physical locations on an interactive Google Map. Built with simplicity and performance in mind, it provides all the essential features without bloat.

== Installation ==

1. Upload the `kaupang-retail-locations` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Retail Locations → Settings**
4. Add your Google Maps API Key ([Get one here](https://developers.google.com/maps/documentation/javascript/get-api-key))
5. Enable the **Geocoding API** in your Google Cloud Console
6. Start adding locations!

= Google Maps API Setup =

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable these APIs: Maps JavaScript API and Geocoding API
4. Create credentials (API Key)
5. Copy the API key to plugin settings

== Frequently Asked Questions ==

**Do I need a Google Maps API key?**
Yes. The plugin requires a Google Maps API key to display maps and geocode addresses.

**Can I import existing locations?**
Yes. Use WordPress's built-in XML import/export functionality.

**Does it work with page builders?**
Yes. Use the shortcodes in any page builder that supports them, or use the Gutenberg blocks.

**Can I customize the appearance?**
Yes. The plugin uses standard WordPress classes and can be styled with custom CSS.

**Is it translation-ready?**
Yes. The plugin uses the `kaupang-retail-locations` text domain and is fully translatable.

**Can I group locations by region?**
Yes. Use the Areas taxonomy to create geographic regions, then use `group_by_area="yes"` in the shortcode or enable it in the block settings.

**How do I make the map sticky?**
In the Gutenberg block, enable "Sticky Position" in the Map Settings panel. For shortcodes, add `sticky="yes"` to the `[retail_locations_map]` shortcode.

**Can I filter locations by category?**
Yes. Add `category="your-category-slug"` to either the map or stores shortcode/block.

== Screenshots ==

1. Location edit screen with map preview and geocoding
2. Locations Map block in Gutenberg editor
3. Locations List block with layout options
4. Frontend display with sticky map and grouped locations
5. Mobile-responsive location list
6. Settings page with Google Maps API configuration

== Changelog ==

= 3.0.0 – 2026-09-29 =

Renamed Retail Locations → **Kaupang Retail Locations**. Never deployed to production (libraluxe.soppify.no is retired), so
identity moved with no back-compat aliases; the content and front-end contract stays frozen. The myrvann theme already
listens on the new hook name; kaupang-wholesale already reads the key through the new seam.

**Added**
* Filter `kaupang/retail-locations/maps_api_key`: the one place the plugin reads its Google Maps key, the seam
  kaupang-wholesale reads it through, and the plugin provides the stored `retail_locations_api_key` to any consumer that
  filters with an empty value.

**Changed**
* **Breaking — moved:** folder/main file/text domain `kaupang-retail-locations` (languages files renamed, block-editor JSON
  regenerated), constants `KAUPANG_RETAIL_LOCATIONS_*`, class `Kaupang_Retail_Locations` (`includes/class-kaupang-retail-locations.php`),
  functions `kaupang_retail_locations()` and `kaupang_retail_locations_{render_svg_symbols,get_icon_markup,icon_*}()`,
  public hooks `retail_locations_{enable_single_pages,has_details,show_view_details}` → `kaupang/retail-locations/*`,
  admin nonces `kaupang_retail_locations_{export,import}_taxonomies` and `kaupang_retail_location_meta`/`_nonce`,
  admin handles `kaupang-retail-locations-{admin,blocks}`, internal flag `kaupang_retail_locations_flush_rewrite`
  (activation deletes the old `retail_locations_flush_rewrite` row). Header per suite standard (GitHub Plugin URI,
  `@package Kaupang\RetailLocations`); no WooCommerce headers, the plugin is Woo-independent.
* **Kept (frozen contract):** block names `retail-locations/{map,stores}`, shortcodes `[retail_locations_map]`/`[retail_locations]`,
  nopriv AJAX `retail_locations_get_stores`, options `retail_locations_{api_key,slug,map_id}` and settings group `retail_locations`,
  CPT `retail_location`, taxonomies, `_location_*` meta, URL slugs, front-end handle `retail-locations` and localized
  `retailLocations`, `window.RetailLocations`, `.retail-locations-*`/`.retail-location-*`/`--rl-*` CSS, `#retail-icon-*` symbols,
  admin page slugs `retail-locations-{settings,export}`.

= 2.2.3 – 2026-09-29 =

**Added**
* Translatable plugin with POT and Norwegian (nb_NO) translations.
* Per-category map pins (store vs café), with a distinct pin for store + café locations.
* Conditional single-page display for locations.

**Changed**
* Location links restructured as explicit Google Maps and Apple Maps buttons; the info window shows them as "Kart: Google  Apple".
* "View Details" only shows when a location has details.
* The selected pin drops below the map centre so its info window fits.
* Admin styles follow the admin palette: `--rl-*` tokens read `--hat-*` with today's colours as fallbacks; the map preview background moved from an inline style to admin.css.
* Frontend CSS updates; sepia filter removed from the map canvas.
* Docs use the `retail_locations_` shortcode prefix (was `tsl_`).
* Adopted suite kit v2: version synced from the header, generated readme.txt, `tools/release`; standard header (GitHub Plugin URI, License GPL-2.0-or-later).
* Suite kit v2.1 (tooling, generated readme.txt); no runtime change.

**Fixed**
* Clicking an area label focuses the map again (auto-fit fallback).

**Removed**
* Experimental fullscreen map page template.
* Tracked `.DS_Store` files.

= 2.2.2 – 2026-01-23 =

**Added**
* Directions link in the info window; clicking a location title opens its pin.
* Client-side geocoding for address-only locations.
* Apple Maps links on iPhone, iPad and Mac, with robust Apple device detection.

**Changed**
* Markers use `AdvancedMarkerElement` and the modern Maps loader.
* SVG icons are symbols; simpler CSS.
* Compact links; the Instagram handle drops its "@"; clicking a title focuses the map.

**Fixed**
* Clicking an area header always focuses the map, also when collapsing its accordion.

= 2.2.1 – 2026-01-23 =

**Fixed**
* Map markers failed to load because of the data structure (hours and categories are now always arrays).

= 2.2.0 – 2026-01-23 =

**Fixed**
* Map markers failed to load because of the data structure.
* Compatibility with non-sequential category IDs.
* UI and alignment bugs, and a PHP fatal error.

= 2.1.0 – 2026-01-23 =

**Added**
* Collapsible accordion groups.

= 2.0.0 =

**Changed**
* Complete rebuild focused on simplicity and business needs.
* Added Gutenberg blocks with live preview.
* Added sticky map positioning option.
* Added clickable Google Maps links for addresses.
* Added category display in location listings.
* Fixed phantom item bug in grouped displays.
* Improved asset cache busting for instant updates.
* Enhanced mobile responsiveness.
* Streamlined admin interface.
* Performance optimizations.

= 1.4.0 =

**Changed**
* Fixed post type and taxonomy registration.
* Improved admin menu structure.
* Standardized text domain to retail-locations.
* Added comprehensive testing.

= 1.3.0 =

**Added**
* Geographic area taxonomy.
* Area-based grouping.
* Map focusing on area click.
* Enhanced store display templates.

== Upgrade Notice ==

= 2.0.0 =

Major update with new Gutenberg blocks, sticky maps, and improved UX.
