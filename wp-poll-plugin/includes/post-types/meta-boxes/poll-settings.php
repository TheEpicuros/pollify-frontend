
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
    $interactive_settings = get_post_meta($post->ID, '_poll_interactive_settings', true);
    
    if (!$results_display) {
        $results_display = 'bar';
    }
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = array('all');
    }
    
    if (!is_array($interactive_settings)) {
        $interactive_settings = array(
            'interaction_type' => 'slider',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'default' => 50
        );
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
    
    <!-- Interactive Poll Settings -->
    <div id="pollify-interactive-settings" class="pollify-conditional-settings" style="display: <?php echo $poll_type === 'interactive' ? 'block' : 'none'; ?>;">
        <h4><?php _e('Interactive Poll Settings', 'pollify'); ?></h4>
        
        <p>
            <label for="_poll_interaction_type"><?php _e('Interaction Type:', 'pollify'); ?></label>
            <select name="_poll_interactive_settings[interaction_type]" id="_poll_interaction_type" class="widefat">
                <option value="slider" <?php selected($interactive_settings['interaction_type'], 'slider'); ?>><?php _e('Slider', 'pollify'); ?></option>
                <option value="drag-drop" <?php selected($interactive_settings['interaction_type'], 'drag-drop'); ?>><?php _e('Drag and Drop', 'pollify'); ?></option>
                <option value="map" <?php selected($interactive_settings['interaction_type'], 'map'); ?>><?php _e('Interactive Map', 'pollify'); ?></option>
                <option value="budget" <?php selected($interactive_settings['interaction_type'], 'budget'); ?>><?php _e('Budget Allocation', 'pollify'); ?></option>
            </select>
        </p>
        
        <div id="pollify-slider-settings" class="pollify-interaction-settings" style="display: <?php echo $interactive_settings['interaction_type'] === 'slider' ? 'block' : 'none'; ?>;">
            <p>
                <label for="_poll_slider_min"><?php _e('Minimum Value:', 'pollify'); ?></label>
                <input type="number" id="_poll_slider_min" name="_poll_interactive_settings[min]" value="<?php echo esc_attr($interactive_settings['min']); ?>" class="small-text">
            </p>
            <p>
                <label for="_poll_slider_max"><?php _e('Maximum Value:', 'pollify'); ?></label>
                <input type="number" id="_poll_slider_max" name="_poll_interactive_settings[max]" value="<?php echo esc_attr($interactive_settings['max']); ?>" class="small-text">
            </p>
            <p>
                <label for="_poll_slider_step"><?php _e('Step:', 'pollify'); ?></label>
                <input type="number" id="_poll_slider_step" name="_poll_interactive_settings[step]" value="<?php echo esc_attr($interactive_settings['step']); ?>" class="small-text">
            </p>
            <p>
                <label for="_poll_slider_default"><?php _e('Default Value:', 'pollify'); ?></label>
                <input type="number" id="_poll_slider_default" name="_poll_interactive_settings[default]" value="<?php echo esc_attr($interactive_settings['default']); ?>" class="small-text">
            </p>
        </div>
        
        <div id="pollify-budget-settings" class="pollify-interaction-settings" style="display: <?php echo $interactive_settings['interaction_type'] === 'budget' ? 'block' : 'none'; ?>;">
            <p>
                <label for="_poll_budget_total"><?php _e('Total Budget:', 'pollify'); ?></label>
                <input type="number" id="_poll_budget_total" name="_poll_interactive_settings[total_budget]" value="<?php echo esc_attr($interactive_settings['total_budget'] ?? 100); ?>" class="small-text">
            </p>
            <p>
                <label for="_poll_budget_min"><?php _e('Minimum Allocation:', 'pollify'); ?></label>
                <input type="number" id="_poll_budget_min" name="_poll_interactive_settings[min_allocation]" value="<?php echo esc_attr($interactive_settings['min_allocation'] ?? 0); ?>" class="small-text">
            </p>
            <p>
                <label for="_poll_budget_max"><?php _e('Maximum Allocation:', 'pollify'); ?></label>
                <input type="number" id="_poll_budget_max" name="_poll_interactive_settings[max_allocation]" value="<?php echo esc_attr($interactive_settings['max_allocation'] ?? 100); ?>" class="small-text">
            </p>
        </div>
        
        <div id="pollify-map-settings" class="pollify-interaction-settings" style="display: <?php echo $interactive_settings['interaction_type'] === 'map' ? 'block' : 'none'; ?>;">
            <p>
                <label for="_poll_map_type"><?php _e('Map Type:', 'pollify'); ?></label>
                <select id="_poll_map_type" name="_poll_interactive_settings[map_type]" class="widefat">
                    <option value="world" <?php selected($interactive_settings['map_type'] ?? 'world', 'world'); ?>><?php _e('World', 'pollify'); ?></option>
                    <option value="us" <?php selected($interactive_settings['map_type'] ?? 'world', 'us'); ?>><?php _e('United States', 'pollify'); ?></option>
                    <option value="europe" <?php selected($interactive_settings['map_type'] ?? 'world', 'europe'); ?>><?php _e('Europe', 'pollify'); ?></option>
                </select>
            </p>
        </div>
    </div>
    
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
    
    <script>
    jQuery(document).ready(function($) {
        // Show/hide interactive settings based on poll type
        $('#_poll_type').on('change', function() {
            if ($(this).val() === 'interactive') {
                $('#pollify-interactive-settings').show();
            } else {
                $('#pollify-interactive-settings').hide();
            }
        });
        
        // Show/hide interactive type settings
        $('#_poll_interaction_type').on('change', function() {
            $('.pollify-interaction-settings').hide();
            
            var type = $(this).val();
            if (type === 'slider') {
                $('#pollify-slider-settings').show();
            } else if (type === 'budget') {
                $('#pollify-budget-settings').show();
            } else if (type === 'map') {
                $('#pollify-map-settings').show();
            }
        });
    });
    </script>
    <?php
}
