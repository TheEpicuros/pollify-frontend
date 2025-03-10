
<?php
/**
 * Admin user activity page for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the user activity page
 */
function pollify_user_activity_page() {
    // Set up pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Get activity data
    $activities = pollify_get_user_activities($per_page, $offset);
    $total_activities = pollify_count_user_activities();
    $total_pages = ceil($total_activities / $per_page);
    
    // Filter parameters
    $activity_type = isset($_GET['activity_type']) ? sanitize_text_field($_GET['activity_type']) : '';
    $user_type = isset($_GET['user_type']) ? sanitize_text_field($_GET['user_type']) : '';
    $date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : '';
    ?>
    <div class="wrap pollify-admin-activity">
        <h1><?php _e('User Activity', 'pollify'); ?></h1>
        
        <div class="pollify-activity-filters">
            <form method="get" action="">
                <input type="hidden" name="page" value="pollify-user-activity">
                
                <select name="activity_type">
                    <option value=""><?php _e('All Activity Types', 'pollify'); ?></option>
                    <option value="vote" <?php selected($activity_type, 'vote'); ?>><?php _e('Votes', 'pollify'); ?></option>
                    <option value="comment" <?php selected($activity_type, 'comment'); ?>><?php _e('Comments', 'pollify'); ?></option>
                    <option value="create_poll" <?php selected($activity_type, 'create_poll'); ?>><?php _e('Poll Creation', 'pollify'); ?></option>
                    <option value="rate" <?php selected($activity_type, 'rate'); ?>><?php _e('Ratings', 'pollify'); ?></option>
                </select>
                
                <select name="user_type">
                    <option value=""><?php _e('All Users', 'pollify'); ?></option>
                    <option value="registered" <?php selected($user_type, 'registered'); ?>><?php _e('Registered Users', 'pollify'); ?></option>
                    <option value="guest" <?php selected($user_type, 'guest'); ?>><?php _e('Guest Users', 'pollify'); ?></option>
                </select>
                
                <select name="date_range">
                    <option value=""><?php _e('All Time', 'pollify'); ?></option>
                    <option value="today" <?php selected($date_range, 'today'); ?>><?php _e('Today', 'pollify'); ?></option>
                    <option value="yesterday" <?php selected($date_range, 'yesterday'); ?>><?php _e('Yesterday', 'pollify'); ?></option>
                    <option value="week" <?php selected($date_range, 'week'); ?>><?php _e('Last 7 Days', 'pollify'); ?></option>
                    <option value="month" <?php selected($date_range, 'month'); ?>><?php _e('Last 30 Days', 'pollify'); ?></option>
                </select>
                
                <button type="submit" class="button"><?php _e('Filter', 'pollify'); ?></button>
            </form>
        </div>
        
        <div class="pollify-activity-list">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('User', 'pollify'); ?></th>
                        <th><?php _e('Activity', 'pollify'); ?></th>
                        <th><?php _e('Poll', 'pollify'); ?></th>
                        <th><?php _e('IP Address', 'pollify'); ?></th>
                        <th><?php _e('Date/Time', 'pollify'); ?></th>
                        <th><?php _e('Points', 'pollify'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($activities) : ?>
                        <?php foreach ($activities as $activity) : ?>
                            <tr>
                                <td>
                                    <?php if ($activity->user_id > 0) : ?>
                                        <?php $user = get_userdata($activity->user_id); ?>
                                        <div class="pollify-user-info">
                                            <?php echo get_avatar($activity->user_id, 32); ?>
                                            <div>
                                                <strong><?php echo esc_html($user ? $user->display_name : __('Unknown User', 'pollify')); ?></strong>
                                                <div class="user-role"><?php echo pollify_get_user_role_name($activity->user_id); ?></div>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="pollify-user-info">
                                            <span class="dashicons dashicons-businessman"></span>
                                            <div>
                                                <strong><?php _e('Guest User', 'pollify'); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html(pollify_get_activity_type_label($activity->activity_type)); ?>
                                </td>
                                <td>
                                    <?php if ($activity->poll_id > 0) : ?>
                                        <a href="<?php echo esc_url(get_edit_post_link($activity->poll_id)); ?>">
                                            <?php echo esc_html(get_the_title($activity->poll_id)); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php _e('N/A', 'pollify'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html($activity->user_ip); ?>
                                </td>
                                <td>
                                    <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($activity->activity_date))); ?>
                                </td>
                                <td>
                                    <?php echo esc_html($activity->points); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"><?php _e('No activities found.', 'pollify'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1) : ?>
            <div class="pollify-pagination">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; Previous', 'pollify'),
                    'next_text' => __('Next &raquo;', 'pollify'),
                    'total' => $total_pages,
                    'current' => $current_page
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <style>
    .pollify-admin-activity {
        max-width: 1200px;
    }
    
    .pollify-activity-filters {
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
    }
    
    .pollify-activity-filters select {
        margin-right: 10px;
    }
    
    .pollify-activity-list {
        margin-bottom: 20px;
    }
    
    .pollify-user-info {
        display: flex;
        align-items: center;
    }
    
    .pollify-user-info .dashicons {
        font-size: 32px;
        width: 32px;
        height: 32px;
        margin-right: 10px;
        color: #ccc;
    }
    
    .pollify-user-info img {
        margin-right: 10px;
    }
    
    .pollify-user-info .user-role {
        font-size: 11px;
        color: #777;
    }
    
    .pollify-pagination {
        margin-top: 20px;
        text-align: center;
    }
    
    @media screen and (max-width: 782px) {
        .pollify-activity-filters {
            display: flex;
            flex-direction: column;
        }
        
        .pollify-activity-filters select {
            margin-bottom: 10px;
            width: 100%;
        }
    }
    </style>
    <?php
}

/**
 * Get user activities with pagination and filtering
 */
function pollify_get_user_activities($per_page = 20, $offset = 0) {
    global $wpdb;
    
    $activity_table = $wpdb->prefix . 'pollify_user_activity';
    
    // Build the query
    $query = "SELECT * FROM $activity_table";
    
    // Apply filters
    $where_clauses = array();
    
    if (isset($_GET['activity_type']) && !empty($_GET['activity_type'])) {
        $activity_type = sanitize_text_field($_GET['activity_type']);
        $where_clauses[] = $wpdb->prepare("activity_type = %s", $activity_type);
    }
    
    if (isset($_GET['user_type']) && !empty($_GET['user_type'])) {
        $user_type = sanitize_text_field($_GET['user_type']);
        if ($user_type === 'registered') {
            $where_clauses[] = "user_id > 0";
        } elseif ($user_type === 'guest') {
            $where_clauses[] = "user_id = 0";
        }
    }
    
    if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
        $date_range = sanitize_text_field($_GET['date_range']);
        switch ($date_range) {
            case 'today':
                $where_clauses[] = "DATE(activity_date) = CURDATE()";
                break;
            case 'yesterday':
                $where_clauses[] = "DATE(activity_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'week':
                $where_clauses[] = "activity_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $where_clauses[] = "activity_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
        }
    }
    
    // Combine where clauses
    if (!empty($where_clauses)) {
        $query .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Add order and limit
    $query .= " ORDER BY activity_date DESC LIMIT $offset, $per_page";
    
    // Execute the query
    $results = $wpdb->get_results($query);
    
    return $results;
}

/**
 * Count total number of user activities for pagination
 */
function pollify_count_user_activities() {
    global $wpdb;
    
    $activity_table = $wpdb->prefix . 'pollify_user_activity';
    
    // Build the query
    $query = "SELECT COUNT(*) FROM $activity_table";
    
    // Apply filters
    $where_clauses = array();
    
    if (isset($_GET['activity_type']) && !empty($_GET['activity_type'])) {
        $activity_type = sanitize_text_field($_GET['activity_type']);
        $where_clauses[] = $wpdb->prepare("activity_type = %s", $activity_type);
    }
    
    if (isset($_GET['user_type']) && !empty($_GET['user_type'])) {
        $user_type = sanitize_text_field($_GET['user_type']);
        if ($user_type === 'registered') {
            $where_clauses[] = "user_id > 0";
        } elseif ($user_type === 'guest') {
            $where_clauses[] = "user_id = 0";
        }
    }
    
    if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
        $date_range = sanitize_text_field($_GET['date_range']);
        switch ($date_range) {
            case 'today':
                $where_clauses[] = "DATE(activity_date) = CURDATE()";
                break;
            case 'yesterday':
                $where_clauses[] = "DATE(activity_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'week':
                $where_clauses[] = "activity_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $where_clauses[] = "activity_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
        }
    }
    
    // Combine where clauses
    if (!empty($where_clauses)) {
        $query .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Execute the query
    $count = $wpdb->get_var($query);
    
    return $count;
}

/**
 * Get user role name
 */
function pollify_get_user_role_name($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return __('Guest', 'pollify');
    }
    
    $roles = $user->roles;
    $role = array_shift($roles);
    
    $role_names = array(
        'administrator' => __('Administrator', 'pollify'),
        'editor' => __('Editor', 'pollify'),
        'author' => __('Author', 'pollify'),
        'contributor' => __('Contributor', 'pollify'),
        'subscriber' => __('Subscriber', 'pollify')
    );
    
    return isset($role_names[$role]) ? $role_names[$role] : ucfirst($role);
}

/**
 * Get activity type label
 */
function pollify_get_activity_type_label($type) {
    $labels = array(
        'vote' => __('Voted on a poll', 'pollify'),
        'create_poll' => __('Created a poll', 'pollify'),
        'comment' => __('Commented on a poll', 'pollify'),
        'rate' => __('Rated a poll', 'pollify')
    );
    
    return isset($labels[$type]) ? $labels[$type] : ucfirst($type);
}
