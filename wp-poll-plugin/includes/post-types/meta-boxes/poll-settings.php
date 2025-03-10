
<?php
/**
 * Poll settings meta box
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll settings meta box
 */
function pollify_poll_settings_callback($post) {
    // Get current values
    $end_date = get_post_meta($post->ID, '_poll_end_date', true);
    $show_results = get_post_meta($post->ID, '_poll_show_results', true);
    $results_display = get_post_meta($post->ID, '_poll_results_display', true);
    $allow_comments = get_post_meta($post->ID, '_poll_allow_comments', true);
    $allowed_roles = get_post_meta($post->ID, '_poll_allowed_roles', true);
    $poll_type = pollify_get_poll_type($post->ID);
    
    if (!$results_display) {
        $results_display = 'bar';
    }
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = array('all');
    }
    
    // Get all user roles
    $roles = get_editable_roles();
    ?>
    <p>
        <label for="_poll_type"><?php _e('Poll Type:', 'pollify'); ?></label>
        <select name="_poll_type" id="_poll_type" class="widefat">
            <?php
            $terms = get_terms(array(
                'taxonomy' => 'poll_type',
                'hide_empty' => false,
            ));
            
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($poll_type, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            ?>
        </select>
    </p>
    
    <p>
        <label for="_poll_end_date"><?php _e('End Date (optional):', 'pollify'); ?></label>
        <input 
            type="datetime-local" 
            id="_poll_end_date" 
            name="_poll_end_date" 
            value="<?php echo esc_attr($end_date); ?>" 
            class="widefat"
        >
        <span class="description"><?php _e('Leave empty for no end date', 'pollify'); ?></span>
    </p>
    
    <p>
        <label for="_poll_show_results">
            <input 
                type="checkbox" 
                id="_poll_show_results" 
                name="_poll_show_results" 
                value="1" 
                <?php checked($show_results, '1'); ?>
            >
            <?php _e('Always show results', 'pollify'); ?>
        </label>
    </p>
    
    <p>
        <label for="_poll_results_display"><?php _e('Results Display:', 'pollify'); ?></label>
        <select name="_poll_results_display" id="_poll_results_display" class="widefat">
            <option value="bar" <?php selected($results_display, 'bar'); ?>><?php _e('Bar Chart', 'pollify'); ?></option>
            <option value="pie" <?php selected($results_display, 'pie'); ?>><?php _e('Pie Chart', 'pollify'); ?></option>
            <option value="donut" <?php selected($results_display, 'donut'); ?>><?php _e('Donut Chart', 'pollify'); ?></option>
            <option value="text" <?php selected($results_display, 'text'); ?>><?php _e('Text Only', 'pollify'); ?></option>
        </select>
    </p>
    
    <p>
        <label for="_poll_allow_comments">
            <input 
                type="checkbox" 
                id="_poll_allow_comments" 
                name="_poll_allow_comments" 
                value="1" 
                <?php checked($allow_comments, '1'); ?>
            >
            <?php _e('Allow comments', 'pollify'); ?>
        </label>
    </p>
    
    <p><?php _e('Who can vote:', 'pollify'); ?></p>
    <ul>
        <li>
            <label>
                <input 
                    type="checkbox" 
                    name="_poll_allowed_roles[]" 
                    value="all" 
                    <?php checked(in_array('all', $allowed_roles), true); ?>
                >
                <?php _e('Everyone (including not logged in)', 'pollify'); ?>
            </label>
        </li>
        <?php foreach ($roles as $role_key => $role) : ?>
        <li>
            <label>
                <input 
                    type="checkbox" 
                    name="_poll_allowed_roles[]" 
                    value="<?php echo esc_attr($role_key); ?>" 
                    <?php checked(in_array($role_key, $allowed_roles), true); ?>
                >
                <?php echo esc_html($role['name']); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
}
