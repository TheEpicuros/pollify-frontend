
<?php
/**
 * Social sharing utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate social sharing buttons HTML
 */
function pollify_get_social_sharing_html($poll_id) {
    $post = get_post($poll_id);
    $permalink = get_permalink($poll_id);
    $title = get_the_title($poll_id);
    $excerpt = has_excerpt($poll_id) ? get_the_excerpt($poll_id) : wp_trim_words($post->post_content, 20);
    
    // Get featured image
    $thumbnail_url = get_the_post_thumbnail_url($poll_id, 'large');
    
    ob_start();
    ?>
    <div class="pollify-social-sharing">
        <h4><?php _e('Share this poll:', 'pollify'); ?></h4>
        
        <div class="pollify-social-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" class="pollify-social-button pollify-facebook" target="_blank" rel="noopener">
                <span class="dashicons dashicons-facebook"></span>
                <span class="pollify-social-text"><?php _e('Facebook', 'pollify'); ?></span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" class="pollify-social-button pollify-twitter" target="_blank" rel="noopener">
                <span class="dashicons dashicons-twitter"></span>
                <span class="pollify-social-text"><?php _e('Twitter', 'pollify'); ?></span>
            </a>
            
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($permalink); ?>&title=<?php echo urlencode($title); ?>&summary=<?php echo urlencode($excerpt); ?>" class="pollify-social-button pollify-linkedin" target="_blank" rel="noopener">
                <span class="dashicons dashicons-linkedin"></span>
                <span class="pollify-social-text"><?php _e('LinkedIn', 'pollify'); ?></span>
            </a>
            
            <a href="mailto:?subject=<?php echo urlencode($title); ?>&body=<?php echo urlencode($excerpt . "\n\n" . $permalink); ?>" class="pollify-social-button pollify-email">
                <span class="dashicons dashicons-email"></span>
                <span class="pollify-social-text"><?php _e('Email', 'pollify'); ?></span>
            </a>
        </div>
    </div>
    
    <!-- Open Graph Meta Tags for better social sharing -->
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($excerpt); ?>">
    <meta property="og:url" content="<?php echo esc_url($permalink); ?>">
    <meta property="og:type" content="article">
    <?php if ($thumbnail_url) : ?>
    <meta property="og:image" content="<?php echo esc_url($thumbnail_url); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($excerpt); ?>">
    <?php if ($thumbnail_url) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($thumbnail_url); ?>">
    <?php endif; ?>
    <?php
    
    return ob_get_clean();
}
