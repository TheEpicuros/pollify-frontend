
<?php
/**
 * Poll form rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll voting form
 */
function pollify_render_poll_form($poll_id, $poll, $options, $poll_type, $show_view_results_link = true) {
    $form_id = 'pollify-form-' . $poll_id;
    
    ob_start();
    ?>
    <form id="<?php echo esc_attr($form_id); ?>" class="pollify-poll-form pollify-poll-type-<?php echo esc_attr($poll_type); ?>" method="post">
        <?php wp_nonce_field('pollify_vote_' . $poll_id, 'pollify_vote_nonce'); ?>
        <input type="hidden" name="poll_id" value="<?php echo esc_attr($poll_id); ?>">
        
        <div class="pollify-poll-options-list">
            <?php 
            switch ($poll_type) {
                case 'binary':
                    echo pollify_render_binary_options($poll_id, $options);
                    break;
                    
                case 'check-all':
                    echo pollify_render_checkbox_options($poll_id, $options);
                    break;
                    
                case 'image-based':
                    echo pollify_render_image_options($poll_id, $options);
                    break;
                    
                case 'rating-scale':
                    echo pollify_render_rating_options($poll_id, $options);
                    break;
                    
                case 'multiple-choice':
                default:
                    echo pollify_render_radio_options($poll_id, $options);
                    break;
            }
            ?>
        </div>
        
        <div class="pollify-poll-actions">
            <button type="submit" class="pollify-submit-vote"><?php _e('Vote', 'pollify'); ?></button>
            
            <?php if ($show_view_results_link): ?>
            <a href="<?php echo esc_url(add_query_arg('results', '1')); ?>" class="pollify-view-results"><?php _e('View Results', 'pollify'); ?></a>
            <?php endif; ?>
        </div>
    </form>
    
    <div class="pollify-loading-indicator" style="display: none;">
        <span class="pollify-loader"></span>
        <span class="pollify-loading-text"><?php _e('Processing your vote...', 'pollify'); ?></span>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render standard radio button options
 */
function pollify_render_radio_options($poll_id, $options) {
    ob_start();
    
    foreach ($options as $option_id => $option_text) :
    ?>
    <div class="pollify-poll-option">
        <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
            <input 
                type="radio" 
                name="option_id" 
                id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                value="<?php echo esc_attr($option_id); ?>" 
                required
            >
            <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
        </label>
    </div>
    <?php 
    endforeach;
    
    return ob_get_clean();
}

/**
 * Render checkbox options (for multi-select polls)
 */
function pollify_render_checkbox_options($poll_id, $options) {
    ob_start();
    
    foreach ($options as $option_id => $option_text) :
    ?>
    <div class="pollify-poll-option">
        <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
            <input 
                type="checkbox" 
                name="option_id[]" 
                id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                value="<?php echo esc_attr($option_id); ?>"
            >
            <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
        </label>
    </div>
    <?php 
    endforeach;
    
    return ob_get_clean();
}

/**
 * Render binary (yes/no) options
 */
function pollify_render_binary_options($poll_id, $options) {
    ob_start();
    
    // Use just the first two options, regardless of how many are stored
    $option_keys = array_keys($options);
    $yes_option_id = isset($option_keys[0]) ? $option_keys[0] : '1';
    $no_option_id = isset($option_keys[1]) ? $option_keys[1] : '2';
    
    $yes_text = isset($options[$yes_option_id]) ? $options[$yes_option_id] : __('Yes', 'pollify');
    $no_text = isset($options[$no_option_id]) ? $options[$no_option_id] : __('No', 'pollify');
    ?>
    <div class="pollify-poll-options-binary">
        <div class="pollify-poll-option pollify-poll-option-yes">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($yes_option_id); ?>" class="pollify-option-label pollify-option-yes">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($yes_option_id); ?>" 
                    value="<?php echo esc_attr($yes_option_id); ?>" 
                    required
                >
                <span class="pollify-option-text"><?php echo esc_html($yes_text); ?></span>
            </label>
        </div>
        
        <div class="pollify-poll-option pollify-poll-option-no">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($no_option_id); ?>" class="pollify-option-label pollify-option-no">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($no_option_id); ?>" 
                    value="<?php echo esc_attr($no_option_id); ?>" 
                    required
                >
                <span class="pollify-option-text"><?php echo esc_html($no_text); ?></span>
            </label>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render image-based options
 */
function pollify_render_image_options($poll_id, $options) {
    $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    
    ob_start();
    ?>
    <div class="pollify-poll-options-images">
        <?php 
        foreach ($options as $option_id => $option_text) :
            $image_id = isset($option_images[$option_id]) ? $option_images[$option_id] : 0;
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        ?>
        <div class="pollify-poll-option pollify-poll-option-image">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                    value="<?php echo esc_attr($option_id); ?>" 
                    required
                >
                
                <?php if ($image_url) : ?>
                <div class="pollify-option-image">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($option_text); ?>">
                </div>
                <?php endif; ?>
                
                <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
            </label>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render rating scale options
 */
function pollify_render_rating_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-rating">
        <div class="pollify-rating-scale">
            <?php 
            // Get min and max labels
            $option_keys = array_keys($options);
            $min_option_id = isset($option_keys[0]) ? $option_keys[0] : '1';
            $max_option_id = isset($option_keys[count($option_keys) - 1]) ? $option_keys[count($option_keys) - 1] : '5';
            
            $min_label = isset($options[$min_option_id]) ? $options[$min_option_id] : '';
            $max_label = isset($options[$max_option_id]) ? $options[$max_option_id] : '';
            ?>
            
            <?php if ($min_label) : ?>
            <div class="pollify-rating-label pollify-rating-min"><?php echo esc_html($min_label); ?></div>
            <?php endif; ?>
            
            <div class="pollify-rating-options">
                <?php foreach ($options as $option_id => $option_text) : ?>
                <div class="pollify-rating-option">
                    <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-rating-value">
                        <input 
                            type="radio" 
                            name="option_id" 
                            id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                            value="<?php echo esc_attr($option_id); ?>" 
                            required
                        >
                        <span><?php echo esc_html($option_text); ?></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($max_label) : ?>
            <div class="pollify-rating-label pollify-rating-max"><?php echo esc_html($max_label); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render message about user's vote
 */
function pollify_render_user_vote_info($user_vote) {
    if (!$user_vote) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="pollify-user-vote-info">
        <p>
            <?php 
            printf(
                __('You voted for "%s" on %s', 'pollify'),
                esc_html($user_vote->option_text),
                date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($user_vote->voted_at))
            ); 
            ?>
        </p>
    </div>
    <?php
    return ob_get_clean();
}
