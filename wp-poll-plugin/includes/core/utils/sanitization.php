
<?php
/**
 * Input sanitization utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize poll option text
 * 
 * @param string $text The text to sanitize
 * @return string Sanitized text
 */
function pollify_sanitize_option_text($text) {
    return wp_kses($text, array(
        'em' => array(),
        'strong' => array(),
        'span' => array('class' => array()),
        'br' => array()
    ));
}

/**
 * Sanitize poll settings
 * 
 * @param array $settings The settings array to sanitize
 * @return array Sanitized settings
 */
function pollify_sanitize_poll_settings($settings) {
    if (!is_array($settings)) {
        return array();
    }
    
    $sanitized = array();
    
    foreach ($settings as $key => $value) {
        $key = sanitize_key($key);
        
        if (is_array($value)) {
            $sanitized[$key] = pollify_sanitize_array($value);
        } else if (is_numeric($value)) {
            $sanitized[$key] = absint($value);
        } else if (is_string($value)) {
            $sanitized[$key] = sanitize_text_field($value);
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}
