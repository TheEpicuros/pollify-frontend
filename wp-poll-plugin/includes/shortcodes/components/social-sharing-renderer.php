
<?php
/**
 * Social sharing rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get social sharing HTML
 */
function pollify_get_social_sharing_html($poll_id) {
    $permalink = get_permalink($poll_id);
    $title = get_the_title($poll_id);
    
    ob_start();
    ?>
    <div class="pollify-social-sharing">
        <span class="pollify-share-text"><?php _e('Share this poll:', 'pollify'); ?></span>
        <div class="pollify-share-buttons">
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" target="_blank" class="pollify-share-button pollify-twitter-share">
                <span class="pollify-share-icon">X</span>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" target="_blank" class="pollify-share-button pollify-facebook-share">
                <span class="pollify-share-icon">f</span>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($permalink); ?>&title=<?php echo urlencode($title); ?>" target="_blank" class="pollify-share-button pollify-linkedin-share">
                <span class="pollify-share-icon">in</span>
            </a>
            <button class="pollify-share-button pollify-copy-link" data-poll-url="<?php echo esc_url($permalink); ?>">
                <span class="pollify-share-icon">🔗</span>
            </button>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.pollify-copy-link');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-poll-url');
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="pollify-share-icon">✓</span>';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
