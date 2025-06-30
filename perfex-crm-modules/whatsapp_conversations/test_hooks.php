<?php
// Test file to check if hooks are working
// Access this via: yoursite.com/modules/whatsapp_conversations/test_hooks.php

echo "<h1>WhatsApp Module Hook Test</h1>";

// Try to include Perfex CRM bootstrap
$possible_paths = [
    '../../../index.php',
    '../../index.php', 
    '../../../application/config/config.php',
    '../../../application/config/database.php'
];

$perfex_found = false;
foreach ($possible_paths as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "Found Perfex file: " . $path . "<br>";
        $perfex_found = true;
        
        // Try to include config
        if (strpos($path, 'config.php') !== false) {
            try {
                include_once(__DIR__ . '/' . $path);
                echo "Config loaded successfully<br>";
            } catch (Exception $e) {
                echo "Error loading config: " . $e->getMessage() . "<br>";
            }
        }
    }
}

if (!$perfex_found) {
    echo "<strong>Perfex CRM files not found in expected locations</strong><br>";
}

// Check current directory structure
echo "<h2>Current Directory Structure:</h2>";
echo "Current dir: " . __DIR__ . "<br>";
echo "Parent dir: " . dirname(__DIR__) . "<br>";
echo "Grandparent dir: " . dirname(dirname(__DIR__)) . "<br>";

// List files in current directory
echo "<h3>Files in current directory:</h3>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $type = is_dir(__DIR__ . '/' . $file) ? '[DIR]' : '[FILE]';
        echo $type . ' ' . $file . "<br>";
    }
}

// Test if we can create a simple hook
echo "<h2>Hook Test:</h2>";

// Define a simple test function
function whatsapp_test_hook() {
    echo "TEST HOOK EXECUTED!<br>";
    error_log('WhatsApp Test Hook: Function executed at ' . date('Y-m-d H:i:s'));
}

// Try different ways to register hooks
if (function_exists('hooks')) {
    echo "hooks() function exists - trying to register test hook<br>";
    hooks()->add_action('test_action', 'whatsapp_test_hook');
    
    // Try to fire the hook
    hooks()->do_action('test_action');
} else {
    echo "hooks() function does NOT exist<br>";
}

// Check global variables
echo "<h2>Global Variables:</h2>";
global $hooks, $CI;

if (isset($hooks)) {
    echo "Global \$hooks variable exists<br>";
} else {
    echo "Global \$hooks variable does NOT exist<br>";
}

if (isset($CI)) {
    echo "Global \$CI variable exists<br>";
    if (isset($CI->hooks)) {
        echo "\$CI->hooks exists<br>";
    } else {
        echo "\$CI->hooks does NOT exist<br>";
    }
} else {
    echo "Global \$CI variable does NOT exist<br>";
}

// Check if we're running in the right context
echo "<h2>Environment Check:</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Check if this is actually a Perfex CRM installation
$perfex_indicators = [
    'application/config/config.php',
    'application/controllers/Admin.php',
    'application/models/Staff_model.php'
];

echo "<h3>Perfex CRM Installation Check:</h3>";
foreach ($perfex_indicators as $indicator) {
    $full_path = dirname(dirname(dirname(__DIR__))) . '/' . $indicator;
    if (file_exists($full_path)) {
        echo "✓ Found: " . $indicator . "<br>";
    } else {
        echo "✗ Missing: " . $indicator . " (looked in: " . $full_path . ")<br>";
    }
}