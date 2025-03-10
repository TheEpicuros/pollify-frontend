
<?php
/**
 * Demo page for Pollify plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function pollify_demo_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Pollify Demo & Testing', 'pollify'); ?></h1>
        
        <div class="pollify-demo-section">
            <h2><?php _e('Test Your Installation', 'pollify'); ?></h2>
            
            <div class="pollify-demo-card">
                <h3><?php _e('1. Create Your First Poll', 'pollify'); ?></h3>
                <p><?php _e('Create a new poll to test the basic functionality:', 'pollify'); ?></p>
                <a href="<?php echo admin_url('post-new.php?post_type=poll'); ?>" class="button button-primary">
                    <?php _e('Create Test Poll', 'pollify'); ?>
                </a>
            </div>

            <div class="pollify-demo-card">
                <h3><?php _e('2. Test Shortcodes', 'pollify'); ?></h3>
                <p><?php _e('Copy these shortcodes to any page or post:', 'pollify'); ?></p>
                <code>[pollify id="YOUR_POLL_ID"]</code> - <?php _e('Display a specific poll', 'pollify'); ?><br>
                <code>[pollify_create]</code> - <?php _e('Show poll creation form', 'pollify'); ?><br>
                <code>[pollify_browse]</code> - <?php _e('Display all polls', 'pollify'); ?>
            </div>

            <div class="pollify-demo-card">
                <h3><?php _e('3. Example Poll', 'pollify'); ?></h3>
                <?php
                // Create a demo poll if it doesn't exist
                $demo_poll_id = get_option('pollify_demo_poll_id');
                if (!$demo_poll_id) {
                    $demo_poll = array(
                        'post_title'    => 'Demo Poll: What is your favorite color?',
                        'post_content'  => 'This is a demo poll to test the Pollify plugin.',
                        'post_status'   => 'publish',
                        'post_type'     => 'poll'
                    );
                    $demo_poll_id = wp_insert_post($demo_poll);
                    if (!is_wp_error($demo_poll_id)) {
                        update_post_meta($demo_poll_id, '_poll_options', array('Blue', 'Red', 'Green', 'Yellow'));
                        update_option('pollify_demo_poll_id', $demo_poll_id);
                    }
                }
                
                if ($demo_poll_id && !is_wp_error($demo_poll_id)) {
                    echo do_shortcode('[pollify id="' . $demo_poll_id . '"]');
                }
                ?>
            </div>
        </div>

        <style>
            .pollify-demo-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 4px;
            }
            .pollify-demo-card h3 {
                margin-top: 0;
            }
            .pollify-demo-card code {
                display: inline-block;
                margin: 5px 0;
                padding: 3px 8px;
                background: #f0f0f1;
            }
        </style>
    </div>
    <?php
}

