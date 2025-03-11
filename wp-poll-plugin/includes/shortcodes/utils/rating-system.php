
<?php
/**
 * Poll rating system utility functions
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
 * Get rating UI and data for a poll - deprecated, use canonical function instead
 */
if (pollify_can_define_function('pollify_get_rating_system_html')) {
    pollify_declare_function('pollify_get_rating_system_html', function($poll_id) {
        // Require the canonical function if it exists
        if (!function_exists('pollify_get_rating_html')) {
            pollify_require_function('pollify_get_rating_html');
        }
        
        // Call the canonical function
        if (function_exists('pollify_get_rating_html')) {
            return pollify_get_rating_html($poll_id);
        }
        
        // Fallback implementation if canonical function not available
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
    }, $current_file);
}

/**
 * Helper function to get poll ratings if the canonical function isn't available
 */
if (pollify_can_define_function('pollify_get_system_poll_ratings')) {
    pollify_declare_function('pollify_get_system_poll_ratings', function($poll_id) {
        // Try to use the canonical function first
        if (!function_exists('pollify_get_poll_ratings')) {
            pollify_require_function('pollify_get_poll_ratings');
        }
        
        if (function_exists('pollify_get_poll_ratings')) {
            return pollify_get_poll_ratings($poll_id);
        }
        
        // Fallback implementation
        global $wpdb;
        $table_name = $wpdb->prefix . 'pollify_ratings';
        
        $upvotes = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND rating = 1",
            $poll_id
        ));
        
        $downvotes = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND rating = 0",
            $poll_id
        ));
        
        return array(
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
            'total' => $upvotes + $downvotes
        );
    }, $current_file);
}
