
<?php
/**
 * Poll rating system utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get rating UI and data for a poll
 */
function pollify_get_rating_html($poll_id) {
    $ratings = pollify_get_poll_ratings($poll_id);
    $upvotes = $ratings['upvotes'];
    $downvotes = $ratings['downvotes'];
    
    // Check if user has already rated
    $user_id = get_current_user_id();
    $user_ip = pollify_get_user_ip();
    
    $user_has_rated = false;
    $user_rating = null;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    $existing_rating = $wpdb->get_var($wpdb->prepare(
        "SELECT rating FROM $table_name WHERE poll_id = %d AND (user_ip = %s" . ($user_id ? " OR user_id = %d" : "") . ")",
        array_merge(array($poll_id, $user_ip), $user_id ? array($user_id) : array())
    ));
    
    if ($existing_rating !== null) {
        $user_has_rated = true;
        $user_rating = (int) $existing_rating;
    }
    
    ob_start();
    ?>
    <div class="pollify-poll-rating" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <h4><?php _e('Rate this poll:', 'pollify'); ?></h4>
        
        <div class="pollify-rating-buttons">
            <button 
                type="button" 
                class="pollify-rating-up <?php echo $user_rating === 1 ? 'pollify-user-rated' : ''; ?>" 
                data-rating="1" 
                data-nonce="<?php echo wp_create_nonce('pollify-rate-poll'); ?>"
                <?php echo $user_has_rated && $user_rating !== 1 ? 'disabled' : ''; ?>
            >
                <span class="dashicons dashicons-thumbs-up"></span>
                <span class="pollify-rating-count"><?php echo number_format_i18n($upvotes); ?></span>
            </button>
            
            <button 
                type="button" 
                class="pollify-rating-down <?php echo $user_rating === 0 ? 'pollify-user-rated' : ''; ?>" 
                data-rating="0" 
                data-nonce="<?php echo wp_create_nonce('pollify-rate-poll'); ?>"
                <?php echo $user_has_rated && $user_rating !== 0 ? 'disabled' : ''; ?>
            >
                <span class="dashicons dashicons-thumbs-down"></span>
                <span class="pollify-rating-count"><?php echo number_format_i18n($downvotes); ?></span>
            </button>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
