<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.4
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

// Register hooks using the standard Perfex CRM method
hooks()->add_action('admin_init', 'whatsapp_conversations_module_init_menu_items');
hooks()->add_action('app_admin_head', 'whatsapp_conversations_add_head_components');
hooks()->add_action('customer_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');

// Also try alternative hook names for different Perfex versions
hooks()->add_action('client_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('client_profile_tab_content', 'whatsapp_conversations_tab_content');

error_log('WhatsApp Module: Hooks registered');

/**
 * Init module menu items in setup in admin_init hook
 */
function whatsapp_conversations_module_init_menu_items()
{
    error_log('WhatsApp Module: Init menu items called');
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Init menu items called\n", FILE_APPEND);
    
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
    error_log('WhatsApp Module: Head components called');
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Head components called\n", FILE_APPEND);
    
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if (!(strpos($viewuri, 'admin/clients/client') === false)) {
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        
        // Add script with proper jQuery handling
        echo '<script>
            console.log("WhatsApp Module: Adding assets to page");
            // Wait for jQuery to be available before loading our script
            function loadWhatsAppScript() {
                if (typeof jQuery !== "undefined") {
                    console.log("WhatsApp Module: jQuery available, loading script");
                    var script = document.createElement("script");
                    script.src = "' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/js/whatsapp_conversations.js') . '";
                    script.onload = function() {
                        console.log("WhatsApp Module: Script loaded successfully");
                    };
                    script.onerror = function() {
                        console.error("WhatsApp Module: Failed to load script");
                    };
                    document.head.appendChild(script);
                } else {
                    console.log("WhatsApp Module: jQuery not ready, retrying in 100ms");
                    setTimeout(loadWhatsAppScript, 100);
                }
            }
            
            // Start trying to load the script
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", loadWhatsAppScript);
            } else {
                loadWhatsAppScript();
            }
        </script>';
    }
}

/**
 * Add WhatsApp Conversations tab in customer profile
 */
function whatsapp_conversations_tab($customer_id)
{
    error_log('WhatsApp Module: Tab function called for customer: ' . $customer_id);
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tab function called for customer: $customer_id\n", FILE_APPEND);
    
    if (has_permission('whatsapp_conversations', '', 'view')) {
        echo '<li role="presentation">
            <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
                <i class="fa fa-whatsapp" aria-hidden="true"></i> 
                ' . _l('whatsapp_conversations') . '
            </a>
        </li>';
        
        echo '<script>
            console.log("WhatsApp Module: Tab added for customer: ' . $customer_id . '");
        </script>';
    }
}

/**
 * Add WhatsApp Conversations tab content
 */
function whatsapp_conversations_tab_content($customer_id)
{
    error_log('WhatsApp Module: Tab content function called for customer: ' . $customer_id);
    $log_file = FCPATH . 'whatsapp_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tab content function called for customer: $customer_id\n", FILE_APPEND);
    
    if (has_permission('whatsapp_conversations', '', 'view')) {
        $CI = &get_instance();
        $CI->load->model('whatsapp_conversations_model');
        
        $data['customer_id'] = $customer_id;
        $data['conversations'] = $CI->whatsapp_conversations_model->get_by_customer($customer_id);
        $data['can_create'] = has_permission('whatsapp_conversations', '', 'create');
        $data['can_edit'] = has_permission('whatsapp_conversations', '', 'edit');
        $data['can_delete'] = has_permission('whatsapp_conversations', '', 'delete');

        echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">';
        $CI->load->view('whatsapp_conversations/customer_tab', $data);
        echo '</div>';
        
        echo '<script>
            console.log("WhatsApp Module: Tab content loaded for customer: ' . $customer_id . '");
        </script>';
    }
}

// Manual hook test for debugging
error_log('WhatsApp Module: Manual hook test executed');
$log_file = FCPATH . 'whatsapp_debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Manual hook test executed\n", FILE_APPEND);