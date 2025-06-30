<?php
// Direct test file to check if we can access customer data
// Access via: yoursite.com/modules/whatsapp_conversations/test_direct.php?customer_id=1

echo "<h1>WhatsApp Module Direct Test</h1>";

// Get customer ID from URL
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 1;
echo "<p>Testing with Customer ID: $customer_id</p>";

// Try to load Perfex CRM
$paths_to_try = [
    '../../../index.php',
    '../../../application/config/config.php',
    '../../index.php'
];

$loaded = false;
foreach ($paths_to_try as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "<p>Found Perfex file: $path</p>";
        try {
            // Set up basic environment
            if (!defined('BASEPATH')) {
                define('BASEPATH', dirname(__FILE__) . '/../../../application/');
            }
            if (!defined('FCPATH')) {
                define('FCPATH', dirname(__FILE__) . '/../../../');
            }
            
            echo "<p>Attempting to load Perfex environment...</p>";
            // This is a simplified test - in real scenario, full bootstrap is complex
            $loaded = true;
            break;
        } catch (Exception $e) {
            echo "<p>Error loading $path: " . $e->getMessage() . "</p>";
        }
    }
}

if (!$loaded) {
    echo "<p>Could not load Perfex CRM environment</p>";
}

// Test hook functions directly
echo "<h2>Testing Hook Functions</h2>";

// Mock the _l function if it doesn't exist
if (!function_exists('_l')) {
    function _l($key, $default = '') {
        return $default ?: $key;
    }
}

// Mock has_permission if it doesn't exist
if (!function_exists('has_permission')) {
    function has_permission($module, $capability, $action) {
        return true; // For testing purposes
    }
}

// Mock admin_url if it doesn't exist
if (!function_exists('admin_url')) {
    function admin_url($path) {
        return '/admin/' . $path;
    }
}

// Test tab function
echo "<h3>Testing Tab Function</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
ob_start();

// Simulate the tab function
if (true) { // Simulate permission check
    echo '<li role="presentation">
        <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> 
            WhatsApp Conversations
        </a>
    </li>';
}

$tab_output = ob_get_clean();
echo htmlspecialchars($tab_output);
echo "</div>";

// Test content function  
echo "<h3>Testing Content Function</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";

$content_output = '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">
    <div class="alert alert-success">
        <h4><i class="fa fa-whatsapp"></i> WhatsApp Conversations Test</h4>
        <p>Customer ID: ' . $customer_id . '</p>
        <p>This tab would show WhatsApp conversations for this customer.</p>
        <p>Current time: ' . date('Y-m-d H:i:s') . '</p>
    </div>
</div>';

echo htmlspecialchars($content_output);
echo "</div>";

// Test JavaScript injection
echo "<h3>Testing JavaScript</h3>";
echo '<script>
console.log("WhatsApp Test: Direct test page loaded");
console.log("WhatsApp Test: Customer ID = ' . $customer_id . '");

// Try to find tab containers on a typical Perfex page
if (typeof jQuery !== "undefined") {
    $(document).ready(function() {
        console.log("WhatsApp Test: jQuery available");
        console.log("WhatsApp Test: .nav-tabs elements:", $(".nav-tabs").length);
        console.log("WhatsApp Test: .tab-content elements:", $(".tab-content").length);
    });
} else {
    console.log("WhatsApp Test: jQuery not available");
}
</script>';

// Check current URL patterns
echo "<h3>URL Pattern Analysis</h3>";
echo "<p>Current script: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>HTTP Host: " . $_SERVER['HTTP_HOST'] . "</p>";

// Suggest test URLs
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
$admin_url = $base_url . '/admin/clients/client/' . $customer_id;

echo "<h3>Test This URL</h3>";
echo '<p>Go to your customer page: <a href="' . $admin_url . '" target="_blank">' . $admin_url . '</a></p>';
echo '<p>Then check browser console for WhatsApp module messages</p>';

// Show debug log if available
$log_file = '../../../whatsapp_debug.log';
if (file_exists($log_file)) {
    echo "<h3>Current Debug Log</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: scroll;'>";
    echo htmlspecialchars(file_get_contents($log_file));
    echo "</pre>";
} else {
    echo "<h3>Debug Log</h3>";
    echo "<p>Debug log file not found at: $log_file</p>";
}
?>