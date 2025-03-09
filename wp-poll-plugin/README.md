
# Pollify - WordPress Polling Plugin

A modern polling system for WordPress that allows users to create, browse, and vote on polls from the frontend.

## Features

- Custom 'poll' post type for storing polls
- Frontend poll display with animated results
- AJAX voting without page refresh
- IP-based vote limiting to prevent duplicate votes
- Shortcodes for displaying polls, creating polls, and browsing polls
- Responsive design that works on all devices

## Installation

1. Download the plugin ZIP file
2. In your WordPress admin dashboard, go to Plugins > Add New > Upload Plugin
3. Upload the ZIP file and activate the plugin
4. The plugin is now ready to use

## Usage

### Displaying a Poll

Use the `[pollify]` shortcode to display a specific poll:

```
[pollify id="123"]
```

Replace `123` with the ID of the poll you want to display.

### Creating a Poll

Use the `[pollify_create]` shortcode to display a form for creating new polls:

```
[pollify_create]
```

Note: Users must be logged in and have permission to publish posts to create polls.

### Browsing Polls

Use the `[pollify_browse]` shortcode to display a list of all polls:

```
[pollify_browse limit="10"]
```

You can specify the number of polls to display using the `limit` attribute.

## Creating Polls in the Admin Dashboard

1. In your WordPress admin dashboard, go to Polls > Add New
2. Enter a title for your poll (this will be the poll question)
3. Add a description (optional)
4. Add poll options in the "Poll Options" meta box
5. Publish the poll

## FAQ

### Can I customize the appearance of polls?

Yes, you can customize the appearance of polls by modifying the CSS in the `assets/css/pollify.css` file.

### Can users vote multiple times?

No, the plugin prevents users from voting multiple times by tracking their IP address.

### Can I display multiple polls on the same page?

Yes, you can use the `[pollify]` shortcode multiple times on the same page with different poll IDs.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by [Your Name/Company]
