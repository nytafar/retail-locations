# Retail Locations Fullscreen Template

An experimental fullscreen page template that displays retail locations with an interactive map on the left and a scrollable location list on the right.

## Features

- **Fullscreen Layout**: No header, footer, or sidebar distractions
- **Split View**:
  - Left side (55%): Sticky Google Map
  - Right side (45%): Scrollable locations list with collapsible groups
- **Responsive Design**:
  - Desktop (>1024px): Side-by-side layout
  - Tablet (768px-1024px): Map on top, list below
  - Mobile (<768px): Stacked layout with hamburger menu to toggle visibility
- **Controls**:
  - Back button to return to previous page
  - Hamburger menu toggle on mobile/tablet
  - Collapsible location groups organized by area
  - Exclusive accordion mode (only one group open at a time)

## How to Use

1. **Create a Page**: Go to WordPress admin → Pages → Add New
2. **Set Template**: In the page settings, scroll to "Page Template" dropdown
3. **Select**: Choose "Retail Locations Fullscreen"
4. **Publish**: Click "Publish" to save

The page will automatically display:
- A Google Map with all retail locations
- A grouped list of locations (organized by area)
- Interactive controls for navigation

## Customization

### Modify the Shortcodes

Edit the template file (`plugins/retail-locations/templates/page-fullscreen.php`) to customize:

**Map Configuration** (currently in page-fullscreen.php):
```php
[retail_locations_map category="" width="100%" height="100%" map_controls="yes" scrollwheel="yes" sticky="no"]
```

**List Configuration** (currently in page-fullscreen.php):
```php
[retail_locations layout="compact" group_by_area="yes" collapsible="yes" exclusive_accordion="yes" posts_per_page="-1"]
```

### Styling

Main CSS file: `assets/css/fullscreen.css`

Key CSS classes:
- `.retail-locations-fullscreen-container` - Main container
- `.retail-locations-fullscreen-map` - Map section
- `.retail-locations-fullscreen-sidebar` - List sidebar
- `.retail-locations-fullscreen-controls` - Top control bar
- `.retail-locations-sidebar-toggle` - Hamburger menu button

### CSS Variables (in SCSS)

- `$sidebar-width`: Width of the list sidebar (default: 45%)
- `$map-width`: Width of the map area (default: 55%)
- `$control-height`: Height of control bar (default: 56px)
- `$bp-tablet`: Tablet breakpoint (default: 1024px)
- `$bp-mobile`: Mobile breakpoint (default: 768px)

## Responsive Behavior

### Desktop (>1024px)
- Side-by-side layout with sticky map
- Map takes up 55%, list takes up 45%
- Hamburger menu hidden

### Tablet (768px-1024px)
- Stacked layout with map on top (50%)
- Hamburger menu visible
- Map not sticky

### Mobile (<768px)
- Stacked layout
- Hamburger menu to toggle sidebar visibility
- Map: minimum 300px height
- List: minimum 400px height

## Browser Compatibility

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Notes

- Map is optimized for full-screen display
- Smooth scrolling on location list
- Lazy-loaded markers with geocoding support
- Efficient CSS animations and transitions

## Troubleshooting

**Map not appearing?**
- Check that Google Maps API key is configured
- Verify API key has Maps and Places libraries enabled

**List not scrolling?**
- Ensure viewport width is correctly set
- Check browser console for JavaScript errors

**Controls not working?**
- Verify jQuery is loaded
- Check that retail-locations JavaScript is enqueued

## Future Enhancements

- [ ] Search/filter functionality in list
- [ ] Category filter in list header
- [ ] Info window customization
- [ ] Marker clustering for large datasets
- [ ] Export location data
- [ ] Keyboard navigation
- [ ] Dark mode support
