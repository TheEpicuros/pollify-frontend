
<?php
/**
 * Poll types helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get poll type
 */
function pollify_get_poll_type($poll_id) {
    $poll_type = get_post_meta($poll_id, '_poll_type', true);
    
    if (empty($poll_type)) {
        return 'multiple-choice'; // Default type
    }
    
    return $poll_type;
}

/**
 * Get poll type name
 */
function pollify_get_poll_type_name($poll_id) {
    $poll_type = pollify_get_poll_type($poll_id);
    
    $types = array(
        'binary' => __('Yes/No', 'pollify'),
        'multiple-choice' => __('Multiple Choice', 'pollify'),
        'check-all' => __('Multiple Answers', 'pollify'),
        'ranked-choice' => __('Ranked Choice', 'pollify'),
        'rating-scale' => __('Rating Scale', 'pollify'),
        'open-ended' => __('Open Ended', 'pollify'),
        'image-based' => __('Image Based', 'pollify')
    );
    
    return isset($types[$poll_type]) ? $types[$poll_type] : __('Standard Poll', 'pollify');
}
