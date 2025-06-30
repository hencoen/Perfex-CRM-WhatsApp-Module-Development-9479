<?php
// Immediate debug file - call this directly via URL to test module loading

error_log('WhatsApp Debug: Direct file access at ' . date('Y-m-d H:i:s'));

// Try to bootstrap Perfex CRM
$perfex_paths = [
    '../../index.php',
    '../../../index.php', 
    '../../../../index.php',
    '../../../application/config/config.php'
];

foreach ($perfex_paths as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        error_log('WhatsApp Debug: Found Perfex path: ' . $path);
        break;
    }
}

// Test hook system
if (function_exists('hooks')) {
    error_log('WhatsApp Debug: hooks() function exists');
    echo "hooks() function exists<br>";
} else {
    error_log('WhatsApp Debug: hooks() function does NOT exist');
    echo "hooks() function does NOT exist<br>";
}

// Test module registration functions
if (function_exists('register_activation_hook')) {
    error_log('WhatsApp Debug: register_activation_hook exists');
    echo "register_activation_hook exists<br>";
} else {
    error_log('WhatsApp Debug: register_activation_hook does NOT exist');
    echo "register_activation_hook does NOT exist<br>";
}

// Check if this module file exists in the right place
$module_file = __DIR__ . '/whatsapp_conversations.php';
if (file_exists($module_file)) {
    echo "Module file exists: " . $module_file . "<br>";
    error_log('WhatsApp Debug: Module file exists at: ' . $module_file);
} else {
    echo "Module file NOT found: " . $module_file . "<br>";
    error_log('WhatsApp Debug: Module file NOT found at: ' . $module_file);
}

// Check modules directory structure
$modules_dir = dirname(__DIR__);
echo "<h3>Modules Directory Structure:</h3>";
echo "Modules dir: " . $modules_dir . "<br>";

if (is_dir($modules_dir)) {
    $dirs = scandir($modules_dir);
    foreach ($dirs as $dir) {
        if ($dir != '.' && $dir != '..' && is_dir($modules_dir . '/' . $dir)) {
            echo "Found module directory: " . $dir . "<br>";
        }
    }
}

// Check if we're in the right place
echo "<h3>Current Location:</h3>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Script path: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Try to access this file via URL
$test_url = $_SERVER['HTTP_HOST'] . str_replace('debug_immediate.php', 'whatsapp_conversations.php', $_SERVER['REQUEST_URI']);
echo "<h3>Try accessing the main module file:</h3>";
echo '<a href="http://' . $test_url . '" target="_blank">Test Module File Access</a><br>';

echo "<h3>Debug Log File:</h3>";
$log_file = $_SERVER['DOCUMENT_ROOT'] . '/whatsapp_debug.log';
if (file_exists($log_file)) {
    echo "Log file exists: " . $log_file . "<br>";
    echo "<pre>" . file_get_contents($log_file) . "</pre>";
} else {
    echo "Log file not found: " . $log_file . "<br>";
}