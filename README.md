# Very Simple Event List Extended

> This plugin is an extended version of the "Very Simple Event List" WordPress plugin, originally created by **Guido**. It adds recurring events support and enhanced display features while maintaining the lightweight nature of the original plugin.

## About

Very Simple Event List Extended allows you to create and manage events with additional features:
- All features from the original Very Simple Event List
- Support for recurring events
- Monthly grouping display
- Enhanced timeline view
- Improved event organization

## New Features

### Recurring Events
- Create weekly recurring events
- Set end date for recurring series
- Automatic instance generation
- Proper chronological display

### Enhanced Display
- Events grouped by month
- Clean timeline view
- Improved date and time formatting
- Better visual organization

## Original Features

### Event Display Options
You can display your event list using:
- Block
- Shortcode
- Widget

### Basic Shortcodes
- `[vsel]` to display upcoming events (today included)
- `[vsel-future-events]` to display future events (today not included)
- `[vsel-current-events]` to display current events
- `[vsel-past-events]` to display past events (before today)
- `[vsel-all-events]` to display all events

### Settings Page
Customize your event list via Settings > VS Event List.
Settings can be overridden using attributes.

### Attributes
Customize your event list with attributes:
```
class="your-class-name"               // Add custom CSS class
posts_per_page="5"                    // Change number of events per page
posts_per_page="-1"                   // Display all events
offset="1"                            // Skip events
date_format="j F Y"                   // Change date format
event_cat="your-category-slug"        // Display specific category
order="DESC"                          // Reverse order
title_link="false"                    // Disable title link
featured_image="false"                // Disable featured image
event_info="all"                      // Display all info
event_info="summary"                  // Display summary
```

Example: `[vsel posts_per_page="5" event_cat="your-category-slug" event_info="summary"]`

### Features Support
- Featured images
- Custom post type "event"
- Single event pages
- Event category pages
- Post type archive
- Search results
- Custom ordering
- Menu page support
- Advanced Custom Fields (ACF)
- RSS and iCal feed

## Installation

1. Upload the plugin files to wp-content/plugins/
2. Activate the plugin
3. Go to Events menu to add events
4. Use shortcode `[vsel]` to display events

## Credits

This plugin is based on Very Simple Event List by Guido (https://wordpress.org/plugins/very-simple-event-list/).
Extended version developed by: [Khawar Mehfooz](https://khawarmehfooz.com).

### Original Plugin Credits
- Original author: Guido
- Original plugin: [Very Simple Event List](https://wordpress.org/plugins/very-simple-event-list/)
- Website: https://www.guido.site

## License

GPLv3 - GNU General Public License v3.0

## Translation

The plugin supports WordPress language packs. The translation folder is kept for reference.

## Support

For questions about the extended features, please visit the GitHub repository: [Your Repository URL](https://github.com/KhawarMehfooz/vs-event-list-extended)

For questions about original features, please check the original plugin's [FAQ](https://wordpress.org/plugins/very-simple-event-list/) section.