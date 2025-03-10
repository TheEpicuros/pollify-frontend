
<?php
/**
 * Admin help and documentation page for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the help page
 */
function pollify_help_page() {
    ?>
    <div class="wrap pollify-admin-help">
        <h1><?php _e('Help & Documentation', 'pollify'); ?></h1>
        
        <div class="pollify-help-tabs">
            <div class="pollify-help-nav">
                <a href="#getting-started" class="active"><?php _e('Getting Started', 'pollify'); ?></a>
                <a href="#shortcodes"><?php _e('Shortcodes', 'pollify'); ?></a>
                <a href="#templates"><?php _e('Templates', 'pollify'); ?></a>
                <a href="#developers"><?php _e('For Developers', 'pollify'); ?></a>
                <a href="#faq"><?php _e('FAQ', 'pollify'); ?></a>
            </div>
            
            <div class="pollify-help-content">
                <!-- Getting Started -->
                <div id="getting-started" class="pollify-help-section active">
                    <h2><?php _e('Getting Started with Pollify', 'pollify'); ?></h2>
                    
                    <div class="pollify-help-grid">
                        <div class="pollify-help-card">
                            <div class="pollify-help-card-icon">
                                <span class="dashicons dashicons-plus-alt"></span>
                            </div>
                            <div class="pollify-help-card-content">
                                <h3><?php _e('Creating Your First Poll', 'pollify'); ?></h3>
                                <p><?php _e('To create a new poll, navigate to Pollify > Add New Poll in your WordPress admin menu.', 'pollify'); ?></p>
                                <ol>
                                    <li><?php _e('Enter a title for your poll (this will be the question)', 'pollify'); ?></li>
                                    <li><?php _e('Add at least two options for your poll', 'pollify'); ?></li>
                                    <li><?php _e('Configure the poll settings as needed', 'pollify'); ?></li>
                                    <li><?php _e('Publish your poll', 'pollify'); ?></li>
                                </ol>
                                <a href="<?php echo admin_url('post-new.php?post_type=poll'); ?>" class="button button-primary">
                                    <?php _e('Create Poll Now', 'pollify'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="pollify-help-card">
                            <div class="pollify-help-card-icon">
                                <span class="dashicons dashicons-admin-appearance"></span>
                            </div>
                            <div class="pollify-help-card-content">
                                <h3><?php _e('Displaying Polls', 'pollify'); ?></h3>
                                <p><?php _e('There are several ways to display polls on your website:', 'pollify'); ?></p>
                                <ul>
                                    <li><?php _e('Use the shortcode [pollify id="123"] to display a specific poll', 'pollify'); ?></li>
                                    <li><?php _e('Use the shortcode [pollify_browse] to display a list of polls', 'pollify'); ?></li>
                                    <li><?php _e('Use the shortcode [pollify_create] to display a poll creation form for frontend users', 'pollify'); ?></li>
                                    <li><?php _e('Add polls to widgets areas using the Pollify Widget', 'pollify'); ?></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="pollify-help-card">
                            <div class="pollify-help-card-icon">
                                <span class="dashicons dashicons-chart-bar"></span>
                            </div>
                            <div class="pollify-help-card-content">
                                <h3><?php _e('Viewing Results', 'pollify'); ?></h3>
                                <p><?php _e('You can view poll results in several ways:', 'pollify'); ?></p>
                                <ul>
                                    <li><?php _e('In the WordPress admin under Pollify > All Polls', 'pollify'); ?></li>
                                    <li><?php _e('In the Analytics page for detailed statistics', 'pollify'); ?></li>
                                    <li><?php _e('On the frontend where polls are displayed (based on your settings)', 'pollify'); ?></li>
                                </ul>
                                <a href="<?php echo admin_url('admin.php?page=pollify-analytics'); ?>" class="button">
                                    <?php _e('View Analytics', 'pollify'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="pollify-help-card">
                            <div class="pollify-help-card-icon">
                                <span class="dashicons dashicons-admin-generic"></span>
                            </div>
                            <div class="pollify-help-card-content">
                                <h3><?php _e('Plugin Settings', 'pollify'); ?></h3>
                                <p><?php _e('Configure global settings for all polls under Pollify > Settings:', 'pollify'); ?></p>
                                <ul>
                                    <li><?php _e('General settings for poll appearance and behavior', 'pollify'); ?></li>
                                    <li><?php _e('Voting settings to control who can vote', 'pollify'); ?></li>
                                    <li><?php _e('Display settings for customizing how polls look', 'pollify'); ?></li>
                                    <li><?php _e('Advanced settings for developers', 'pollify'); ?></li>
                                </ul>
                                <a href="<?php echo admin_url('admin.php?page=pollify-settings'); ?>" class="button">
                                    <?php _e('Configure Settings', 'pollify'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Shortcodes -->
                <div id="shortcodes" class="pollify-help-section">
                    <h2><?php _e('Available Shortcodes', 'pollify'); ?></h2>
                    
                    <div class="pollify-shortcode-list">
                        <div class="pollify-shortcode-item">
                            <h3>[pollify id="123"]</h3>
                            <p><?php _e('Displays a specific poll by ID.', 'pollify'); ?></p>
                            <h4><?php _e('Parameters:', 'pollify'); ?></h4>
                            <ul>
                                <li><code>id</code> - <?php _e('The ID of the poll to display (required)', 'pollify'); ?></li>
                                <li><code>show_results</code> - <?php _e('Set to "true" to always show results (optional)', 'pollify'); ?></li>
                                <li><code>style</code> - <?php _e('Set the display style: "default", "compact", or "minimal" (optional)', 'pollify'); ?></li>
                            </ul>
                            <div class="pollify-shortcode-example">
                                <h4><?php _e('Example:', 'pollify'); ?></h4>
                                <code>[pollify id="123" show_results="true" style="compact"]</code>
                            </div>
                        </div>
                        
                        <div class="pollify-shortcode-item">
                            <h3>[pollify_browse]</h3>
                            <p><?php _e('Displays a list of polls for users to browse.', 'pollify'); ?></p>
                            <h4><?php _e('Parameters:', 'pollify'); ?></h4>
                            <ul>
                                <li><code>number</code> - <?php _e('Number of polls to display (default: 10)', 'pollify'); ?></li>
                                <li><code>orderby</code> - <?php _e('Order by "date", "votes", or "title" (default: "date")', 'pollify'); ?></li>
                                <li><code>order</code> - <?php _e('Sort order "asc" or "desc" (default: "desc")', 'pollify'); ?></li>
                                <li><code>category</code> - <?php _e('Filter by poll category slug (optional)', 'pollify'); ?></li>
                            </ul>
                            <div class="pollify-shortcode-example">
                                <h4><?php _e('Example:', 'pollify'); ?></h4>
                                <code>[pollify_browse number="5" orderby="votes" order="desc" category="politics"]</code>
                            </div>
                        </div>
                        
                        <div class="pollify-shortcode-item">
                            <h3>[pollify_create]</h3>
                            <p><?php _e('Displays a form for users to create their own polls.', 'pollify'); ?></p>
                            <h4><?php _e('Parameters:', 'pollify'); ?></h4>
                            <ul>
                                <li><code>require_login</code> - <?php _e('Set to "true" to require users to be logged in (default: "true")', 'pollify'); ?></li>
                                <li><code>redirect</code> - <?php _e('URL to redirect to after poll creation (optional)', 'pollify'); ?></li>
                                <li><code>min_options</code> - <?php _e('Minimum number of options (default: 2)', 'pollify'); ?></li>
                                <li><code>max_options</code> - <?php _e('Maximum number of options (default: 10)', 'pollify'); ?></li>
                            </ul>
                            <div class="pollify-shortcode-example">
                                <h4><?php _e('Example:', 'pollify'); ?></h4>
                                <code>[pollify_create require_login="true" min_options="2" max_options="5"]</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Templates -->
                <div id="templates" class="pollify-help-section">
                    <h2><?php _e('Customizing Templates', 'pollify'); ?></h2>
                    
                    <p><?php _e('Pollify uses a template system that allows you to override the default templates with your own customized versions.', 'pollify'); ?></p>
                    
                    <h3><?php _e('Template Locations', 'pollify'); ?></h3>
                    <p><?php _e('Default templates are located in:', 'pollify'); ?></p>
                    <code>/wp-content/plugins/pollify/templates/</code>
                    
                    <p><?php _e('To override templates, create a "pollify" directory in your theme:', 'pollify'); ?></p>
                    <code>/wp-content/themes/your-theme/pollify/</code>
                    
                    <h3><?php _e('Available Templates', 'pollify'); ?></h3>
                    <ul>
                        <li><code>poll-single.php</code> - <?php _e('Template for displaying a single poll', 'pollify'); ?></li>
                        <li><code>poll-list.php</code> - <?php _e('Template for displaying a list of polls', 'pollify'); ?></li>
                        <li><code>poll-create-form.php</code> - <?php _e('Template for the poll creation form', 'pollify'); ?></li>
                        <li><code>poll-results.php</code> - <?php _e('Template for displaying poll results', 'pollify'); ?></li>
                    </ul>
                    
                    <h3><?php _e('Template Functions', 'pollify'); ?></h3>
                    <p><?php _e('You can use these functions in your custom templates:', 'pollify'); ?></p>
                    
                    <ul>
                        <li><code>pollify_get_poll_question($poll_id)</code> - <?php _e('Get the poll question', 'pollify'); ?></li>
                        <li><code>pollify_get_poll_options($poll_id)</code> - <?php _e('Get the poll options as an array', 'pollify'); ?></li>
                        <li><code>pollify_get_vote_counts($poll_id)</code> - <?php _e('Get vote counts for each option', 'pollify'); ?></li>
                        <li><code>pollify_has_user_voted($poll_id)</code> - <?php _e('Check if current user has voted', 'pollify'); ?></li>
                        <li><code>pollify_get_total_votes($poll_id)</code> - <?php _e('Get total number of votes', 'pollify'); ?></li>
                    </ul>
                </div>
                
                <!-- For Developers -->
                <div id="developers" class="pollify-help-section">
                    <h2><?php _e('For Developers', 'pollify'); ?></h2>
                    
                    <h3><?php _e('Hooks & Filters', 'pollify'); ?></h3>
                    <p><?php _e('Pollify provides various hooks and filters for developers to extend functionality:', 'pollify'); ?></p>
                    
                    <div class="pollify-hook-list">
                        <div class="pollify-hook-item">
                            <h4><?php _e('Actions', 'pollify'); ?></h4>
                            <ul>
                                <li><code>pollify_before_poll</code> - <?php _e('Fires before a poll is displayed', 'pollify'); ?></li>
                                <li><code>pollify_after_poll</code> - <?php _e('Fires after a poll is displayed', 'pollify'); ?></li>
                                <li><code>pollify_before_vote</code> - <?php _e('Fires before a vote is recorded', 'pollify'); ?></li>
                                <li><code>pollify_after_vote</code> - <?php _e('Fires after a vote is recorded', 'pollify'); ?></li>
                                <li><code>pollify_poll_created</code> - <?php _e('Fires when a new poll is created', 'pollify'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="pollify-hook-item">
                            <h4><?php _e('Filters', 'pollify'); ?></h4>
                            <ul>
                                <li><code>pollify_poll_options</code> - <?php _e('Filter poll options before display', 'pollify'); ?></li>
                                <li><code>pollify_poll_results</code> - <?php _e('Filter poll results before display', 'pollify'); ?></li>
                                <li><code>pollify_can_vote</code> - <?php _e('Filter whether a user can vote on a poll', 'pollify'); ?></li>
                                <li><code>pollify_vote_button_text</code> - <?php _e('Filter the vote button text', 'pollify'); ?></li>
                                <li><code>pollify_results_display_style</code> - <?php _e('Filter the results display style', 'pollify'); ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h3><?php _e('REST API', 'pollify'); ?></h3>
                    <p><?php _e('Pollify provides a REST API for interacting with polls programmatically:', 'pollify'); ?></p>
                    
                    <ul>
                        <li><code>/wp-json/pollify/v1/polls</code> - <?php _e('Get all polls', 'pollify'); ?></li>
                        <li><code>/wp-json/pollify/v1/polls/&lt;id&gt;</code> - <?php _e('Get a specific poll', 'pollify'); ?></li>
                        <li><code>/wp-json/pollify/v1/polls/&lt;id&gt;/vote</code> - <?php _e('Submit a vote (POST)', 'pollify'); ?></li>
                        <li><code>/wp-json/pollify/v1/polls/&lt;id&gt;/results</code> - <?php _e('Get poll results', 'pollify'); ?></li>
                    </ul>
                    
                    <h3><?php _e('Custom CSS', 'pollify'); ?></h3>
                    <p><?php _e('You can add custom CSS to your theme to style polls:', 'pollify'); ?></p>
                    
                    <pre><code>.pollify-poll { /* Poll container */ }
.pollify-question { /* Poll question */ }
.pollify-options { /* Options container */ }
.pollify-option { /* Individual option */ }
.pollify-results { /* Results container */ }
.pollify-result-bar { /* Result bar */ }
.pollify-vote-button { /* Vote button */ }</code></pre>
                </div>
                
                <!-- FAQ -->
                <div id="faq" class="pollify-help-section">
                    <h2><?php _e('Frequently Asked Questions', 'pollify'); ?></h2>
                    
                    <div class="pollify-faq-list">
                        <div class="pollify-faq-item">
                            <h3><?php _e('How do I prevent users from voting multiple times?', 'pollify'); ?></h3>
                            <p><?php _e('Pollify tracks votes based on IP address and cookies. For more security, you can require users to be logged in by enabling this option in the Settings.', 'pollify'); ?></p>
                        </div>
                        
                        <div class="pollify-faq-item">
                            <h3><?php _e('Can I create image-based polls?', 'pollify'); ?></h3>
                            <p><?php _e('Yes, when creating a poll, select "Image-based poll" as the poll type and you\'ll be able to upload images for each option.', 'pollify'); ?></p>
                        </div>
                        
                        <div class="pollify-faq-item">
                            <h3><?php _e('How do I enable social sharing for polls?', 'pollify'); ?></h3>
                            <p><?php _e('Go to Pollify > Settings and enable the "Social Sharing" option in the General settings tab.', 'pollify'); ?></p>
                        </div>
                        
                        <div class="pollify-faq-item">
                            <h3><?php _e('Can I export poll data?', 'pollify'); ?></h3>
                            <p><?php _e('Yes, you can export poll data as CSV from the Analytics page by clicking the "Export CSV" button.', 'pollify'); ?></p>
                        </div>
                        
                        <div class="pollify-faq-item">
                            <h3><?php _e('Can I limit polls to specific user roles?', 'pollify'); ?></h3>
                            <p><?php _e('Yes, when creating or editing a poll, you can select which user roles are allowed to vote under the Poll Settings meta box.', 'pollify'); ?></p>
                        </div>
                        
                        <div class="pollify-faq-item">
                            <h3><?php _e('Does Pollify support GDPR compliance?', 'pollify'); ?></h3>
                            <p><?php _e('Yes, Pollify is designed with privacy in mind. IP addresses are anonymized by default, and you can choose to add privacy notices to your polls.', 'pollify'); ?></p>
                        </div>
                    </div>
                    
                    <div class="pollify-help-contact">
                        <h3><?php _e('Need More Help?', 'pollify'); ?></h3>
                        <p><?php _e('If you have questions not covered here, please visit our support center.', 'pollify'); ?></p>
                        <a href="https://example.com/pollify-support" class="button button-primary" target="_blank">
                            <?php _e('Contact Support', 'pollify'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.pollify-help-nav a').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and sections
            $('.pollify-help-nav a').removeClass('active');
            $('.pollify-help-section').removeClass('active');
            
            // Add active class to clicked tab and corresponding section
            $(this).addClass('active');
            $($(this).attr('href')).addClass('active');
            
            // Update URL hash
            window.location.hash = $(this).attr('href');
        });
        
        // Check for hash in URL
        if (window.location.hash) {
            const hash = window.location.hash;
            if ($(hash).length) {
                $('.pollify-help-nav a').removeClass('active');
                $('.pollify-help-section').removeClass('active');
                
                $('.pollify-help-nav a[href="' + hash + '"]').addClass('active');
                $(hash).addClass('active');
            }
        }
    });
    </script>
    
    <style>
    .pollify-admin-help {
        max-width: 1200px;
    }
    
    .pollify-help-tabs {
        margin-top: 20px;
    }
    
    .pollify-help-nav {
        display: flex;
        border-bottom: 1px solid #ccd0d4;
        margin-bottom: 20px;
    }
    
    .pollify-help-nav a {
        padding: 10px 15px;
        text-decoration: none;
        border: 1px solid transparent;
        border-bottom: none;
        margin-bottom: -1px;
        font-weight: 500;
    }
    
    .pollify-help-nav a.active {
        border-color: #ccd0d4;
        border-bottom-color: #f0f0f1;
        background: #f0f0f1;
    }
    
    .pollify-help-section {
        display: none;
        background: #fff;
        padding: 20px;
        border: 1px solid #ccd0d4;
    }
    
    .pollify-help-section.active {
        display: block;
    }
    
    .pollify-help-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .pollify-help-card {
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        overflow: hidden;
        background: #f9f9f9;
    }
    
    .pollify-help-card-icon {
        background: #8B5CF6;
        color: #fff;
        padding: 15px;
        text-align: center;
    }
    
    .pollify-help-card-icon .dashicons {
        font-size: 30px;
        width: 30px;
        height: 30px;
    }
    
    .pollify-help-card-content {
        padding: 20px;
    }
    
    .pollify-help-card h3 {
        margin-top: 0;
    }
    
    .pollify-shortcode-item,
    .pollify-faq-item {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }
    
    .pollify-shortcode-item:last-child,
    .pollify-faq-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .pollify-shortcode-example {
        background: #f0f0f1;
        padding: 15px;
        border-radius: 4px;
        margin-top: 15px;
    }
    
    .pollify-hook-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin-top: 20px;
    }
    
    .pollify-help-contact {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        text-align: center;
    }
    
    code, pre {
        background: #f0f0f1;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 13px;
    }
    
    pre {
        padding: 15px;
        overflow: auto;
        margin: 15px 0;
    }
    
    ul li {
        margin-bottom: 8px;
    }
    
    @media screen and (max-width: 782px) {
        .pollify-help-grid,
        .pollify-hook-list {
            grid-template-columns: 1fr;
        }
        
        .pollify-help-nav {
            flex-direction: column;
            border-bottom: none;
        }
        
        .pollify-help-nav a {
            border: 1px solid #ccd0d4;
            margin-bottom: 5px;
        }
        
        .pollify-help-nav a.active {
            border-bottom-color: #ccd0d4;
        }
    }
    </style>
    <?php
}
