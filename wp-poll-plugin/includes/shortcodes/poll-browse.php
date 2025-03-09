
<?php
/**
 * Poll browse shortcode [pollify_browse]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Poll browse shortcode [pollify_browse]
 */
function pollify_browse_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 10,
        'type' => '',
        'user' => '',
        'voted' => '',
        'columns' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
        'show_filters' => 'yes',
        'pagination' => 'yes',
    ), $atts, 'pollify_browse');
    
    $limit = absint($atts['limit']);
    $columns = max(1, min(4, absint($atts['columns'])));
    $show_filters = $atts['show_filters'] === 'yes';
    $show_pagination = $atts['pagination'] === 'yes';
    
    // Get current page
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    
    // Build query arguments
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'paged' => $paged,
    );
    
    // Apply poll type filter
    if (!empty($atts['type'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'poll_type',
                'field' => 'slug',
                'terms' => explode(',', $atts['type']),
            ),
        );
    }
    
    // Apply user filter
    if (!empty($atts['user'])) {
        if ($atts['user'] === 'current' && is_user_logged_in()) {
            $args['author'] = get_current_user_id();
        } else {
            $args['author_name'] = $atts['user'];
        }
    }
    
    // Get the polls
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<div class="pollify-info">' . __('No polls found.', 'pollify') . '</div>';
    }
    
    // Get current user's voted polls if needed
    $user_voted_polls = array();
    $current_user_id = get_current_user_id();
    $user_ip = pollify_get_user_ip();
    
    if (!empty($atts['voted']) || $show_filters) {
        global $wpdb;
        $votes_table = $wpdb->prefix . 'pollify_votes';
        
        $query_condition = '';
        $query_params = array();
        
        if ($current_user_id) {
            $query_condition = 'user_id = %d';
            $query_params[] = $current_user_id;
        } else {
            $query_condition = 'user_ip = %s';
            $query_params[] = $user_ip;
        }
        
        $voted_polls = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT poll_id FROM $votes_table WHERE $query_condition",
            $query_params
        ));
        
        foreach ($voted_polls as $poll_id) {
            $user_voted_polls[$poll_id] = true;
        }
    }
    
    // Filter by voted status if requested
    $filtered_polls = array();
    
    if ($atts['voted'] === 'yes' || $atts['voted'] === 'no') {
        $show_voted = $atts['voted'] === 'yes';
        
        while ($query->have_posts()) {
            $query->the_post();
            $poll_id = get_the_ID();
            $has_voted = isset($user_voted_polls[$poll_id]);
            
            if (($show_voted && $has_voted) || (!$show_voted && !$has_voted)) {
                $filtered_polls[] = $poll_id;
            }
        }
        
        wp_reset_postdata();
        
        if (empty($filtered_polls)) {
            if ($show_voted) {
                return '<div class="pollify-info">' . __('You haven\'t voted on any polls yet.', 'pollify') . '</div>';
            } else {
                return '<div class="pollify-info">' . __('No new polls found to vote on.', 'pollify') . '</div>';
            }
        }
        
        // Create a new query with only the filtered polls
        $args['post__in'] = $filtered_polls;
        $query = new WP_Query($args);
    }
    
    ob_start();
    ?>
    <div class="pollify-browse-container">
        <?php if ($show_filters) : ?>
        <div class="pollify-filters">
            <div class="pollify-filter-buttons">
                <button type="button" class="pollify-filter-button<?php echo empty($atts['voted']) ? ' active' : ''; ?>" data-filter="all">
                    <?php _e('All Polls', 'pollify'); ?>
                </button>
                <button type="button" class="pollify-filter-button<?php echo $atts['voted'] === 'no' ? ' active' : ''; ?>" data-filter="not-voted">
                    <?php _e('Not Voted', 'pollify'); ?>
                </button>
                <button type="button" class="pollify-filter-button<?php echo $atts['voted'] === 'yes' ? ' active' : ''; ?>" data-filter="voted">
                    <?php _e('Voted', 'pollify'); ?>
                </button>
            </div>
            
            <?php if (empty($atts['type'])) : ?>
            <div class="pollify-type-filter">
                <label for="pollify-type-select"><?php _e('Poll Type:', 'pollify'); ?></label>
                <select id="pollify-type-select">
                    <option value=""><?php _e('All Types', 'pollify'); ?></option>
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'poll_type',
                        'hide_empty' => true,
                    ));
                    
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="pollify-polls-list pollify-columns-<?php echo $columns; ?>">
            <?php while ($query->have_posts()) : $query->the_post(); 
                $poll_id = get_the_ID();
                
                // Get vote counts
                $vote_counts = pollify_get_vote_counts($poll_id);
                $total_votes = array_sum($vote_counts);
                
                // Check if user has voted
                $has_voted = isset($user_voted_polls[$poll_id]);
                
                // Check poll status
                $poll_status = pollify_get_poll_status($poll_id);
                $has_ended = $poll_status === 'ended';
                
                // Get poll type
                $poll_type = pollify_get_poll_type($poll_id);
                
                // Get poll image (featured image or first option image for image-based polls)
                $poll_image = '';
                if (has_post_thumbnail($poll_id)) {
                    $poll_image = get_the_post_thumbnail_url($poll_id, 'medium');
                } elseif ($poll_type === 'image-based') {
                    $option_images = get_post_meta($poll_id, '_poll_option_images', true);
                    if (!empty($option_images) && is_array($option_images)) {
                        foreach ($option_images as $img_id) {
                            if (!empty($img_id)) {
                                $poll_image = wp_get_attachment_image_url($img_id, 'medium');
                                break;
                            }
                        }
                    }
                }
            ?>
            <div class="pollify-poll-card <?php echo $has_voted ? 'pollify-voted' : 'pollify-not-voted'; ?> pollify-poll-status-<?php echo $poll_status; ?>">
                <?php if (!empty($poll_image)) : ?>
                <div class="pollify-poll-image">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url($poll_image); ?>" alt="<?php the_title_attribute(); ?>">
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="pollify-poll-content">
                    <h3 class="pollify-poll-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if (has_excerpt()) : ?>
                    <div class="pollify-poll-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="pollify-poll-meta">
                        <span class="pollify-poll-type"><?php echo esc_html(pollify_get_poll_type_name($poll_id)); ?></span>
                        <span class="pollify-poll-votes"><?php echo sprintf(_n('%s vote', '%s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?></span>
                        <span class="pollify-poll-date"><?php echo get_the_date(); ?></span>
                        
                        <?php if ($has_ended) : ?>
                        <span class="pollify-poll-status pollify-ended"><?php _e('Ended', 'pollify'); ?></span>
                        <?php elseif ($poll_status === 'active') : ?>
                        <span class="pollify-poll-status pollify-active"><?php _e('Active', 'pollify'); ?></span>
                        <?php elseif ($poll_status === 'scheduled') : ?>
                        <span class="pollify-poll-status pollify-scheduled"><?php _e('Scheduled', 'pollify'); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pollify-poll-actions">
                        <?php if ($has_voted || $has_ended) : ?>
                        <a href="<?php the_permalink(); ?>" class="pollify-poll-link pollify-view-link">
                            <?php _e('View Results', 'pollify'); ?>
                        </a>
                        <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="pollify-poll-link pollify-vote-link">
                            <?php _e('Vote', 'pollify'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php if ($show_pagination && $query->max_num_pages > 1) : ?>
        <div class="pollify-pagination">
            <?php
            $big = 999999999; // need an unlikely integer
            
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => $query->max_num_pages,
                'prev_text' => '&laquo; ' . __('Previous', 'pollify'),
                'next_text' => __('Next', 'pollify') . ' &raquo;',
            ));
            ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter buttons
        var filterButtons = document.querySelectorAll('.pollify-filter-button');
        
        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var filter = this.getAttribute('data-filter');
                var currentUrl = new URL(window.location.href);
                
                if (filter === 'all') {
                    currentUrl.searchParams.delete('voted');
                } else if (filter === 'not-voted') {
                    currentUrl.searchParams.set('voted', 'no');
                } else if (filter === 'voted') {
                    currentUrl.searchParams.set('voted', 'yes');
                }
                
                window.location.href = currentUrl.toString();
            });
        });
        
        // Type filter
        var typeSelect = document.getElementById('pollify-type-select');
        
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                var type = this.value;
                var currentUrl = new URL(window.location.href);
                
                if (type === '') {
                    currentUrl.searchParams.delete('type');
                } else {
                    currentUrl.searchParams.set('type', type);
                }
                
                window.location.href = currentUrl.toString();
            });
            
            // Set initial value from URL
            var params = new URLSearchParams(window.location.search);
            var typeParam = params.get('type');
            
            if (typeParam) {
                typeSelect.value = typeParam;
            }
        }
    });
    </script>
    <?php
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
