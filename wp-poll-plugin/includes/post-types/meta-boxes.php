
<?php
/**
 * Poll meta boxes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
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
