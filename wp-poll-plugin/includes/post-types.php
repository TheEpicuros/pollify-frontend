
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
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type('poll', $args);
    
    // Register meta boxes for poll options
    add_action('add_meta_boxes', 'pollify_add_meta_boxes');
    add_action('save_post_poll', 'pollify_save_poll_meta', 10, 2);
    
    // Register poll categories
    register_taxonomy(
        'poll_type',
        'poll',
        array(
            'label' => __('Poll Types', 'pollify'),
            'rewrite' => array('slug' => 'poll-type'),
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        )
    );
    
    // Create default poll types if they don't exist
    pollify_create_default_poll_types();
}
add_action('init', 'pollify_register_post_types');

/**
 * Create default poll types
 */
function pollify_create_default_poll_types() {
    $poll_types = array(
        'binary' => __('Binary Choice', 'pollify'),
        'multiple-choice' => __('Multiple Choice', 'pollify'),
        'check-all' => __('Check All That Apply', 'pollify'),
        'ranked-choice' => __('Ranked Choice', 'pollify'),
        'rating-scale' => __('Rating Scale', 'pollify'),
        'open-ended' => __('Open-Ended', 'pollify'),
        'image-based' => __('Image-Based', 'pollify'),
        'quiz' => __('Quiz', 'pollify'),
        'opinion' => __('Opinion', 'pollify'),
        'straw' => __('Straw Poll', 'pollify'),
        'interactive' => __('Interactive', 'pollify'),
        'referendum' => __('Referendum', 'pollify')
    );
    
    foreach ($poll_types as $slug => $name) {
        if (!term_exists($slug, 'poll_type')) {
            wp_insert_term($name, 'poll_type', array('slug' => $slug));
        }
    }
}

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
    
    add_meta_box(
        'pollify_poll_settings',
        __('Poll Settings', 'pollify'),
        'pollify_poll_settings_callback',
        'poll',
        'side',
        'default'
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
    
    // Get poll type
    $poll_type = pollify_get_poll_type($post->ID);
    ?>
    <div id="poll-options">
        <p><?php _e('Add options for your poll. You need at least two options.', 'pollify'); ?></p>
        
        <?php if ($poll_type === 'image-based') : ?>
        <p class="description"><?php _e('For image-based polls, enter an image URL or media ID for each option.', 'pollify'); ?></p>
        <?php endif; ?>
        
        <?php foreach ($options as $key => $option) : ?>
        <div class="poll-option">
            <input 
                type="text" 
                name="poll_options[]" 
                value="<?php echo esc_attr($option); ?>" 
                class="widefat" 
                placeholder="<?php esc_attr_e('Poll option', 'pollify'); ?>"
            >
            <?php if ($poll_type === 'image-based') : ?>
            <div class="poll-option-image-container">
                <button type="button" class="button poll-option-image-select">
                    <?php _e('Select Image', 'pollify'); ?>
                </button>
                <input 
                    type="hidden" 
                    name="poll_option_images[]" 
                    value="<?php echo esc_attr(get_post_meta($post->ID, '_poll_option_images', true)[$key] ?? ''); ?>" 
                    class="poll-option-image-id"
                >
                <div class="poll-option-image-preview"></div>
            </div>
            <?php endif; ?>
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
            var pollType = $('#_poll_type').val();
            var imageHtml = '';
            
            if (pollType === 'image-based') {
                imageHtml = '<div class="poll-option-image-container">' +
                    '<button type="button" class="button poll-option-image-select"><?php _e('Select Image', 'pollify'); ?></button>' +
                    '<input type="hidden" name="poll_option_images[]" value="" class="poll-option-image-id">' +
                    '<div class="poll-option-image-preview"></div>' +
                    '</div>';
            }
            
            var option = $('<div class="poll-option">' +
                '<input type="text" name="poll_options[]" value="" class="widefat" placeholder="<?php esc_attr_e('Poll option', 'pollify'); ?>">' +
                imageHtml +
                '</div>');
            
            $('#poll-options').append(option);
            
            // Initialize media uploader for the new button
            initMediaUploader(option.find('.poll-option-image-select'));
        });
        
        // Media uploader for image-based polls
        function initMediaUploader(button) {
            $(button).on('click', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var container = button.closest('.poll-option-image-container');
                var imageIdInput = container.find('.poll-option-image-id');
                var imagePreview = container.find('.poll-option-image-preview');
                
                var frame = wp.media({
                    title: '<?php _e('Select or Upload Image', 'pollify'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'pollify'); ?>'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    
                    imageIdInput.val(attachment.id);
                    imagePreview.html('<img src="' + attachment.url + '" alt="" style="max-width:100px;max-height:100px;">');
                });
                
                frame.open();
            });
        }
        
        // Initialize media uploader for existing buttons
        $('.poll-option-image-select').each(function() {
            initMediaUploader($(this));
        });
        
        // Load image previews on page load
        $('.poll-option-image-id').each(function() {
            var imageId = $(this).val();
            var previewContainer = $(this).siblings('.poll-option-image-preview');
            
            if (imageId) {
                wp.ajax.post('get-attachment', {
                    id: imageId
                }).done(function(attachment) {
                    previewContainer.html('<img src="' + attachment.url + '" alt="" style="max-width:100px;max-height:100px;">');
                });
            }
        });
    });
    </script>
    <?php
}

/**
 * Render the poll settings meta box
 */
function pollify_poll_settings_callback($post) {
    // Get current values
    $end_date = get_post_meta($post->ID, '_poll_end_date', true);
    $show_results = get_post_meta($post->ID, '_poll_show_results', true);
    $results_display = get_post_meta($post->ID, '_poll_results_display', true);
    $allow_comments = get_post_meta($post->ID, '_poll_allow_comments', true);
    $allowed_roles = get_post_meta($post->ID, '_poll_allowed_roles', true);
    $poll_type = pollify_get_poll_type($post->ID);
    
    if (!$results_display) {
        $results_display = 'bar';
    }
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = array('all');
    }
    
    // Get all user roles
    $roles = get_editable_roles();
    ?>
    <p>
        <label for="_poll_type"><?php _e('Poll Type:', 'pollify'); ?></label>
        <select name="_poll_type" id="_poll_type" class="widefat">
            <?php
            $terms = get_terms(array(
                'taxonomy' => 'poll_type',
                'hide_empty' => false,
            ));
            
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($poll_type, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            ?>
        </select>
    </p>
    
    <p>
        <label for="_poll_end_date"><?php _e('End Date (optional):', 'pollify'); ?></label>
        <input 
            type="datetime-local" 
            id="_poll_end_date" 
            name="_poll_end_date" 
            value="<?php echo esc_attr($end_date); ?>" 
            class="widefat"
        >
        <span class="description"><?php _e('Leave empty for no end date', 'pollify'); ?></span>
    </p>
    
    <p>
        <label for="_poll_show_results">
            <input 
                type="checkbox" 
                id="_poll_show_results" 
                name="_poll_show_results" 
                value="1" 
                <?php checked($show_results, '1'); ?>
            >
            <?php _e('Always show results', 'pollify'); ?>
        </label>
    </p>
    
    <p>
        <label for="_poll_results_display"><?php _e('Results Display:', 'pollify'); ?></label>
        <select name="_poll_results_display" id="_poll_results_display" class="widefat">
            <option value="bar" <?php selected($results_display, 'bar'); ?>><?php _e('Bar Chart', 'pollify'); ?></option>
            <option value="pie" <?php selected($results_display, 'pie'); ?>><?php _e('Pie Chart', 'pollify'); ?></option>
            <option value="donut" <?php selected($results_display, 'donut'); ?>><?php _e('Donut Chart', 'pollify'); ?></option>
            <option value="text" <?php selected($results_display, 'text'); ?>><?php _e('Text Only', 'pollify'); ?></option>
        </select>
    </p>
    
    <p>
        <label for="_poll_allow_comments">
            <input 
                type="checkbox" 
                id="_poll_allow_comments" 
                name="_poll_allow_comments" 
                value="1" 
                <?php checked($allow_comments, '1'); ?>
            >
            <?php _e('Allow comments', 'pollify'); ?>
        </label>
    </p>
    
    <p><?php _e('Who can vote:', 'pollify'); ?></p>
    <ul>
        <li>
            <label>
                <input 
                    type="checkbox" 
                    name="_poll_allowed_roles[]" 
                    value="all" 
                    <?php checked(in_array('all', $allowed_roles), true); ?>
                >
                <?php _e('Everyone (including not logged in)', 'pollify'); ?>
            </label>
        </li>
        <?php foreach ($roles as $role_key => $role) : ?>
        <li>
            <label>
                <input 
                    type="checkbox" 
                    name="_poll_allowed_roles[]" 
                    value="<?php echo esc_attr($role_key); ?>" 
                    <?php checked(in_array($role_key, $allowed_roles), true); ?>
                >
                <?php echo esc_html($role['name']); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
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
    
    // Save poll type
    if (isset($_POST['_poll_type'])) {
        $poll_type = sanitize_text_field($_POST['_poll_type']);
        
        // Set the poll type taxonomy
        wp_set_object_terms($post_id, $poll_type, 'poll_type');
        
        // For image-based polls, save the image IDs
        if ($poll_type === 'image-based' && isset($_POST['poll_option_images'])) {
            $images = array_map('absint', $_POST['poll_option_images']);
            update_post_meta($post_id, '_poll_option_images', $images);
        }
    }
    
    // Save other poll settings
    if (isset($_POST['_poll_end_date'])) {
        update_post_meta($post_id, '_poll_end_date', sanitize_text_field($_POST['_poll_end_date']));
    }
    
    update_post_meta($post_id, '_poll_show_results', isset($_POST['_poll_show_results']) ? '1' : '0');
    
    if (isset($_POST['_poll_results_display'])) {
        update_post_meta($post_id, '_poll_results_display', sanitize_text_field($_POST['_poll_results_display']));
    }
    
    update_post_meta($post_id, '_poll_allow_comments', isset($_POST['_poll_allow_comments']) ? '1' : '0');
    
    if (isset($_POST['_poll_allowed_roles'])) {
        $allowed_roles = array_map('sanitize_text_field', $_POST['_poll_allowed_roles']);
        update_post_meta($post_id, '_poll_allowed_roles', $allowed_roles);
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

/**
 * Helper function to get poll type
 */
function pollify_get_poll_type($poll_id) {
    $terms = wp_get_object_terms($poll_id, 'poll_type', array('fields' => 'slugs'));
    return !empty($terms) ? $terms[0] : 'multiple-choice'; // Default to multiple choice
}

/**
 * Check if a poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false; // No end date, poll is active
    }
    
    $current_time = current_time('timestamp');
    $end_timestamp = strtotime($end_date);
    
    return $current_time > $end_timestamp;
}

/**
 * Check if user can vote on a poll
 */
function pollify_can_user_vote($poll_id) {
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return false;
    }
    
    // Check user role restrictions
    $allowed_roles = get_post_meta($poll_id, '_poll_allowed_roles', true);
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = array('all');
    }
    
    // If 'all' is allowed, everyone can vote
    if (in_array('all', $allowed_roles)) {
        return true;
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return false;
    }
    
    // Get current user's roles
    $user = wp_get_current_user();
    $user_roles = $user->roles;
    
    // Check if user has any of the allowed roles
    foreach ($user_roles as $role) {
        if (in_array($role, $allowed_roles)) {
            return true;
        }
    }
    
    return false;
}
