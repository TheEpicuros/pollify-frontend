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

// Ensure the function pollify_get_popular_polls() is available
require_once plugin_dir_path(__FILE__) . 'analytics.php';


/**
 * Render the popular polls widget
 */
function pollify_render_popular_polls_widget() {
    ?>
    <div class="pollify-widget pollify-popular-polls-widget">
        <h2><?php _e('Popular Polls', 'pollify'); ?></h2>
        
        <?php
        // Fetch popular polls
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
