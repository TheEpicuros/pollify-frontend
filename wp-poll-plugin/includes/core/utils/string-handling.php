
<?php
/**
 * String handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Truncate a string to a specified length with ellipsis
 * 
 * @param string $string The string to truncate
 * @param int $length Maximum length of the string
 * @param string $append String to append if truncated (default: '...')
 * @return string Truncated string
 */
function pollify_truncate_string($string, $length = 50, $append = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    
    $string = substr($string, 0, $length - strlen($append));
    $string = substr($string, 0, strrpos($string, ' '));
    
    return $string . $append;
}

/**
 * Generate a random string
 * 
 * @param int $length Length of the random string
 * @param bool $special_chars Whether to include special characters
 * @return string Random string
 */
function pollify_generate_random_string($length = 10, $special_chars = false) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    
    if ($special_chars) {
        $chars .= '!@#$%^&*()-_=+{}[]|:;<>,.?/';
    }
    
    $random_string = '';
    $chars_length = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $chars[random_int(0, $chars_length - 1)];
    }
    
    return $random_string;
}

/**
 * Clean a string for use in a slug
 * 
 * @param string $string The string to clean
 * @return string Cleaned string
 */
function pollify_clean_string_for_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');
    
    return $string;
}

