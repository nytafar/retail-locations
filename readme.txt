=== Retail Locations ===
Contributors: lassejellum
Tags: store locator, google maps, locations, stores, map
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimal, business-focused store locator plugin for real businesses. Display retail locations on Google Maps with smart filtering and organization.

== Description ==

**Retail Locations** is a clean, professional store locator plugin designed for businesses that need to display their physical locations on an interactive Google Map. Built with simplicity and performance in mind, it provides all the essential features without bloat.

= Why Retail Locations? =

Most store locator plugins are overcomplicated, slow, or designed for enterprise use cases. We built Retail Locations for **real businesses** who need:

* **Fast setup** - Add your Google Maps API key and start adding locations
* **Clean interface** - Intuitive admin UI that doesn't overwhelm
* **Flexible display** - Show locations as lists, grids, or on interactive maps
* **Smart organization** - Group by geographic area or business category
* **Modern blocks** - Full Gutenberg support with live previews
* **Mobile-ready** - Responsive design that works on all devices

= Key Features =

**Location Management**
* Custom post type for retail locations with full WordPress editor support
* Store address, coordinates (latitude/longitude), contact info, and business hours
* Featured images and rich descriptions
* Bulk import/export via WordPress XML

**Interactive Maps**
* Google Maps integration with customizable appearance
* Automatic geocoding from addresses
* Info windows with location details
* Click-to-focus on geographic areas
* Configurable zoom levels and map controls
* Mobile-friendly touch interactions

**Organization & Filtering**
* Categories - Organize by business type (cafes, retail, services, etc.)
* Geographic Areas - Group locations by city, region, or neighborhood
* Filter locations by category on maps and lists
* Hierarchical taxonomy support

**Display Options**
* Gutenberg Blocks - Locations Map and Locations List blocks with live preview
* Shortcodes - `[tsl_map]` and `[tsl_stores]` for classic editor
* Layout Options - Full width, 2-column, 3-column, or 4-column grids
* Sticky Maps - Keep map visible while scrolling through location list
* Grouped Display - Group by category or area with collapsible sections
* Customizable Fields - Show/hide images, descriptions, hours, contact info

**User Experience**
* Clickable Addresses - Direct links to Google Maps for navigation
* Category Display - Show location categories inline
* Area Focusing - Click area headers to center map on that region
* Responsive Design - Mobile-first CSS with touch-friendly controls

= Perfect For =

* Retail chains with multiple store locations
* Restaurant groups and franchises
* Service businesses with multiple offices
* Real estate agencies
* Healthcare providers with multiple clinics
* Any business with physical locations to showcase

== Installation ==

1. Upload the `retail-locations` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Retail Locations → Settings**
4. Add your Google Maps API Key ([Get one here](https://developers.google.com/maps/documentation/javascript/get-api-key))
5. Enable the **Geocoding API** in your Google Cloud Console
6. Start adding locations!

= Google Maps API Setup =

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable these APIs:
   * Maps JavaScript API
   * Geocoding API
4. Create credentials (API Key)
5. Copy the API key to plugin settings

== Frequently Asked Questions ==

= Do I need a Google Maps API key? =

Yes. The plugin requires a Google Maps API key to display maps and geocode addresses. Google provides a generous free tier that covers most small to medium business needs.

= Can I import existing locations? =

Yes. Use WordPress's built-in XML import/export functionality to migrate locations between sites.

= Does it work with page builders? =

Yes. Use the shortcodes in any page builder that supports them, or use the Gutenberg blocks in the block editor.

= Can I customize the appearance? =

Yes. The plugin uses standard WordPress classes and can be styled with custom CSS. Add your styles to your theme's stylesheet.

= Is it translation-ready? =

Yes. The plugin uses the `retail-locations` text domain and is fully translatable using standard WordPress translation tools.

= Can I group locations by region? =

Yes. Use the Areas taxonomy to create geographic regions, then use `group_by_area="yes"` in the shortcode or enable it in the block settings.

= How do I make the map sticky? =

In the Gutenberg block, enable "Sticky Position" in the Map Settings panel. For shortcodes, add `sticky="yes"` to the `[tsl_map]` shortcode.

= Can I filter locations by category? =

Yes. Add `category="your-category-slug"` to either the map or stores shortcode/block.

== Screenshots ==

1. Location edit screen with map preview and geocoding
2. Locations Map block in Gutenberg editor
3. Locations List block with layout options
4. Frontend display with sticky map and grouped locations
5. Mobile-responsive location list
6. Settings page with Google Maps API configuration

== Changelog ==

= 2.0.1 =
* Fixed critical bug where map markers failed to load due to data structure issues
* Ensured compatibility with non-sequential category IDs

= 2.0.0 =
* Complete rebuild focused on simplicity and business needs
* Added Gutenberg blocks with live preview
* Added sticky map positioning option
* Added clickable Google Maps links for addresses
* Added category display in location listings
* Fixed phantom item bug in grouped displays
* Improved asset cache busting for instant updates
* Enhanced mobile responsiveness
* Streamlined admin interface
* Performance optimizations

= 1.4.0 =
* Fixed post type and taxonomy registration
* Improved admin menu structure
* Standardized text domain to retail-locations
* Added comprehensive testing

= 1.3.0 =
* Added geographic area taxonomy
* Added area-based grouping
* Added map focusing on area click
* Enhanced store display templates

== Upgrade Notice ==

= 2.0.0 =
Major update with new Gutenberg blocks, sticky maps, and improved UX.

== Shortcode Examples ==

**Basic Map:**
`[tsl_map]`

**Filtered Map with Custom Height:**
`[tsl_map category="cafes" height="600px"]`

**Sticky Map:**
`[tsl_map sticky="yes" height="80vh"]`

**Store List Grouped by Area:**
`[tsl_stores group_by_area="yes" layout="grid2"]`

**Filtered Grid Layout:**
`[tsl_stores category="retail" layout="grid3" show_hours="no"]`

== Support ==

For support, documentation, and updates, visit https://jellum.net/retail-locations
