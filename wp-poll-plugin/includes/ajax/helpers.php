
<?php
/**
 * Helper functions for AJAX handlers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper function to get the date of the first activity of a specific type
 */
function pollify_get_first_activity_date($user_id, $activity_type = null) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    $query = "SELECT activity_date FROM $table_name WHERE user_id = %d";
    $params = array($user_id);
    
    if ($activity_type) {
        $query .= " AND activity_type = %s";
        $params[] = $activity_type;
    }
    
    $query .= " ORDER BY activity_date ASC LIMIT 1";
    
    $date = $wpdb->get_var($wpdb->prepare($query, $params));
    
    return $date ? date('Y-m-d', strtotime($date)) : null;
}

/**
 * Helper function to get the date when an achievement was unlocked
 */
function pollify_get_achievement_date($user_id, $activity_type = null, $threshold = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    if ($activity_type && $threshold > 0) {
        // For count-based achievements (like 10 comments)
        $query = "SELECT a1.activity_date 
                  FROM $table_name a1
                  JOIN (
                      SELECT activity_date
                      FROM $table_name
                      WHERE user_id = %d AND activity_type = %s
                      ORDER BY activity_date ASC
                      LIMIT %d
                  ) a2 ON a1.activity_date = a2.activity_date
                  ORDER BY a1.activity_date DESC
                  LIMIT 1";
        
        $date = $wpdb->get_var($wpdb->prepare($query, $user_id, $activity_type, $threshold));
    } elseif ($threshold > 0) {
        // For point-based achievements (like 500 points)
        $query = "SELECT activity_date 
                  FROM $table_name
                  WHERE user_id = %d
                  GROUP BY activity_date
                  HAVING SUM(points) >= %d
                  ORDER BY activity_date ASC
                  LIMIT 1";
        
        $date = $wpdb->get_var($wpdb->prepare($query, $user_id, $threshold));
    } else {
        return null;
    }
    
    return $date ? date('Y-m-d', strtotime($date)) : null;
}
