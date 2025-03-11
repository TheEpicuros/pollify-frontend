
<?php
/**
 * Quiz poll specific rendering functions
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
 * Render quiz results with correct answers
 */
function pollify_render_quiz_results($poll_id, $options, $user_vote) {
    // Get correct answers for this quiz
    $correct_options = get_post_meta($poll_id, '_poll_correct_options', true);
    
    if (!is_array($correct_options) || empty($correct_options)) {
        return '';
    }
    
    $user_option_id = isset($user_vote->option_id) ? $user_vote->option_id : '';
    $is_correct = in_array($user_option_id, $correct_options);
    
    ob_start();
    ?>
    <div class="pollify-quiz-results">
        <div class="pollify-quiz-result <?php echo $is_correct ? 'pollify-quiz-correct' : 'pollify-quiz-incorrect'; ?>">
            <?php if ($is_correct) : ?>
                <div class="pollify-quiz-correct-message">
                    <span class="pollify-quiz-icon pollify-quiz-correct-icon">✓</span>
                    <p><?php _e('Correct!', 'pollify'); ?></p>
                </div>
            <?php else : ?>
                <div class="pollify-quiz-incorrect-message">
                    <span class="pollify-quiz-icon pollify-quiz-incorrect-icon">✗</span>
                    <p><?php _e('Incorrect!', 'pollify'); ?></p>
                    
                    <div class="pollify-quiz-correct-answer">
                        <p><?php _e('The correct answer is:', 'pollify'); ?></p>
                        <ul>
                        <?php 
                        foreach ($correct_options as $correct_id) {
                            if (isset($options[$correct_id])) {
                                echo '<li>' . esc_html($options[$correct_id]) . '</li>';
                            }
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render user vote info - registered as the canonical function
 */
if (pollify_can_define_function('pollify_render_user_vote_info')) {
    function pollify_render_user_vote_info($user_vote) {
        if (!$user_vote) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="pollify-user-vote-info">
            <p><?php _e('Your response has been recorded.', 'pollify'); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }
    pollify_register_function_path('pollify_render_user_vote_info', $current_file);
}
