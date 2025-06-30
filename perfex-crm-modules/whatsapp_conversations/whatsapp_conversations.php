<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.3
Author: Custom Module
Requires at least: 2.3.*
*/

define('WHATSAPP_CONVERSATIONS_MODULE_NAME', 'whatsapp_conversations');

// Force immediate execution log
error_log('WhatsApp Module: File loaded at ' . date('Y-m-d H:i:s'));
log_message('error', 'WhatsApp Module: File loaded at ' . date('Y-m-d H:i:s'));

// Try to write to a custom log file as well
$log_file = FCPATH . 'whatsapp_debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - WhatsApp Module file loaded\n", FILE_APPEND);

/**
 * Register activation module hook
 */
register_activation_hook(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'whatsapp_conversations_module_activation_hook');

function whatsapp_conversations_module_activation_hook()
{
    error_log('WhatsApp Module: Activation hook called');
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Activation hook called\n", FILE_APPEND);
    
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files(WHATSAPP_CONVERSATIONS_MODULE_NAME, [WHATSAPP_CONVERSATIONS_MODULE_NAME]);

// Force hook registration with multiple methods
$CI = &get_instance();

// Method 1: Direct hook registration
if (function_exists('hooks')) {
    error_log('WhatsApp Module: Registering hooks via hooks() function');
    hooks()->add_action('admin_init', 'whatsapp_conversations_module_init_menu_items');
    hooks()->add_action('app_admin_head', 'whatsapp_conversations_add_head_components');
    hooks()->add_action('customer_profile_tab', 'whatsapp_conversations_tab');
    hooks()->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');
    
    // Try alternative hook names
    hooks()->add_action('client_profile_tab', 'whatsapp_conversations_tab');
    hooks()->add_action('client_profile_tab_content', 'whatsapp_conversations_tab_content');
}

// Method 2: Direct CI hooks registration
if (isset($CI->hooks)) {
    error_log('WhatsApp Module: Registering hooks via CI hooks');
    $CI->hooks->add_action('customer_profile_tab', 'whatsapp_conversations_tab');
    $CI->hooks->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');
}

// Method 3: Global hooks array (for older versions)
global $hooks_actions;
if (!isset($hooks_actions)) {
    $hooks_actions = [];
}
$hooks_actions['customer_profile_tab'][] = 'whatsapp_conversations_tab';
$hooks_actions['customer_profile_tab_content'][] = 'whatsapp_conversations_tab_content';

error_log('WhatsApp Module: All hook registration methods attempted');

/**
 * Init module menu items in setup in admin_init hook
 */
function whatsapp_conversations_module_init_menu_items()
{
    error_log('WhatsApp Module: Init menu items called');
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Init menu items called\n", FILE_APPEND);
    
    if (function_exists('register_staff_capabilities')) {
        try {
            $capabilities = [];
            $capabilities['capabilities'] = [
                'view' => 'View WhatsApp Conversations',
                'create' => 'Create WhatsApp Conversations', 
                'edit' => 'Edit WhatsApp Conversations',
                'delete' => 'Delete WhatsApp Conversations',
            ];
            
            register_staff_capabilities('whatsapp_conversations', $capabilities, 'WhatsApp Conversations');
            error_log('WhatsApp Module: Capabilities registered');
        } catch (Exception $e) {
            error_log('WhatsApp Module: Error registering capabilities: ' . $e->getMessage());
        }
    }
}

/**
 * Add additional CSS/JS files in head
 */
function whatsapp_conversations_add_head_components()
{
    error_log('WhatsApp Module: Head components called');
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Head components called\n", FILE_APPEND);
    
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if (!(strpos($viewuri, 'admin/clients/client') === false)) {
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/js/whatsapp_conversations.js') . '"></script>';
        echo '<script>console.log("WhatsApp Conversations module assets loaded");</script>';
    }
}

/**
 * Check permissions with extensive fallbacks
 */
function whatsapp_conversations_has_permission($permission_type = 'view')
{
    // Always allow access for initial testing
    return true;
    
    /*
    if (function_exists('has_permission')) {
        try {
            return has_permission('whatsapp_conversations', '', $permission_type);
        } catch (Exception $e) {
            if (function_exists('is_admin')) {
                return is_admin();
            }
            return true;
        }
    }
    
    if (function_exists('is_staff_logged_in')) {
        return is_staff_logged_in();
    }
    
    return true;
    */
}

/**
 * Add WhatsApp Conversations tab in customer profile
 */
function whatsapp_conversations_tab($customer_id)
{
    error_log('WhatsApp Module: Tab function called for customer: ' . $customer_id);
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tab function called for customer: $customer_id\n", FILE_APPEND);
    
    // Force show tab regardless of permissions for testing
    echo '<li role="presentation" class="whatsapp-test-tab">
        <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab" style="background-color: yellow;">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp TEST
        </a>
    </li>';
    
    echo '<script>
        console.log("WhatsApp tab function executed for customer: ' . $customer_id . '");
        // Force add tab if not present
        $(document).ready(function() {
            if ($("#whatsapp_conversations").length === 0) {
                console.log("Adding WhatsApp tab manually");
                var tabHtml = \'<li role="presentation"><a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab"><i class="fa fa-whatsapp"></i> WhatsApp Manual</a></li>\';
                $(".nav-tabs").append(tabHtml);
                
                var contentHtml = \'<div role="tabpanel" class="tab-pane" id="whatsapp_conversations"><div class="alert alert-success">WhatsApp Conversations Tab - Manual Addition Working!</div></div>\';
                $(".tab-content").append(contentHtml);
            }
        });
    </script>';
}

/**
 * Add WhatsApp Conversations tab content
 */
function whatsapp_conversations_tab_content($customer_id)
{
    error_log('WhatsApp Module: Tab content function called for customer: ' . $customer_id);
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tab content function called for customer: $customer_id\n", FILE_APPEND);
    
    $CI = &get_instance();
    
    // Try to load model
    try {
        $CI->load->model('whatsapp_conversations_model');
        $conversations = $CI->whatsapp_conversations_model->get_by_customer($customer_id);
    } catch (Exception $e) {
        error_log('WhatsApp Module: Error loading model: ' . $e->getMessage());
        $conversations = [];
    }
    
    echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">';
    echo '<div class="alert alert-info">';
    echo '<h4>WhatsApp Conversations Module - DEBUG MODE</h4>';
    echo '<p><strong>Customer ID:</strong> ' . $customer_id . '</p>';
    echo '<p><strong>Conversations Found:</strong> ' . count($conversations) . '</p>';
    echo '<p><strong>Time:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    echo '<p><strong>Module Path:</strong> ' . __DIR__ . '</p>';
    echo '</div>';
    
    // Load the actual view if possible
    $data = [
        'customer_id' => $customer_id,
        'conversations' => $conversations,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true
    ];
    
    try {
        $CI->load->view('whatsapp_conversations/customer_tab', $data);
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error loading view: ' . $e->getMessage() . '</div>';
    }
    
    echo '</div>';
    
    echo '<script>console.log("WhatsApp tab content function executed for customer: ' . $customer_id . '");</script>';
}

// Force immediate hook test
error_log('WhatsApp Module: Attempting immediate hook test');
$log_file = FCPATH . 'whatsapp_debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Module file execution completed\n", FILE_APPEND);

// Try to execute hooks immediately if we're on a customer page
if (isset($_GET['group']) || strpos($_SERVER['REQUEST_URI'], 'clients/client') !== false) {
    error_log('WhatsApp Module: On customer page, attempting direct execution');
    
    // Try to get customer ID from URL
    $customer_id = 0;
    if (preg_match('/clients\/client\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $customer_id = $matches[1];
        error_log('WhatsApp Module: Extracted customer ID: ' . $customer_id);
        
        // Force execute our functions
        echo '<script>
            console.log("WhatsApp Module: Direct execution attempt");
            $(document).ready(function() {
                setTimeout(function() {
                    if ($(".whatsapp-test-tab").length === 0) {
                        console.log("Adding WhatsApp tab via direct execution");
                        var tabHtml = \'<li role="presentation" class="whatsapp-direct-tab"><a href="#whatsapp_conversations_direct" aria-controls="whatsapp_conversations_direct" role="tab" data-toggle="tab" style="background-color: red; color: white;"><i class="fa fa-whatsapp"></i> WhatsApp DIRECT</a></li>\';
                        $(".nav-tabs:first").append(tabHtml);
                        
                        var contentHtml = \'<div role="tabpanel" class="tab-pane" id="whatsapp_conversations_direct"><div class="alert alert-warning"><h4>WhatsApp Conversations - Direct Execution</h4><p>This tab was added via direct JavaScript execution because the hook system is not working.</p><p>Customer ID: ' . $customer_id . '</p></div></div>\';
                        $(".tab-content:first").append(contentHtml);
                    }
                }, 1000);
            });
        </script>';
    }
}