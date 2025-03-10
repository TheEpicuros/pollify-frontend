
<?php
/**
 * Poll options meta box
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
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
