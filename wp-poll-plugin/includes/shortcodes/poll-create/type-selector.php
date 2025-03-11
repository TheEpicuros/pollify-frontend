
<?php
/**
 * Poll type selector grid rendering
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

/**
 * Render the poll type selector grid
 * 
 * @param array $poll_types Array of poll types to display
 */
function pollify_render_poll_type_selector($poll_types) {
    ?>
    <div class="pollify-poll-types-grid">
        <?php foreach ($poll_types as $type_slug => $type_name) : 
            // Get an icon/image for each poll type
            $icon_class = pollify_get_poll_type_icon($type_slug);
        ?>
        <div class="pollify-poll-type-card" data-poll-type="<?php echo esc_attr($type_slug); ?>">
            <div class="pollify-poll-type-icon">
                <span class="dashicons <?php echo esc_attr($icon_class); ?>"></span>
            </div>
            <h3 class="pollify-poll-type-title"><?php echo esc_html($type_name); ?></h3>
            <p class="pollify-poll-type-description">
                <?php 
                // Use the canonical function for getting poll type description
                if (!function_exists('pollify_get_poll_type_description')) {
                    pollify_require_function('pollify_get_poll_type_description');
                }
                echo pollify_get_poll_type_description($type_slug); 
                ?>
            </p>
            <button type="button" class="pollify-select-poll-type">
                <?php _e('Select', 'pollify'); ?>
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Get appropriate icon class for poll type
 * 
 * @param string $type_slug The poll type slug
 * @return string Icon class
 */
function pollify_get_poll_type_icon($type_slug) {
    switch ($type_slug) {
        case 'binary':
            return 'dashicons-yes-no';
        case 'multiple-choice':
            return 'dashicons-list-view';
        case 'check-all':
            return 'dashicons-yes';
        case 'ranked-choice':
            return 'dashicons-sort';
        case 'rating-scale':
            return 'dashicons-star-filled';
        case 'open-ended':
            return 'dashicons-editor-textcolor';
        case 'image-based':
            return 'dashicons-format-image';
        case 'quiz':
            return 'dashicons-clipboard';
        case 'opinion':
            return 'dashicons-testimonial';
        case 'straw':
            return 'dashicons-chart-line';
        case 'interactive':
            return 'dashicons-admin-site-alt3';
        case 'referendum':
            return 'dashicons-tickets-alt';
        default:
            return 'dashicons-chart-bar';
    }
}
