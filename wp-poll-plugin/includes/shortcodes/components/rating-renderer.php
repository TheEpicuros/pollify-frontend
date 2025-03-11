
<?php
/**
 * Poll rating rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get poll rating HTML - deprecated, use canonical function instead
 * This function now forwards to the canonical implementation
 */
function pollify_get_rating_html_renderer($poll_id) {
    // Require the canonical function if it exists
    if (!function_exists('pollify_get_rating_html')) {
        pollify_require_function('pollify_get_rating_html');
    }
    
    // Call the canonical function
    if (function_exists('pollify_get_rating_html')) {
        return pollify_get_rating_html($poll_id);
    }
    
    // Fallback implementation if canonical function not available
    $user_id = get_current_user_id();
    $has_rated = $user_id ? pollify_has_user_rated($poll_id, $user_id) : false;
    $rating = pollify_get_poll_rating($poll_id);
    
    ob_start();
    ?>
    <div class="pollify-poll-rating" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <div class="pollify-rating-header">
            <span class="pollify-rating-title"><?php _e('Rate this poll:', 'pollify'); ?></span>
            <?php if ($rating['count'] > 0) : ?>
            <span class="pollify-rating-average">
                <span class="pollify-rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php if ($i <= round($rating['average'])) : ?>
                            <span class="pollify-star pollify-star-filled">★</span>
                        <?php else : ?>
                            <span class="pollify-star">☆</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
                <span class="pollify-rating-text">
                    <?php printf(__('%1$s/5 (%2$s ratings)', 'pollify'), number_format($rating['average'], 1), $rating['count']); ?>
                </span>
            </span>
            <?php endif; ?>
        </div>
        
        <?php if (!$has_rated) : ?>
        <div class="pollify-rating-form">
            <div class="pollify-rating-stars-input">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                <label for="pollify-rate-<?php echo esc_attr($poll_id); ?>-<?php echo $i; ?>" class="pollify-star-label">
                    <input type="radio" name="pollify_rating" id="pollify-rate-<?php echo esc_attr($poll_id); ?>-<?php echo $i; ?>" value="<?php echo $i; ?>" 
                           class="pollify-star-input" data-poll-id="<?php echo esc_attr($poll_id); ?>">
                    <span class="pollify-star">☆</span>
                </label>
                <?php endfor; ?>
            </div>
            <button type="button" class="pollify-submit-rating" disabled><?php _e('Submit Rating', 'pollify'); ?></button>
        </div>
        <div class="pollify-rating-message" style="display: none;"></div>
        <?php else : ?>
        <div class="pollify-rating-message">
            <?php _e('You have already rated this poll.', 'pollify'); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Helper function to check if user has rated a poll
 */
function pollify_has_user_rated($poll_id, $user_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    return (bool) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND user_id = %d",
        $poll_id, $user_id
    ));
}

/**
 * Helper function to get poll rating
 */
function pollify_get_poll_rating($poll_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    $results = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) as count, AVG(rating) as average FROM $table_name WHERE poll_id = %d",
        $poll_id
    ));
    
    return array(
        'count' => $results->count ? (int) $results->count : 0,
        'average' => $results->average ? (float) $results->average : 0,
    );
}
