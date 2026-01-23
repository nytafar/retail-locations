# Retail Locations

**A minimal, business-focused WordPress plugin for real store locators.**

Retail Locations is a clean, professional store locator plugin designed for businesses that need to display their physical locations on an interactive Google Map. Built with simplicity and performance in mind, it provides all the essential features without bloat.

## Why Retail Locations?

Most store locator plugins are overcomplicated, slow, or designed for enterprise use cases. We built Retail Locations for **real businesses** who need:

- **Fast setup** - Add your Google Maps API key and start adding locations
- **Clean interface** - Intuitive admin UI that doesn't overwhelm
- **Flexible display** - Show locations as lists, grids, or on interactive maps
- **Smart organization** - Group by geographic area or business category
- **Modern blocks** - Full Gutenberg support with live previews
- **Mobile-ready** - Responsive design that works on all devices

## Features

### 📍 Location Management
- Custom post type for retail locations with full WordPress editor support
- Store address, coordinates (latitude/longitude), contact info, and business hours
- Featured images and rich descriptions
- Bulk import/export via WordPress XML

### 🗺️ Interactive Maps
- Google Maps integration with customizable appearance
- Automatic geocoding from addresses
- Info windows with location details
- Click-to-focus on geographic areas
- Configurable zoom levels and map controls
- Mobile-friendly touch interactions

### 🏷️ Organization & Filtering
- **Categories** - Organize by business type (cafes, retail, services, etc.)
- **Geographic Areas** - Group locations by city, region, or neighborhood
- Filter locations by category on maps and lists
- Hierarchical taxonomy support

### 🎨 Display Options
- **Gutenberg Blocks** - Locations Map and Locations List blocks with live preview
- **Shortcodes** - `[tsl_map]` and `[tsl_stores]` for classic editor
- **Layout Options** - Full width, 2-column, 3-column, or 4-column grids
- **Sticky Maps** - Keep map visible while scrolling through location list
- **Grouped Display** - Group by category or area with collapsible sections
- **Customizable Fields** - Show/hide images, descriptions, hours, contact info

### 🔗 User Experience
- **Clickable Addresses** - Direct links to Google Maps for navigation
- **Category Display** - Show location categories inline
- **Area Focusing** - Click area headers to center map on that region
- **Responsive Design** - Mobile-first CSS with touch-friendly controls

## Installation

1. Upload the `retail-locations` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Retail Locations → Settings**
4. Add your Google Maps API Key ([Get one here](https://developers.google.com/maps/documentation/javascript/get-api-key))
5. Enable the **Geocoding API** in your Google Cloud Console
6. Start adding locations!

## Quick Start

### Adding Your First Location

1. Go to **Retail Locations → Add New**
2. Enter the location name and description
3. Add the full address in the **Location Details** meta box
4. The map will auto-geocode and show a preview
5. Add contact info and business hours
6. Assign categories and areas
7. Publish!

### Displaying Locations

**Using Gutenberg Blocks:**
1. Add a **Locations Map** block for the interactive map
2. Add a **Locations List** block for the store listing
3. Configure options in the block sidebar
4. Enable "Sticky Position" on the map for side-by-side layouts

**Using Shortcodes:**
```
[tsl_map height="600px" category="cafes"]
[tsl_stores layout="grid3" group_by_area="yes"]
```

## Shortcode Reference

### Map Shortcode
```
[tsl_map 
    category=""           // Filter by category slug
    width="100%"          // Map width
    height="500px"        // Map height
    map_controls="yes"    // Show map controls
    scrollwheel="no"      // Enable scroll zoom
    mobile_draggable="no" // Enable mobile drag
    sticky="no"           // Sticky positioning
]
```

### Stores List Shortcode
```
[tsl_stores 
    category=""              // Filter by category slug
    posts_per_page="-1"      // Number of locations (-1 = all)
    layout="fullwidth"       // fullwidth, grid2, grid3, grid4
    show_hours="yes"         // Show business hours
    show_contact="yes"       // Show contact info
    show_description="yes"   // Show description
    show_image="yes"         // Show featured image
    group_by_category="no"   // Group by category
    group_by_area="no"       // Group by area
    order="ASC"              // Sort order
    orderby="title"          // Sort field
]
```

## Block Attributes

Both Gutenberg blocks support the same attributes as the shortcodes, configurable via the block sidebar inspector controls.

## Geographic Areas

Areas allow you to group locations by geography and enable map focusing:

1. Go to **Retail Locations → Areas**
2. Create an area (e.g., "Downtown Oslo", "Brooklyn")
3. Add latitude, longitude, and zoom level for map centering
4. Assign locations to areas
5. Use `group_by_area="yes"` to display grouped by area
6. Click area headers to focus the map on that region

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Google Maps API Key with:
  - Maps JavaScript API enabled
  - Geocoding API enabled

## Frequently Asked Questions

**Do I need a Google Maps API key?**
Yes. The plugin requires a Google Maps API key to display maps and geocode addresses.

**Can I import existing locations?**
Yes. Use WordPress's built-in XML import/export functionality.

**Does it work with page builders?**
Yes. Use the shortcodes in any page builder that supports them, or use the Gutenberg blocks.

**Can I customize the appearance?**
Yes. The plugin uses standard WordPress classes and can be styled with custom CSS.

**Is it translation-ready?**
Yes. The plugin uses the `retail-locations` text domain and is fully translatable.

## Support

For support, documentation, and updates:
- Website: [https://jellum.net/retail-locations](https://jellum.net/retail-locations)
- GitHub: [Report issues and contribute](https://github.com/yourusername/retail-locations)

## Credits

Developed by [Lasse Jellum](https://jellum.net)

## License

GPL v2 or later
