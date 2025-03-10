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
                
                case 'ranked-choice':
                    echo pollify_render_ranked_options($poll_id, $options);
                    break;
                
                case 'open-ended':
                    echo pollify_render_open_ended_options($poll_id, $options);
                    break;
                
                case 'quiz':
                    echo pollify_render_quiz_options($poll_id, $options);
                    break;

                case 'opinion':
                case 'straw':
                case 'interactive':
                case 'referendum':
                    // These types use the standard multiple choice UI
                    echo pollify_render_radio_options($poll_id, $options);
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
 * Render ranked-choice options
 */
function pollify_render_ranked_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-ranked">
        <p class="pollify-ranked-instructions"><?php _e('Drag options to rank them in your preferred order.', 'pollify'); ?></p>
        <ul class="pollify-ranked-list" id="pollify-ranked-list-<?php echo esc_attr($poll_id); ?>">
            <?php foreach ($options as $option_id => $option_text) : ?>
            <li class="pollify-ranked-item" data-option-id="<?php echo esc_attr($option_id); ?>">
                <div class="pollify-ranked-handle">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 6H16M8 12H16M8 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="pollify-ranked-text"><?php echo esc_html($option_text); ?></div>
                <input type="hidden" name="ranked_options[]" value="<?php echo esc_attr($option_id); ?>">
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple drag and drop functionality
        const list = document.getElementById('pollify-ranked-list-<?php echo esc_js($poll_id); ?>');
        let draggedItem = null;
        
        const items = list.querySelectorAll('.pollify-ranked-item');
        items.forEach(item => {
            item.addEventListener('dragstart', function() {
                draggedItem = item;
                setTimeout(function() {
                    item.style.display = 'none';
                }, 0);
            });
            
            item.addEventListener('dragend', function() {
                setTimeout(function() {
                    draggedItem.style.display = 'flex';
                    draggedItem = null;
                }, 0);
            });
            
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            
            item.addEventListener('dragenter', function(e) {
                e.preventDefault();
                this.style.borderTop = '2px solid #3b82f6';
            });
            
            item.addEventListener('dragleave', function() {
                this.style.borderTop = '1px solid transparent';
            });
            
            item.addEventListener('drop', function() {
                this.style.borderTop = '1px solid transparent';
                if (draggedItem !== this) {
                    let children = Array.from(list.children);
                    let draggedPos = children.indexOf(draggedItem);
                    let targetPos = children.indexOf(this);
                    
                    if (draggedPos < targetPos) {
                        list.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        list.insertBefore(draggedItem, this);
                    }
                    
                    // Update hidden inputs to reflect new order
                    const updatedItems = list.querySelectorAll('.pollify-ranked-item');
                    updatedItems.forEach((item, index) => {
                        const input = item.querySelector('input[name="ranked_options[]"]');
                        input.value = item.dataset.optionId;
                    });
                }
            });
            
            // Make items draggable
            item.setAttribute('draggable', true);
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Render open-ended options
 */
function pollify_render_open_ended_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-open-ended">
        <div class="pollify-poll-option">
            <label for="pollify-open-response-<?php echo esc_attr($poll_id); ?>" class="pollify-option-label">
                <?php 
                // Use the first option text as the prompt, or fallback to default
                $prompt = !empty($options) && is_array($options) && count($options) > 0 
                    ? reset($options) 
                    : __('Enter your response', 'pollify');
                ?>
                <span class="pollify-option-text"><?php echo esc_html($prompt); ?></span>
            </label>
            <textarea 
                id="pollify-open-response-<?php echo esc_attr($poll_id); ?>" 
                name="open_response" 
                class="pollify-open-response-input" 
                rows="4" 
                placeholder="<?php esc_attr_e('Type your answer here...', 'pollify'); ?>" 
                required
            ></textarea>
            <input type="hidden" name="option_id" value="open_response">
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render quiz options
 */
function pollify_render_quiz_options($poll_id, $options) {
    // For front-end, quiz options look like regular radio buttons
    // The correct answer is only revealed after voting
    return pollify_render_radio_options($poll_id, $options);
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
