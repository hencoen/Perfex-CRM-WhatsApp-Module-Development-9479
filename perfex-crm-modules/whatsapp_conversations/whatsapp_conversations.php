<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.6
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

// Register customer tab hooks with proper parameter handling
hooks()->add_action('customer_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');

// Also try alternative hook names for different Perfex versions
hooks()->add_action('client_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('client_profile_tab_content', 'whatsapp_conversations_tab_content');

whatsapp_debug_log('Hooks registered');

/**
 * Extract customer ID from various parameter formats
 */
function whatsapp_extract_customer_id($param) {
    whatsapp_debug_log('Extracting customer ID from: ' . print_r($param, true));
    
    // If it's already a simple number/string
    if (is_numeric($param)) {
        whatsapp_debug_log('Parameter is numeric: ' . $param);
        return (int)$param;
    }
    
    // If it's an array, try different possible keys
    if (is_array($param)) {
        whatsapp_debug_log('Parameter is array with keys: ' . implode(', ', array_keys($param)));
        
        $possible_keys = ['customer_id', 'client_id', 'id', 'userid', 'user_id'];
        foreach ($possible_keys as $key) {
            if (isset($param[$key]) && is_numeric($param[$key])) {
                whatsapp_debug_log('Found customer ID in array key "' . $key . '": ' . $param[$key]);
                return (int)$param[$key];
            }
        }
        
        // If array has numeric keys, try the first one
        if (isset($param[0]) && is_numeric($param[0])) {
            whatsapp_debug_log('Using first array element: ' . $param[0]);
            return (int)$param[0];
        }
    }
    
    // If it's an object, try to extract ID
    if (is_object($param)) {
        whatsapp_debug_log('Parameter is object');
        $possible_props = ['customer_id', 'client_id', 'id', 'userid', 'user_id'];
        foreach ($possible_props as $prop) {
            if (isset($param->$prop) && is_numeric($param->$prop)) {
                whatsapp_debug_log('Found customer ID in object property "' . $prop . '": ' . $param->$prop);
                return (int)$param->$prop;
            }
        }
    }
    
    // Last resort: try to get from URL
    $CI = &get_instance();
    $uri_segments = $CI->uri->segment_array();
    whatsapp_debug_log('URI segments: ' . implode(', ', $uri_segments));
    
    // Look for client/customer in URL segments
    $client_segment_index = array_search('client', $uri_segments);
    if ($client_segment_index !== false && isset($uri_segments[$client_segment_index + 1])) {
        $customer_id = (int)$uri_segments[$client_segment_index + 1];
        whatsapp_debug_log('Extracted customer ID from URI: ' . $customer_id);
        return $customer_id;
    }
    
    whatsapp_debug_log('Could not extract customer ID, defaulting to 0');
    return 0;
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
        
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        
        // Force add the tab via JavaScript as fallback
        echo '<script>
            console.log("WhatsApp Module: Force adding tab via JavaScript");
            
            function addWhatsAppTabFallback() {
                console.log("WhatsApp Module: Attempting fallback tab addition");
                
                // Look for various possible tab containers
                var tabSelectors = [
                    ".nav-tabs",
                    ".nav.nav-tabs", 
                    "#customer_profile_tabs",
                    "#client_profile_tabs",
                    ".profile-tabs .nav-tabs",
                    ".customer-tabs .nav-tabs"
                ];
                
                var contentSelectors = [
                    ".tab-content",
                    "#customer_profile_tab_content", 
                    "#client_profile_tab_content",
                    ".profile-content .tab-content",
                    ".customer-content .tab-content"
                ];
                
                var tabAdded = false;
                
                // Try to find and add to tab navigation
                for (var i = 0; i < tabSelectors.length; i++) {
                    var $tabs = $(tabSelectors[i]);
                    if ($tabs.length > 0) {
                        console.log("WhatsApp Module: Found tabs container:", tabSelectors[i]);
                        
                        // Check if tab already exists
                        if ($tabs.find(\'a[href="#whatsapp_conversations"]\').length === 0) {
                            $tabs.append(\'<li role="presentation"><a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp Conversations</a></li>\');
                            console.log("WhatsApp Module: Tab added to", tabSelectors[i]);
                            tabAdded = true;
                        }
                        break;
                    }
                }
                
                // Try to find and add tab content
                for (var j = 0; j < contentSelectors.length; j++) {
                    var $content = $(contentSelectors[j]);
                    if ($content.length > 0) {
                        console.log("WhatsApp Module: Found content container:", contentSelectors[j]);
                        
                        // Check if content already exists
                        if ($content.find(\'#whatsapp_conversations\').length === 0) {
                            $content.append(\'<div role="tabpanel" class="tab-pane" id="whatsapp_conversations"><div class="alert alert-info"><h4><i class="fa fa-whatsapp"></i> WhatsApp Conversations</h4><p>Loading WhatsApp conversations...</p><p><small>If this message persists, please check module permissions and installation.</small></p></div></div>\');
                            console.log("WhatsApp Module: Content added to", contentSelectors[j]);
                        }
                        break;
                    }
                }
                
                if (tabAdded) {
                    console.log("WhatsApp Module: Fallback tab successfully added");
                } else {
                    console.log("WhatsApp Module: Could not find suitable tab container");
                    console.log("Available elements:", {
                        "nav-tabs": $(".nav-tabs").length,
                        "tab-content": $(".tab-content").length,
                        "all-tabs": $("[class*=\'tab\']").length
                    });
                }
            }
            
            // Wait for jQuery and DOM
            function initWhatsAppFallback() {
                if (typeof jQuery !== "undefined") {
                    $(document).ready(function() {
                        setTimeout(addWhatsAppTabFallback, 1000); // Wait 1 second for page to fully load
                    });
                } else {
                    setTimeout(initWhatsAppFallback, 100);
                }
            }
            
            initWhatsAppFallback();
            
            // Load our main script
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
                    setTimeout(loadWhatsAppScript, 100);
                }
            }
            
            loadWhatsAppScript();
        </script>';
    }
}

/**
 * Add WhatsApp Conversations tab in customer profile
 */
function whatsapp_conversations_tab($customer_data = null)
{
    $customer_id = whatsapp_extract_customer_id($customer_data);
    whatsapp_debug_log("Tab function called for customer: $customer_id");
    
    if ($customer_id <= 0) {
        whatsapp_debug_log("Invalid customer ID, skipping tab");
        return;
    }
    
    if (has_permission('whatsapp_conversations', '', 'view') || is_admin()) {
        whatsapp_debug_log("Permission check passed for customer: $customer_id");
        
        echo '<li role="presentation">
            <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
                <i class="fa fa-whatsapp" aria-hidden="true"></i> 
                ' . _l('whatsapp_conversations') . '
            </a>
        </li>';
        
        whatsapp_debug_log("Tab HTML output for customer: $customer_id");
        
        echo '<script>
            console.log("WhatsApp Module: Tab added for customer: ' . $customer_id . '");
        </script>';
    } else {
        whatsapp_debug_log("Permission check FAILED for customer: $customer_id");
    }
}

/**
 * Add WhatsApp Conversations tab content
 */
function whatsapp_conversations_tab_content($customer_data = null)
{
    $customer_id = whatsapp_extract_customer_id($customer_data);
    whatsapp_debug_log("Tab content function called for customer: $customer_id");
    
    if ($customer_id <= 0) {
        whatsapp_debug_log("Invalid customer ID, skipping content");
        return;
    }
    
    if (has_permission('whatsapp_conversations', '', 'view') || is_admin()) {
        whatsapp_debug_log("Content permission check passed for customer: $customer_id");
        
        $CI = &get_instance();
        $CI->load->model('whatsapp_conversations_model');
        
        $data['customer_id'] = $customer_id;
        $data['conversations'] = $CI->whatsapp_conversations_model->get_by_customer($customer_id);
        $data['can_create'] = has_permission('whatsapp_conversations', '', 'create') || is_admin();
        $data['can_edit'] = has_permission('whatsapp_conversations', '', 'edit') || is_admin();
        $data['can_delete'] = has_permission('whatsapp_conversations', '', 'delete') || is_admin();

        echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">';
        $CI->load->view('whatsapp_conversations/customer_tab', $data);
        echo '</div>';
        
        whatsapp_debug_log("Tab content HTML output for customer: $customer_id");
        
        echo '<script>
            console.log("WhatsApp Module: Tab content loaded for customer: ' . $customer_id . '");
        </script>';
    } else {
        whatsapp_debug_log("Content permission check FAILED for customer: $customer_id");
    }
}

whatsapp_debug_log('Module initialization complete');