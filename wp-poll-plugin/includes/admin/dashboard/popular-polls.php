
<?php
/**
 * Dashboard popular polls widget
 *
 * @package Pollify
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
 * Render the popular polls widget
 */
function pollify_render_popular_polls_widget() {
    ?>
    <div class="pollify-widget pollify-popular-polls-widget">
        <h2><?php _e('Popular Polls', 'pollify'); ?></h2>
        
        <?php
        $popular_polls = pollify_get_popular_polls(5);
        
        if ($popular_polls) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>' . __('Title', 'pollify') . '</th>';
            echo '<th>' . __('Votes', 'pollify') . '</th>';
            echo '<th>' . __('Author', 'pollify') . '</th>';
            echo '</tr></thead><tbody>';
            
            foreach ($popular_polls as $poll) {
                $author = get_the_author_meta('display_name', $poll->post_author);
                
                echo '<tr>';
                echo '<td><a href="' . get_edit_post_link($poll->ID) . '">' . esc_html($poll->post_title) . '</a></td>';
                echo '<td>' . number_format_i18n($poll->vote_count) . '</td>';
                echo '<td>' . esc_html($author) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No polls with votes yet.', 'pollify') . '</p>';
        }
        ?>
    </div>
    <?php
}

/**
 * Get popular polls - registered as canonical function
 */
if (pollify_can_define_function('pollify_get_popular_polls')) {
    pollify_declare_function('pollify_get_popular_polls', function($limit = 5) {
        global $wpdb;
        
        $votes_table = $wpdb->prefix . 'pollify_votes';
        
        $query = "SELECT p.*, COUNT(v.id) AS vote_count
                FROM {$wpdb->posts} p
                LEFT JOIN $votes_table v ON p.ID = v.poll_id
                WHERE p.post_type = 'poll' AND p.post_status = 'publish'
                GROUP BY p.ID
                ORDER BY vote_count DESC
                LIMIT %d";
        
        $popular_polls = $wpdb->get_results($wpdb->prepare($query, $limit));
        
        return $popular_polls;
    }, $current_file);
}
