
<?php
/**
 * Utility functions
 * 
 * This is a lightweight file that includes all the utility modules via the main.php file.
 * Each utility is separated into its own file for better organization and maintainability.
 * 
 * Available utility modules:
 * - array-handling.php: Array manipulation functions
 * - formatting.php: Text and data formatting functions
 * - logging.php: Debug and error logging
 * - permissions.php: User permission checks
 * - capabilities.php: Role capability management
 * - poll-data.php: Poll data retrieval functions
 * - transients.php: Transient data handling
 * - url-handling.php: URL manipulation and processing
 * - user-interactions.php: User action tracking
 * - date-formatting.php: Date and time formatting
 * - validation.php: Input validation functions
 * - sanitization.php: Input sanitization functions
 * - ip-handling.php: IP address handling and validation
 * - string-handling.php: String manipulation utilities
 * - file-handling.php: File operations and processing
 * - security.php: Security-related functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the main utils file
require_once plugin_dir_path(__FILE__) . 'utils/main.php';

