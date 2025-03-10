<?php
/**
 * Poll form rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll voting form
 */
function pollify_render_poll_form($poll_id, $poll, $options, $poll_type, $show_view_results_link = true) {
    $form_id = 'pollify-form-' . $poll_id;
    
    ob_start();
    ?>
    <form id="<?php echo esc_attr($form_id); ?>" class="pollify-poll-form pollify-poll-type-<?php echo esc_attr($poll_type); ?>" method="post">
        <?php wp_nonce_field('pollify_vote_' . $poll_id, 'pollify_vote_nonce'); ?>
        <input type="hidden" name="poll_id" value="<?php echo esc_attr($poll_id); ?>">
        
        <div class="pollify-poll-options-list">
            <?php 
            switch ($poll_type) {
                case 'binary':
                    echo pollify_render_binary_options($poll_id, $options);
                    break;
                    
                case 'check-all':
                    echo pollify_render_checkbox_options($poll_id, $options);
                    break;
                    
                case 'image-based':
                    echo pollify_render_image_options($poll_id, $options);
                    break;
                    
                case 'rating-scale':
                    echo pollify_render_rating_options($poll_id, $options);
                    break;
                
                case 'ranked-choice':
                    echo pollify_render_ranked_options($poll_id, $options);
                    break;
                
                case 'open-ended':
                    echo pollify_render_open_ended_options($poll_id, $options);
                    break;
                
                case 'quiz':
                    echo pollify_render_quiz_options($poll_id, $options);
                    break;

                case 'interactive':
                    echo pollify_render_interactive_options($poll_id, $options);
                    break;

                case 'opinion':
                case 'straw':
                case 'referendum':
                    // These types use the standard multiple choice UI
                    echo pollify_render_radio_options($poll_id, $options);
                    break;
                    
                case 'multiple-choice':
                default:
                    echo pollify_render_radio_options($poll_id, $options);
                    break;
            }
            ?>
        </div>
        
        <div class="pollify-poll-actions">
            <button type="submit" class="pollify-submit-vote"><?php _e('Vote', 'pollify'); ?></button>
            
            <?php if ($show_view_results_link): ?>
            <a href="<?php echo esc_url(add_query_arg('results', '1')); ?>" class="pollify-view-results"><?php _e('View Results', 'pollify'); ?></a>
            <?php endif; ?>
        </div>
    </form>
    
    <div class="pollify-loading-indicator" style="display: none;">
        <span class="pollify-loader"></span>
        <span class="pollify-loading-text"><?php _e('Processing your vote...', 'pollify'); ?></span>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render standard radio button options
 */
function pollify_render_radio_options($poll_id, $options) {
    ob_start();
    
    foreach ($options as $option_id => $option_text) :
    ?>
    <div class="pollify-poll-option">
        <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
            <input 
                type="radio" 
                name="option_id" 
                id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                value="<?php echo esc_attr($option_id); ?>" 
                required
            >
            <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
        </label>
    </div>
    <?php 
    endforeach;
    
    return ob_get_clean();
}

/**
 * Render checkbox options (for multi-select polls)
 */
function pollify_render_checkbox_options($poll_id, $options) {
    ob_start();
    
    foreach ($options as $option_id => $option_text) :
    ?>
    <div class="pollify-poll-option">
        <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
            <input 
                type="checkbox" 
                name="option_id[]" 
                id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                value="<?php echo esc_attr($option_id); ?>"
            >
            <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
        </label>
    </div>
    <?php 
    endforeach;
    
    return ob_get_clean();
}

/**
 * Render binary (yes/no) options
 */
function pollify_render_binary_options($poll_id, $options) {
    ob_start();
    
    // Use just the first two options, regardless of how many are stored
    $option_keys = array_keys($options);
    $yes_option_id = isset($option_keys[0]) ? $option_keys[0] : '1';
    $no_option_id = isset($option_keys[1]) ? $option_keys[1] : '2';
    
    $yes_text = isset($options[$yes_option_id]) ? $options[$yes_option_id] : __('Yes', 'pollify');
    $no_text = isset($options[$no_option_id]) ? $options[$no_option_id] : __('No', 'pollify');
    ?>
    <div class="pollify-poll-options-binary">
        <div class="pollify-poll-option pollify-poll-option-yes">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($yes_option_id); ?>" class="pollify-option-label pollify-option-yes">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($yes_option_id); ?>" 
                    value="<?php echo esc_attr($yes_option_id); ?>" 
                    required
                >
                <span class="pollify-option-text"><?php echo esc_html($yes_text); ?></span>
            </label>
        </div>
        
        <div class="pollify-poll-option pollify-poll-option-no">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($no_option_id); ?>" class="pollify-option-label pollify-option-no">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($no_option_id); ?>" 
                    value="<?php echo esc_attr($no_option_id); ?>" 
                    required
                >
                <span class="pollify-option-text"><?php echo esc_html($no_text); ?></span>
            </label>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render image-based options
 */
function pollify_render_image_options($poll_id, $options) {
    $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    
    ob_start();
    ?>
    <div class="pollify-poll-options-images">
        <?php 
        foreach ($options as $option_id => $option_text) :
            $image_id = isset($option_images[$option_id]) ? $option_images[$option_id] : 0;
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        ?>
        <div class="pollify-poll-option pollify-poll-option-image">
            <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-option-label">
                <input 
                    type="radio" 
                    name="option_id" 
                    id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                    value="<?php echo esc_attr($option_id); ?>" 
                    required
                >
                
                <?php if ($image_url) : ?>
                <div class="pollify-option-image">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($option_text); ?>">
                </div>
                <?php endif; ?>
                
                <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
            </label>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render rating scale options
 */
function pollify_render_rating_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-rating">
        <div class="pollify-rating-scale">
            <?php 
            // Get min and max labels
            $option_keys = array_keys($options);
            $min_option_id = isset($option_keys[0]) ? $option_keys[0] : '1';
            $max_option_id = isset($option_keys[count($option_keys) - 1]) ? $option_keys[count($option_keys) - 1] : '5';
            
            $min_label = isset($options[$min_option_id]) ? $options[$min_option_id] : '';
            $max_label = isset($options[$max_option_id]) ? $options[$max_option_id] : '';
            ?>
            
            <?php if ($min_label) : ?>
            <div class="pollify-rating-label pollify-rating-min"><?php echo esc_html($min_label); ?></div>
            <?php endif; ?>
            
            <div class="pollify-rating-options">
                <?php foreach ($options as $option_id => $option_text) : ?>
                <div class="pollify-rating-option">
                    <label for="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" class="pollify-rating-value">
                        <input 
                            type="radio" 
                            name="option_id" 
                            id="pollify-option-<?php echo esc_attr($poll_id); ?>-<?php echo esc_attr($option_id); ?>" 
                            value="<?php echo esc_attr($option_id); ?>" 
                            required
                        >
                        <span><?php echo esc_html($option_text); ?></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($max_label) : ?>
            <div class="pollify-rating-label pollify-rating-max"><?php echo esc_html($max_label); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render ranked-choice options
 */
function pollify_render_ranked_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-ranked">
        <p class="pollify-ranked-instructions"><?php _e('Drag options to rank them in your preferred order.', 'pollify'); ?></p>
        <ul class="pollify-ranked-list" id="pollify-ranked-list-<?php echo esc_attr($poll_id); ?>">
            <?php foreach ($options as $option_id => $option_text) : ?>
            <li class="pollify-ranked-item" data-option-id="<?php echo esc_attr($option_id); ?>">
                <div class="pollify-ranked-handle">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 6H16M8 12H16M8 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="pollify-ranked-text"><?php echo esc_html($option_text); ?></div>
                <input type="hidden" name="ranked_options[]" value="<?php echo esc_attr($option_id); ?>">
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple drag and drop functionality
        const list = document.getElementById('pollify-ranked-list-<?php echo esc_js($poll_id); ?>');
        let draggedItem = null;
        
        const items = list.querySelectorAll('.pollify-ranked-item');
        items.forEach(item => {
            item.addEventListener('dragstart', function() {
                draggedItem = item;
                setTimeout(function() {
                    item.style.display = 'none';
                }, 0);
            });
            
            item.addEventListener('dragend', function() {
                setTimeout(function() {
                    draggedItem.style.display = 'flex';
                    draggedItem = null;
                }, 0);
            });
            
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            
            item.addEventListener('dragenter', function(e) {
                e.preventDefault();
                this.style.borderTop = '2px solid #3b82f6';
            });
            
            item.addEventListener('dragleave', function() {
                this.style.borderTop = '1px solid transparent';
            });
            
            item.addEventListener('drop', function() {
                this.style.borderTop = '1px solid transparent';
                if (draggedItem !== this) {
                    let children = Array.from(list.children);
                    let draggedPos = children.indexOf(draggedItem);
                    let targetPos = children.indexOf(this);
                    
                    if (draggedPos < targetPos) {
                        list.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        list.insertBefore(draggedItem, this);
                    }
                    
                    // Update hidden inputs to reflect new order
                    const updatedItems = list.querySelectorAll('.pollify-ranked-item');
                    updatedItems.forEach((item, index) => {
                        const input = item.querySelector('input[name="ranked_options[]"]');
                        input.value = item.dataset.optionId;
                    });
                }
            });
            
            // Make items draggable
            item.setAttribute('draggable', true);
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Render open-ended options
 */
function pollify_render_open_ended_options($poll_id, $options) {
    ob_start();
    ?>
    <div class="pollify-poll-options-open-ended">
        <div class="pollify-poll-option">
            <label for="pollify-open-response-<?php echo esc_attr($poll_id); ?>" class="pollify-option-label">
                <?php 
                // Use the first option text as the prompt, or fallback to default
                $prompt = !empty($options) && is_array($options) && count($options) > 0 
                    ? reset($options) 
                    : __('Enter your response', 'pollify');
                ?>
                <span class="pollify-option-text"><?php echo esc_html($prompt); ?></span>
            </label>
            <textarea 
                id="pollify-open-response-<?php echo esc_attr($poll_id); ?>" 
                name="open_response" 
                class="pollify-open-response-input" 
                rows="4" 
                placeholder="<?php esc_attr_e('Type your answer here...', 'pollify'); ?>" 
                required
            ></textarea>
            <input type="hidden" name="option_id" value="open_response">
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render quiz options
 */
function pollify_render_quiz_options($poll_id, $options) {
    // For front-end, quiz options look like regular radio buttons
    // The correct answer is only revealed after voting
    return pollify_render_radio_options($poll_id, $options);
}

/**
 * Render interactive poll options
 */
function pollify_render_interactive_options($poll_id, $options) {
    // Get interactive poll settings
    $interactive_settings = get_post_meta($poll_id, '_poll_interactive_settings', true);
    $interaction_type = isset($interactive_settings['interaction_type']) ? $interactive_settings['interaction_type'] : 'slider';
    
    ob_start();
    ?>
    <div class="pollify-poll-options-interactive" data-interaction-type="<?php echo esc_attr($interaction_type); ?>">
        <?php 
        switch ($interaction_type) {
            case 'slider':
                echo pollify_render_interactive_slider($poll_id, $options, $interactive_settings);
                break;
                
            case 'drag-drop':
                echo pollify_render_interactive_drag_drop($poll_id, $options, $interactive_settings);
                break;
                
            case 'map':
                echo pollify_render_interactive_map($poll_id, $options, $interactive_settings);
                break;
                
            case 'budget':
                echo pollify_render_interactive_budget($poll_id, $options, $interactive_settings);
                break;
                
            default:
                echo pollify_render_interactive_slider($poll_id, $options, $interactive_settings);
                break;
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render interactive slider poll
 */
function pollify_render_interactive_slider($poll_id, $options, $settings) {
    $min = isset($settings['min']) ? intval($settings['min']) : 0;
    $max = isset($settings['max']) ? intval($settings['max']) : 100;
    $step = isset($settings['step']) ? intval($settings['step']) : 1;
    $default = isset($settings['default']) ? intval($settings['default']) : floor(($min + $max) / 2);
    
    $slider_id = 'pollify-slider-' . $poll_id;
    
    ob_start();
    ?>
    <div class="pollify-interactive-slider">
        <div class="pollify-slider-container">
            <div class="pollify-slider-labels">
                <span class="pollify-slider-min"><?php echo esc_html($min); ?></span>
                <span class="pollify-slider-max"><?php echo esc_html($max); ?></span>
            </div>
            <input 
                type="range" 
                id="<?php echo esc_attr($slider_id); ?>" 
                name="interactive_value" 
                min="<?php echo esc_attr($min); ?>" 
                max="<?php echo esc_attr($max); ?>" 
                step="<?php echo esc_attr($step); ?>" 
                value="<?php echo esc_attr($default); ?>" 
                class="pollify-slider"
            >
            <div class="pollify-slider-value">
                <span id="<?php echo esc_attr($slider_id); ?>-value"><?php echo esc_html($default); ?></span>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var slider = document.getElementById('<?php echo esc_js($slider_id); ?>');
            var output = document.getElementById('<?php echo esc_js($slider_id); ?>-value');
            
            output.innerHTML = slider.value;
            
            slider.oninput = function() {
                output.innerHTML = this.value;
            }
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render interactive drag and drop poll
 */
function pollify_render_interactive_drag_drop($poll_id, $options, $settings) {
    $container_id = 'pollify-drag-drop-' . $poll_id;
    
    ob_start();
    ?>
    <div class="pollify-interactive-drag-drop" id="<?php echo esc_attr($container_id); ?>">
        <div class="pollify-drag-items">
            <?php foreach ($options as $option_id => $option_text) : ?>
            <div class="pollify-drag-item" draggable="true" data-option-id="<?php echo esc_attr($option_id); ?>">
                <?php echo esc_html($option_text); ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pollify-drop-zones">
            <?php 
            $zones = isset($settings['zones']) ? $settings['zones'] : array('Zone 1', 'Zone 2');
            foreach ($zones as $zone_id => $zone_name) : 
            ?>
            <div class="pollify-drop-zone" data-zone-id="<?php echo esc_attr($zone_id); ?>">
                <h4><?php echo esc_html($zone_name); ?></h4>
                <div class="pollify-drop-zone-items"></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <input type="hidden" name="interactive_value" id="<?php echo esc_attr($container_id); ?>-value">
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('<?php echo esc_js($container_id); ?>');
            var items = container.querySelectorAll('.pollify-drag-item');
            var zones = container.querySelectorAll('.pollify-drop-zone');
            var valueInput = document.getElementById('<?php echo esc_js($container_id); ?>-value');
            
            // Initialize drag and drop functionality
            items.forEach(function(item) {
                item.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', item.dataset.optionId);
                    setTimeout(function() {
                        item.classList.add('dragging');
                    }, 0);
                });
                
                item.addEventListener('dragend', function() {
                    item.classList.remove('dragging');
                    updateValue();
                });
            });
            
            zones.forEach(function(zone) {
                zone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });
                
                zone.addEventListener('dragleave', function() {
                    zone.classList.remove('drag-over');
                });
                
                zone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    
                    var optionId = e.dataTransfer.getData('text/plain');
                    var item = container.querySelector('.pollify-drag-item[data-option-id="' + optionId + '"]');
                    
                    if (item) {
                        var itemsContainer = zone.querySelector('.pollify-drop-zone-items');
                        itemsContainer.appendChild(item);
                    }
                    
                    updateValue();
                });
            });
            
            function updateValue() {
                var result = {};
                
                zones.forEach(function(zone) {
                    var zoneId = zone.dataset.zoneId;
                    var zoneItems = zone.querySelectorAll('.pollify-drag-item');
                    
                    result[zoneId] = [];
                    zoneItems.forEach(function(item) {
                        result[zoneId].push(item.dataset.optionId);
                    });
                });
                
                valueInput.value = JSON.stringify(result);
            }
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render interactive map poll
 */
function pollify_render_interactive_map($poll_id, $options, $settings) {
    $map_id = 'pollify-map-' . $poll_id;
    $map_type = isset($settings['map_type']) ? $settings['map_type'] : 'world';
    
    ob_start();
    ?>
    <div class="pollify-interactive-map">
        <div id="<?php echo esc_attr($map_id); ?>" class="pollify-map-container" data-map-type="<?php echo esc_attr($map_type); ?>">
            <div class="pollify-map-loading"><?php _e('Loading map...', 'pollify'); ?></div>
        </div>
        
        <input type="hidden" name="interactive_value" id="<?php echo esc_attr($map_id); ?>-value">
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // This would be implemented with a mapping library like Leaflet or Google Maps
            var mapContainer = document.getElementById('<?php echo esc_js($map_id); ?>');
            var valueInput = document.getElementById('<?php echo esc_js($map_id); ?>-value');
            
            // Placeholder for map implementation
            mapContainer.innerHTML = '<div class="pollify-map-placeholder"><?php _e('Interactive map would be displayed here', 'pollify'); ?></div>';
            valueInput.value = JSON.stringify({selected: "none"});
            
            // In a real implementation, we would initialize the map here
            // and set up event listeners to update the value input
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render interactive budget allocation poll
 */
function pollify_render_interactive_budget($poll_id, $options, $settings) {
    $budget_id = 'pollify-budget-' . $poll_id;
    $total_budget = isset($settings['total_budget']) ? intval($settings['total_budget']) : 100;
    $min_allocation = isset($settings['min_allocation']) ? intval($settings['min_allocation']) : 0;
    $max_allocation = isset($settings['max_allocation']) ? intval($settings['max_allocation']) : $total_budget;
    
    ob_start();
    ?>
    <div class="pollify-interactive-budget" id="<?php echo esc_attr($budget_id); ?>">
        <div class="pollify-budget-info">
            <div class="pollify-budget-total">
                <?php printf(__('Total Budget: %s', 'pollify'), '<span class="pollify-budget-amount">' . esc_html($total_budget) . '</span>'); ?>
            </div>
            <div class="pollify-budget-remaining">
                <?php printf(__('Remaining: %s', 'pollify'), '<span id="' . esc_attr($budget_id) . '-remaining">' . esc_html($total_budget) . '</span>'); ?>
            </div>
        </div>
        
        <div class="pollify-budget-options">
            <?php foreach ($options as $option_id => $option_text) : ?>
            <div class="pollify-budget-option">
                <label for="<?php echo esc_attr($budget_id . '-option-' . $option_id); ?>" class="pollify-budget-label">
                    <?php echo esc_html($option_text); ?>
                </label>
                <div class="pollify-budget-controls">
                    <input 
                        type="number" 
                        id="<?php echo esc_attr($budget_id . '-option-' . $option_id); ?>" 
                        name="budget_allocation[<?php echo esc_attr($option_id); ?>]" 
                        min="<?php echo esc_attr($min_allocation); ?>" 
                        max="<?php echo esc_attr($max_allocation); ?>" 
                        value="0" 
                        class="pollify-budget-input" 
                        data-option-id="<?php echo esc_attr($option_id); ?>"
                    >
                    <div class="pollify-budget-slider-container">
                        <input 
                            type="range" 
                            min="<?php echo esc_attr($min_allocation); ?>" 
                            max="<?php echo esc_attr($max_allocation); ?>" 
                            value="0" 
                            class="pollify-budget-slider" 
                            data-option-id="<?php echo esc_attr($option_id); ?>"
                        >
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <input type="hidden" name="interactive_value" id="<?php echo esc_attr($budget_id); ?>-value">
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('<?php echo esc_js($budget_id); ?>');
            var inputs = container.querySelectorAll('.pollify-budget-input');
            var sliders = container.querySelectorAll('.pollify-budget-slider');
            var remainingEl = document.getElementById('<?php echo esc_js($budget_id); ?>-remaining');
            var valueInput = document.getElementById('<?php echo esc_js($budget_id); ?>-value');
            var totalBudget = <?php echo esc_js($total_budget); ?>;
            
            // Initialize budget functionality
            function updateBudget() {
                var totalAllocated = 0;
                var allocations = {};
                
                inputs.forEach(function(input) {
                    var value = parseInt(input.value, 10) || 0;
                    totalAllocated += value;
                    allocations[input.dataset.optionId] = value;
                });
                
                var remaining = totalBudget - totalAllocated;
                remainingEl.textContent = remaining;
                
                if (remaining < 0) {
                    remainingEl.classList.add('pollify-budget-overallocated');
                } else {
                    remainingEl.classList.remove('pollify-budget-overallocated');
                }
                
                valueInput.value = JSON.stringify(allocations);
            }
            
            inputs.forEach(function(input, index) {
                input.addEventListener('input', function() {
                    var value = parseInt(input.value, 10) || 0;
                    sliders[index].value = value;
                    updateBudget();
                });
            });
            
            sliders.forEach(function(slider, index) {
                slider.addEventListener('input', function() {
                    var value = slider.value;
                    inputs[index].value = value;
                    updateBudget();
                });
            });
            
            // Initialize
            updateBudget();
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render message about user's vote
 */
function pollify_render_user_vote_info($user_vote) {
    if (!$user_vote) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="pollify-user-vote-info">
        <p>
            <?php 
            printf(
                __('You voted for "%s" on %s', 'pollify'),
                esc_html($user_vote->option_text),
                date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($user_vote->voted_at))
            ); 
            ?>
        </p>
    </div>
    <?php
    return ob_get_clean();
}
