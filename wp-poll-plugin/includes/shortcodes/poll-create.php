
<?php
/**
 * Poll creation shortcode [pollify_create]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Poll create shortcode [pollify_create]
 */
function pollify_create_shortcode($atts) {
    // Only logged in users can create polls
    if (!is_user_logged_in()) {
        return '<div class="pollify-error">You must be logged in to create a poll.</div>';
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        return '<div class="pollify-error">You do not have permission to create polls.</div>';
    }
    
    ob_start();
    ?>
    <div id="pollify-create-poll" class="pollify-create-poll">
        <div class="pollify-create-poll-container">
            <h2>Create a New Poll</h2>
            
            <form id="pollify-create-poll-form" class="pollify-create-poll-form">
                <div class="pollify-form-group">
                    <label for="poll-title">Poll Question</label>
                    <input type="text" id="poll-title" name="poll_title" required>
                </div>
                
                <div class="pollify-form-group">
                    <label for="poll-description">Description (optional)</label>
                    <textarea id="poll-description" name="poll_description"></textarea>
                </div>
                
                <div class="pollify-form-group">
                    <label>Options</label>
                    <div id="poll-options-container">
                        <div class="pollify-poll-option-input">
                            <input type="text" name="poll_options[]" required>
                        </div>
                        <div class="pollify-poll-option-input">
                            <input type="text" name="poll_options[]" required>
                        </div>
                    </div>
                    <button type="button" id="add-poll-option-btn">Add Option</button>
                </div>
                
                <div class="pollify-form-submit">
                    <button type="submit">Create Poll</button>
                </div>
                
                <div class="pollify-create-poll-message" style="display: none;"></div>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

