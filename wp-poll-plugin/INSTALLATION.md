
# Pollify WordPress Plugin Installation Guide

This guide will walk you through the process of installing and setting up the Pollify WordPress plugin.

## Installation Steps

### Method 1: Upload via WordPress Admin Dashboard

1. Download the plugin ZIP file
2. Log in to your WordPress admin dashboard
3. Navigate to Plugins > Add New
4. Click "Upload Plugin" at the top of the page
5. Click "Choose File" and select the downloaded ZIP file
6. Click "Install Now"
7. After installation completes, click "Activate Plugin"

### Method 2: Manual Installation via FTP

1. Download and extract the plugin ZIP file to your computer
2. Connect to your server using an FTP client
3. Navigate to the `/wp-content/plugins/` directory
4. Upload the entire `wp-poll-plugin` folder to this directory
5. Log in to your WordPress admin dashboard
6. Navigate to Plugins
7. Find "Pollify - React Polling System" in the list and click "Activate"

## Initial Setup

1. After activation, you'll see a new "Polls" menu item in your WordPress admin dashboard
2. Create your first poll by going to Polls > Add New
3. Enter a title (question), description (optional), and add poll options
4. Publish the poll to make it available for voting

## Using Shortcodes

The plugin provides three main shortcodes for displaying polls on your site:

1. Display a specific poll:
   ```
   [pollify id="123"]
   ```
   Replace "123" with the ID of your poll.

2. Display a poll creation form:
   ```
   [pollify_create]
   ```
   Note: Users must be logged in and have proper permissions to create polls.

3. Display a list of polls:
   ```
   [pollify_browse]
   ```
   This will display a grid of all published polls.

## Where to Find Poll IDs

You can find poll IDs in several ways:
1. In the admin dashboard, hover over a poll in the Polls list to see its ID in the URL preview
2. When viewing a poll, the ID is in the URL (e.g., `/wp-admin/post.php?post=123&action=edit`)
3. Use the "Polls" menu to view all polls and find their IDs

## Troubleshooting

If you encounter any issues:
1. Make sure your WordPress version is 5.0 or higher
2. Check that all plugin files were uploaded correctly
3. Verify that your server meets the minimum requirements (PHP 7.2+)
4. Try deactivating other plugins to check for conflicts
5. If problems persist, contact support with details about your environment
