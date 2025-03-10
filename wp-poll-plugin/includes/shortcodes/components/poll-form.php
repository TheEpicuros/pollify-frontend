
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
function pollify_render_poll_form($poll_id, $poll, $options, $poll_type, $show_results) {
    ob_start();
    
    // Check if this is an image-based poll
    $option_images = array();
    if ($poll_type === 'image-based') {
        $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    }
    
    // Different input types based on poll type
    $input_type = 'radio';
    if ($poll_type === 'check-all') {
        $input_type = 'checkbox';
    }
    
    ?>
    <form class="pollify-poll-form" data-poll-id="<?php echo $poll_id; ?>">
        <?php if ($poll_type === 'ranked-choice'): ?>
        <div class="pollify-ranked-choices">
            <p class="pollify-instruction"><?php _e('Drag to rank your choices in order of preference:', 'pollify'); ?></p>
            <ul class="pollify-sortable-options">
                <?php foreach ($options as $option_id => $option_text) : ?>
                <li class="pollify-sortable-option" data-option-id="<?php echo esc_attr($option_id); ?>">
                    <span class="pollify-drag-handle dashicons dashicons-menu"></span>
                    <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" name="ranked_options" value="">
        </div>
        
        <?php elseif ($poll_type === 'rating-scale'): ?>
        <div class="pollify-rating-scale">
            <div class="pollify-rating-question"><?php echo esc_html($poll->post_title); ?></div>
            <div class="pollify-rating-options">
                <?php 
                // Rating scale typically from 1-5 or 1-10
                $scale_max = count($options);
                for ($i = 1; $i <= $scale_max; $i++) : 
                    $option_id = array_keys($options)[$i-1];
                ?>
                <label class="pollify-rating-option">
                    <input type="radio" name="poll_option" value="<?php echo esc_attr($option_id); ?>">
                    <span class="pollify-rating-value"><?php echo $i; ?></span>
                    <span class="pollify-rating-label"><?php echo esc_html($options[$option_id]); ?></span>
                </label>
                <?php endfor; ?>
            </div>
        </div>
        
        <?php else: ?>
        <div class="pollify-poll-options-list <?php echo $poll_type === 'image-based' ? 'pollify-image-options' : ''; ?>">
            <?php foreach ($options as $option_id => $option_text) : ?>
            <div class="pollify-poll-option">
                <label>
                    <input type="<?php echo $input_type; ?>" name="poll_option<?php echo $input_type === 'checkbox' ? '[]' : ''; ?>" value="<?php echo esc_attr($option_id); ?>">
                    
                    <?php if ($poll_type === 'image-based' && !empty($option_images[$option_id])) : 
                        $image_url = wp_get_attachment_image_url($option_images[$option_id], 'medium');
                        if ($image_url) :
                    ?>
                    <div class="pollify-option-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($option_text); ?>">
                    </div>
                    <?php endif; endif; ?>
                    
                    <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="pollify-poll-submit">
            <button type="submit" class="pollify-poll-vote-button">
                <?php _e('Vote', 'pollify'); ?>
            </button>
            
            <?php if ($show_results): ?>
            <button type="button" class="pollify-view-results-button">
                <?php _e('View Results', 'pollify'); ?>
            </button>
            <?php endif; ?>
        </div>
        
        <div class="pollify-poll-message" style="display: none;"></div>
    </form>
    <?php
    
    return ob_get_clean();
}

/**
 * Render user vote information
 */
function pollify_render_user_vote_info($user_vote) {
    if (!$user_vote) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="pollify-user-vote-info">
        <?php 
            printf(
                __('You voted on %s', 'pollify'), 
                pollify_format_date($user_vote->voted_at)
            ); 
        ?>
    </div>
    <?php
    return ob_get_clean();
}
