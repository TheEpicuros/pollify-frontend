
<?php
/**
 * Security utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate a nonce for poll actions
 * 
 * @param int $poll_id Poll ID
 * @param string $action Action name
 * @return string Generated nonce
 */
function pollify_create_poll_nonce($poll_id, $action = 'vote') {
    return wp_create_nonce('pollify_' . $action . '_' . $poll_id);
}

/**
 * Verify a poll nonce
 * 
 * @param string $nonce Nonce to verify
 * @param int $poll_id Poll ID
 * @param string $action Action name
 * @return bool True if nonce is valid
 */
function pollify_verify_poll_nonce($nonce, $poll_id, $action = 'vote') {
    return wp_verify_nonce($nonce, 'pollify_' . $action . '_' . $poll_id);
}

/**
 * Check for suspicious bot activity
 * 
 * @return bool True if request appears to be from a bot
 */
function pollify_is_bot_request() {
    // Check user agent
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    if (empty($user_agent)) {
        return true;
    }
    
    // Check for common bot identifiers
    $bot_markers = array(
        'bot', 'spider', 'crawler', 'scraper', 'wget', 'curl', 
        'slurp', 'baidu', 'yandex', 'teoma', 'ahrefs'
    );
    
    foreach ($bot_markers as $marker) {
        if (stripos($user_agent, $marker) !== false) {
            return true;
        }
    }
    
    // Check for rapid voting attempts
    $last_vote_time = get_transient('pollify_last_vote_' . pollify_get_client_ip());
    if ($last_vote_time && (time() - $last_vote_time) < 2) {
        return true;
    }
    
    return false;
}

