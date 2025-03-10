
<?php
/**
 * File handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create a CSV file from array data
 * 
 * @param array $data Array of data rows
 * @param array $headers CSV header row
 * @param string $filename Filename for the CSV
 * @return string|bool Path to the created file or false on failure
 */
function pollify_create_csv_file($data, $headers, $filename) {
    if (!is_array($data) || empty($data)) {
        return false;
    }
    
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/pollify/' . sanitize_file_name($filename);
    
    // Create the pollify directory if it doesn't exist
    wp_mkdir_p($upload_dir['basedir'] . '/pollify');
    
    $file = fopen($file_path, 'w');
    
    if (!$file) {
        return false;
    }
    
    // Add headers
    if (!empty($headers) && is_array($headers)) {
        fputcsv($file, $headers);
    }
    
    // Add data rows
    foreach ($data as $row) {
        if (is_array($row)) {
            fputcsv($file, $row);
        }
    }
    
    fclose($file);
    
    return $file_path;
}

/**
 * Get file size in a readable format
 * 
 * @param string $file_path Path to the file
 * @param int $precision Number of decimal places
 * @return string Formatted file size
 */
function pollify_get_readable_file_size($file_path, $precision = 2) {
    if (!file_exists($file_path)) {
        return '0 B';
    }
    
    $size = filesize($file_path);
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    $size = max($size, 0);
    $pow = floor(($size ? log($size) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $size /= pow(1024, $pow);
    
    return round($size, $precision) . ' ' . $units[$pow];
}

/**
 * Get file extension
 * 
 * @param string $file_path Path to the file
 * @return string File extension
 */
function pollify_get_file_extension($file_path) {
    return strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
}

