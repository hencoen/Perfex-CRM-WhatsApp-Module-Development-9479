<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.7
Author: Custom Module
Requires at least: 2.3.*
*/

define('WHATSAPP_CONVERSATIONS_MODULE_NAME', 'whatsapp_conversations');

// Enhanced logging function
function whatsapp_debug_log($message) {
    $log_file = FCPATH . 'whatsapp_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "$timestamp - $message\n", FILE_APPEND);
    error_log("WhatsApp Module: $message");
    log_message('info', "WhatsApp Module: $message");
}

whatsapp_debug_log('Module file loaded');

/**
 * Register activation module hook
 */
register_activation_hook(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'whatsapp_conversations_module_activation_hook');

function whatsapp_conversations_module_activation_hook()
{
    whatsapp_debug_log('Activation hook called');
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files(WHATSAPP_CONVERSATIONS_MODULE_NAME, [WHATSAPP_CONVERSATIONS_MODULE_NAME]);

// Register hooks
hooks()->add_action('admin_init', 'whatsapp_conversations_module_init_menu_items');
hooks()->add_action('app_admin_head', 'whatsapp_conversations_add_head_components');

// Try multiple hook variations to ensure compatibility
$hook_variations = [
    'customer_profile_tab',
    'client_profile_tab', 
    'customer_tabs',
    'client_tabs'
];

foreach ($hook_variations as $hook) {
    hooks()->add_action($hook, 'whatsapp_conversations_tab_safe');
    hooks()->add_action($hook . '_content', 'whatsapp_conversations_tab_content_safe');
}

whatsapp_debug_log('Hooks registered with variations');

/**
 * Get customer ID from current URL
 */
function whatsapp_get_current_customer_id() {
    $CI = &get_instance();
    
    // Try multiple methods to get customer ID
    $methods = [
        // Method 1: URI segments
        function() use ($CI) {
            $segments = $CI->uri->segment_array();
            $client_index = array_search('client', $segments);
            if ($client_index !== false && isset($segments[$client_index + 1])) {
                return (int)$segments[$client_index + 1];
            }
            return 0;
        },
        
        // Method 2: GET parameter
        function() {
            return isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 
                   (isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0);
        },
        
        // Method 3: POST parameter
        function() {
            return isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 
                   (isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0);
        }
    ];
    
    foreach ($methods as $method) {
        $id = $method();
        if ($id > 0) {
            whatsapp_debug_log("Found customer ID: $id");
            return $id;
        }
    }
    
    whatsapp_debug_log("Could not determine customer ID");
    return 0;
}

/**
 * Safe tab function that handles any parameter format
 */
function whatsapp_conversations_tab_safe($param = null) {
    try {
        whatsapp_debug_log('Safe tab function called with param: ' . gettype($param));
        
        // Don't output anything here - let JavaScript handle it
        // This prevents the 404 error from malformed parameters
        
        // Just ensure we have permission
        if (has_permission('whatsapp_conversations', '', 'view') || is_admin()) {
            whatsapp_debug_log('Permission check passed for tab');
            // Tab will be added via JavaScript
        }
    } catch (Exception $e) {
        whatsapp_debug_log('Error in tab function: ' . $e->getMessage());
    }
}

/**
 * Safe content function that handles any parameter format
 */
function whatsapp_conversations_tab_content_safe($param = null) {
    try {
        whatsapp_debug_log('Safe content function called with param: ' . gettype($param));
        
        // Don't output anything here - let JavaScript handle it
        // This prevents the 404 error from malformed parameters
        
        if (has_permission('whatsapp_conversations', '', 'view') || is_admin()) {
            whatsapp_debug_log('Permission check passed for content');
            // Content will be loaded via JavaScript
        }
    } catch (Exception $e) {
        whatsapp_debug_log('Error in content function: ' . $e->getMessage());
    }
}

/**
 * Init module menu items in setup in admin_init hook
 */
function whatsapp_conversations_module_init_menu_items()
{
    whatsapp_debug_log('Init menu items called');
    
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('whatsapp_conversations', $capabilities, _l('whatsapp_conversations'));
}

/**
 * Add additional CSS/JS files in head
 */
function whatsapp_conversations_add_head_components()
{
    whatsapp_debug_log('Head components called');
    
    $viewuri = $_SERVER['REQUEST_URI'];
    whatsapp_debug_log("Current URI: $viewuri");
    
    // Check for customer/client pages
    $is_customer_page = (
        strpos($viewuri, 'admin/clients/client') !== false ||
        strpos($viewuri, 'admin/customers/client') !== false ||
        strpos($viewuri, 'clients/client') !== false ||
        strpos($viewuri, 'customers/client') !== false
    );
    
    whatsapp_debug_log("Is customer page: " . ($is_customer_page ? 'YES' : 'NO'));
    
    if ($is_customer_page) {
        whatsapp_debug_log('Adding CSS and JS assets');
        
        $customer_id = whatsapp_get_current_customer_id();
        
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        
        // Enhanced JavaScript that loads actual content
        echo '<script>
            var whatsAppCustomerId = ' . $customer_id . ';
            console.log("WhatsApp Module: Customer ID detected:", whatsAppCustomerId);
            
            function loadWhatsAppContent() {
                if (whatsAppCustomerId > 0) {
                    // Load actual content via AJAX
                    var contentUrl = "' . admin_url('whatsapp_conversations/get_tab_content/') . '" + whatsAppCustomerId;
                    
                    $.get(contentUrl)
                        .done(function(response) {
                            console.log("WhatsApp Module: Content loaded successfully");
                            $("#whatsapp_conversations").html(response);
                        })
                        .fail(function() {
                            console.log("WhatsApp Module: Using fallback content");
                            $("#whatsapp_conversations").html(`
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h4><i class="fa fa-whatsapp"></i> WhatsApp Conversations</h4>
                                            <p>WhatsApp conversation management for this customer.</p>
                                            <p><small>Customer ID: ${whatsAppCustomerId}</small></p>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                } else {
                    $("#whatsapp_conversations").html(`
                        <div class="alert alert-warning">
                            <h4>WhatsApp Conversations</h4>
                            <p>Could not determine customer ID.</p>
                        </div>
                    `);
                }
            }
            
            function addWhatsAppTab() {
                console.log("WhatsApp Module: Adding tab and content");
                
                // Find tab container
                var $tabContainer = $(".nav-tabs").first();
                if ($tabContainer.length === 0) {
                    console.log("WhatsApp Module: No tab container found");
                    return;
                }
                
                // Find content container  
                var $contentContainer = $(".tab-content").first();
                if ($contentContainer.length === 0) {
                    console.log("WhatsApp Module: No content container found");
                    return;
                }
                
                // Check if tab already exists
                if ($tabContainer.find(\'a[href="#whatsapp_conversations"]\').length > 0) {
                    console.log("WhatsApp Module: Tab already exists");
                    return;
                }
                
                // Add tab
                $tabContainer.append(`
                    <li role="presentation">
                        <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
                            <i class="fa fa-whatsapp" aria-hidden="true"></i> 
                            WhatsApp Conversations
                        </a>
                    </li>
                `);
                
                // Add content
                $contentContainer.append(`
                    <div role="tabpanel" class="tab-pane" id="whatsapp_conversations">
                        <div class="text-center" style="padding: 20px;">
                            <i class="fa fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                `);
                
                console.log("WhatsApp Module: Tab and content added successfully");
                
                // Load content after a short delay
                setTimeout(loadWhatsAppContent, 500);
            }
            
            // Initialize when DOM is ready
            $(document).ready(function() {
                setTimeout(addWhatsAppTab, 1000);
            });
        </script>';
        
        // Load the main JavaScript file
        echo '<script src="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/js/whatsapp_conversations.js') . '"></script>';
    }
}

whatsapp_debug_log('Module initialization complete');