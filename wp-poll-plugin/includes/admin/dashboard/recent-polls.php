
<?php
/**
 * Dashboard recent polls widget
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the recent polls widget
 */
function pollify_render_recent_polls_widget() {
    ?>
    <div class="pollify-widget pollify-recent-polls-widget">
        <h2><?php _e('Recent Polls', 'pollify'); ?></h2>
        
        <?php
        $recent_polls = get_posts(array(
            'post_type' => 'poll',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($recent_polls) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>' . __('Title', 'pollify') . '</th>';
            echo '<th>' . __('Author', 'pollify') . '</th>';
            echo '<th>' . __('Votes', 'pollify') . '</th>';
            echo '<th>' . __('Date', 'pollify') . '</th>';
            echo '</tr></thead><tbody>';
            
            foreach ($recent_polls as $poll) {
                $vote_counts = pollify_get_vote_counts($poll->ID);
                $total_votes = array_sum($vote_counts);
                $author = get_the_author_meta('display_name', $poll->post_author);
                $date = get_the_date('M j, Y', $poll);
                
                echo '<tr>';
                echo '<td><a href="' . get_edit_post_link($poll->ID) . '">' . esc_html($poll->post_title) . '</a></td>';
                echo '<td>' . esc_html($author) . '</td>';
                echo '<td>' . number_format_i18n($total_votes) . '</td>';
                echo '<td>' . esc_html($date) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No polls found.', 'pollify') . '</p>';
        }
        ?>
        
        <p class="pollify-view-all">
            <a href="<?php echo admin_url('edit.php?post_type=poll'); ?>">
                <?php _e('View All Polls', 'pollify'); ?> →
            </a>
        </p>
    </div>
    <?php
}
