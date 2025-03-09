
<?php
/**
 * Custom post types
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the poll custom post type
 */
function pollify_register_post_types() {
    $labels = array(
        'name'               => _x('Polls', 'post type general name', 'pollify'),
        'singular_name'      => _x('Poll', 'post type singular name', 'pollify'),
        'menu_name'          => _x('Polls', 'admin menu', 'pollify'),
        'name_admin_bar'     => _x('Poll', 'add new on admin bar', 'pollify'),
        'add_new'            => _x('Add New', 'poll', 'pollify'),
        'add_new_item'       => __('Add New Poll', 'pollify'),
        'new_item'           => __('New Poll', 'pollify'),
        'edit_item'          => __('Edit Poll', 'pollify'),
        'view_item'          => __('View Poll', 'pollify'),
        'all_items'          => __('All Polls', 'pollify'),
        'search_items'       => __('Search Polls', 'pollify'),
        'parent_item_colon'  => __('Parent Polls:', 'pollify'),
        'not_found'          => __('No polls found.', 'pollify'),
        'not_found_in_trash' => __('No polls found in Trash.', 'pollify')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Poll posts for the Pollify plugin', 'pollify'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'poll'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-chart-bar',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('poll', $args);
    
    // Register meta boxes for poll options
    add_action('add_meta_boxes', 'pollify_add_meta_boxes');
    add_action('save_post_poll', 'pollify_save_poll_meta', 10, 2);
}
add_action('init', 'pollify_register_post_types');

/**
 * Add meta boxes to the poll post type
 */
function pollify_add_meta_boxes() {
    add_meta_box(
        'pollify_poll_options',
        __('Poll Options', 'pollify'),
        'pollify_poll_options_callback',
        'poll',
        'normal',
        'high'
    );
}

/**
 * Render the poll options meta box
 */
function pollify_poll_options_callback($post) {
    // Add a nonce field
    wp_nonce_field('pollify_save_poll_meta', 'pollify_poll_meta_nonce');
    
    // Get the current poll options
    $options = get_post_meta($post->ID, '_poll_options', true);
    
    if (!is_array($options)) {
        $options = array('', '');
    }
    ?>
    <div id="poll-options">
        <p><?php _e('Add options for your poll. You need at least two options.', 'pollify'); ?></p>
        
        <?php foreach ($options as $key => $option) : ?>
        <div class="poll-option">
            <input 
                type="text" 
                name="poll_options[]" 
                value="<?php echo esc_attr($option); ?>" 
                class="widefat" 
                placeholder="<?php esc_attr_e('Poll option', 'pollify'); ?>"
            >
        </div>
        <?php endforeach; ?>
        
        <p>
            <button type="button" class="button" id="add-poll-option">
                <?php _e('Add Option', 'pollify'); ?>
            </button>
        </p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#add-poll-option').on('click', function() {
            var option = $('<div class="poll-option">' +
                '<input type="text" name="poll_options[]" value="" class="widefat" placeholder="<?php esc_attr_e('Poll option', 'pollify'); ?>">' +
                '</div>');
            $('#poll-options').append(option);
        });
    });
    </script>
    <?php
}

/**
 * Save the poll meta data
 */
function pollify_save_poll_meta($post_id, $post) {
    // Check if our nonce is set
    if (!isset($_POST['pollify_poll_meta_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['pollify_poll_meta_nonce'], 'pollify_save_poll_meta')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sanitize and save the poll options
    if (isset($_POST['poll_options'])) {
        $options = array_map('sanitize_text_field', $_POST['poll_options']);
        $options = array_filter($options); // Remove empty options
        
        if (count($options) < 2) {
            // Add error message
            add_filter('redirect_post_location', function($location) {
                return add_query_arg('pollify_error', 'options', $location);
            });
        } else {
            update_post_meta($post_id, '_poll_options', $options);
        }
    }
}

/**
 * Display admin notices for poll errors
 */
function pollify_admin_notices() {
    if (isset($_GET['pollify_error']) && $_GET['pollify_error'] === 'options') {
        ?>
        <div class="error">
            <p><?php _e('A poll must have at least two options. Please add more options.', 'pollify'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'pollify_admin_notices');
