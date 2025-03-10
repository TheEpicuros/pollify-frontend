<?php
/**
 * IP handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get user IP address with additional security checks
 * 
 * @return string Sanitized IP address
 */
function pollify_get_client_ip() {
    // Check for CloudFlare IP
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    // Check for standard forwarded IP
    elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    // Check for proxy forwarded IP
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // HTTP_X_FORWARDED_FOR can contain multiple IPs, take the first one
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } 
    // Use remote address as fallback
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Validate IP format
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return sanitize_text_field($ip);
    }
    
    // Fallback to a safe default
    return '0.0.0.0';
}

/**
 * Anonymize IP address for GDPR compliance
 * 
 * @param string $ip IP address to anonymize
 * @return string Anonymized IP address
 */
function pollify_anonymize_ip($ip) {
    if (empty($ip)) {
        return '';
    }
    
    // Check if IPv4 or IPv6
    if (strpos($ip, ':') !== false) {
        // IPv6 - keep first 3 blocks
        return preg_replace('/:[0-9a-f]{0,4}(:[0-9a-f]{0,4}){3}$/i', ':0:0:0:0', $ip);
    } else {
        // IPv4 - replace last octet with 0
        return preg_replace('/\.\d+$/', '.0', $ip);
    }
}
