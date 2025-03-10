
<?php
/**
 * AJAX handler for user statistics
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for getting user activity stats
 */
function pollify_ajax_get_user_stats() {
    // Check nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'pollify-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Get user ID
    $user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
    
    // If no user ID provided, try to get current user
    if (!$user_id && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        wp_send_json_error(array('message' => 'User not found.'));
    }
    
    // Get user data
    $user = get_userdata($user_id);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }
    
    // Get user activity stats
    $activity_stats = pollify_get_user_activity_stats($user_id);
    
    // Get polls created by user
    $args = array(
        'post_type' => 'poll',
        'author' => $user_id,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    $polls = get_posts($args);
    $poll_count = count($polls);
    
    // Calculate level and progress
    $points = $activity_stats['total_points'];
    $current_level = 1;
    $next_level_points = 100;
    
    if ($points >= 100) {
        $current_level = 2;
        $next_level_points = 200;
    }
    
    if ($points >= 200) {
        $current_level = 3;
        $next_level_points = 350;
    }
    
    if ($points >= 350) {
        $current_level = 4;
        $next_level_points = 500;
    }
    
    if ($points >= 500) {
        $current_level = 5;
        $next_level_points = 750;
    }
    
    if ($points >= 750) {
        $current_level = 6;
        $next_level_points = 1000;
    }
    
    // Calculate progress percentage to next level
    $level_min = $next_level_points - ($next_level_points - $points < 100 ? 100 : $next_level_points - $points);
    $progress_percentage = min(100, max(0, (($points - $level_min) / ($next_level_points - $level_min)) * 100));
    
    // Get user achievements
    $achievements = array();
    
    // First vote achievement
    $has_voted = $activity_stats['vote_count'] > 0;
    $achievements[] = array(
        'id' => 'first_vote',
        'title' => __('First Vote', 'pollify'),
        'description' => __('Cast your first vote on a poll', 'pollify'),
        'icon' => 'vote',
        'unlocked' => $has_voted,
        'date_unlocked' => $has_voted ? pollify_get_first_activity_date($user_id, 'vote') : null,
    );
    
    // Community voice achievement
    $is_community_voice = $activity_stats['comment_count'] >= 10;
    $achievements[] = array(
        'id' => 'community_voice',
        'title' => __('Community Voice', 'pollify'),
        'description' => __('Leave 10 comments on polls', 'pollify'),
        'icon' => 'message',
        'unlocked' => $is_community_voice,
        'date_unlocked' => $is_community_voice ? pollify_get_achievement_date($user_id, 'comment', 10) : null,
    );
    
    // Poll creator achievement
    $is_poll_creator = $poll_count > 0;
    $achievements[] = array(
        'id' => 'poll_creator',
        'title' => __('Poll Creator', 'pollify'),
        'description' => __('Create your first poll', 'pollify'),
        'icon' => 'chart',
        'unlocked' => $is_poll_creator,
        'date_unlocked' => $is_poll_creator ? get_the_date('Y-m-d', $polls[0]->ID) : null,
    );
    
    // Popular poll achievement
    $has_popular_poll = false;
    $popular_poll_date = null;
    
    foreach ($polls as $poll) {
        $vote_count = pollify_get_total_votes($poll->ID);
        
        if ($vote_count >= 50) {
            $has_popular_poll = true;
            $popular_poll_date = get_the_date('Y-m-d', $poll->ID);
            break;
        }
    }
    
    $achievements[] = array(
        'id' => 'popular_poll',
        'title' => __('Popular Opinion', 'pollify'),
        'description' => __('Create a poll with 50+ votes', 'pollify'),
        'icon' => 'star',
        'unlocked' => $has_popular_poll,
        'date_unlocked' => $popular_poll_date,
    );
    
    // Poll expert achievement
    $is_poll_expert = $points >= 500;
    $achievements[] = array(
        'id' => 'poll_expert',
        'title' => __('Poll Expert', 'pollify'),
        'description' => __('Earn 500 points in the system', 'pollify'),
        'icon' => 'award',
        'unlocked' => $is_poll_expert,
        'date_unlocked' => $is_poll_expert ? pollify_get_achievement_date($user_id, null, 500) : null,
    );
    
    // Prepare response data
    $response_data = array(
        'user_id' => $user_id,
        'user_name' => $user->display_name,
        'total_points' => $activity_stats['total_points'],
        'vote_count' => $activity_stats['vote_count'],
        'comment_count' => $activity_stats['comment_count'],
        'rating_count' => $activity_stats['rating_count'],
        'poll_count' => $poll_count,
        'current_level' => $current_level,
        'next_level_points' => $next_level_points,
        'progress_percentage' => round($progress_percentage),
        'achievements' => $achievements,
    );
    
    wp_send_json_success($response_data);
}
add_action('wp_ajax_pollify_get_user_stats', 'pollify_ajax_get_user_stats');
add_action('wp_ajax_nopriv_pollify_get_user_stats', 'pollify_ajax_get_user_stats');

