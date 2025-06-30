<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.2
Author: Custom Module
Requires at least: 2.3.*
*/

define('WHATSAPP_CONVERSATIONS_MODULE_NAME', 'whatsapp_conversations');

// Register hooks early
hooks()->add_action('admin_init', 'whatsapp_conversations_module_init_menu_items');
hooks()->add_action('app_admin_head', 'whatsapp_conversations_add_head_components');

// These are the critical hooks for the customer tab
hooks()->add_action('customer_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');

// Also try alternative hook names that might be used in different versions
hooks()->add_action('client_profile_tab', 'whatsapp_conversations_tab');
hooks()->add_action('client_profile_tab_content', 'whatsapp_conversations_tab_content');

/**
 * Register activation module hook
 */
register_activation_hook(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'whatsapp_conversations_module_activation_hook');

function whatsapp_conversations_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(WHATSAPP_CONVERSATIONS_MODULE_NAME, [WHATSAPP_CONVERSATIONS_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function whatsapp_conversations_module_init_menu_items()
{
    $CI = &get_instance();
    
    // Only register capabilities if the function exists and we're in a proper admin context
    if (function_exists('register_staff_capabilities') && function_exists('_l')) {
        try {
            $capabilities = [];
            $capabilities['capabilities'] = [
                'view' => 'View WhatsApp Conversations',
                'create' => 'Create WhatsApp Conversations', 
                'edit' => 'Edit WhatsApp Conversations',
                'delete' => 'Delete WhatsApp Conversations',
            ];
            
            register_staff_capabilities('whatsapp_conversations', $capabilities, 'WhatsApp Conversations');
        } catch (Exception $e) {
            // Log error but don't break functionality
            log_message('error', 'WhatsApp Conversations Module: Error registering capabilities: ' . $e->getMessage());
        }
    }
}

/**
 * Add additional CSS/JS files in head
 */
function whatsapp_conversations_add_head_components()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    // Load assets on customer/client pages
    if (strpos($viewuri, 'admin/clients/client') !== false || strpos($viewuri, 'admin/customers/client') !== false) {
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/js/whatsapp_conversations.js') . '"></script>';
    }
}

/**
 * Check if user has permission with fallback
 */
function whatsapp_conversations_has_permission($permission_type = 'view')
{
    if (function_exists('has_permission')) {
        try {
            return has_permission('whatsapp_conversations', '', $permission_type);
        } catch (Exception $e) {
            // If permission system fails, allow basic access for admins
            if (function_exists('is_admin')) {
                return is_admin();
            }
            return true; // Fallback to allow access
        }
    }
    
    // If no permission system, allow access for staff users
    if (function_exists('is_staff_logged_in')) {
        return is_staff_logged_in();
    }
    
    return true; // Final fallback - allow access
}

/**
 * Add WhatsApp Conversations tab in customer profile
 */
function whatsapp_conversations_tab($customer_id)
{
    // Always show the tab for now to test
    echo '<li role="presentation" class="whatsapp-conversations-tab">
        <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp Conversations
        </a>
    </li>';
}

/**
 * Add WhatsApp Conversations tab content
 */
function whatsapp_conversations_tab_content($customer_id)
{
    $CI = &get_instance();
    $CI->load->model('whatsapp_conversations_model');
    
    $data['customer_id'] = $customer_id;
    $data['conversations'] = $CI->whatsapp_conversations_model->get_by_customer($customer_id);
    
    // Permission checks with fallbacks
    $data['can_create'] = whatsapp_conversations_has_permission('create');
    $data['can_edit'] = whatsapp_conversations_has_permission('edit');
    $data['can_delete'] = whatsapp_conversations_has_permission('delete');
        
    echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">';
    $CI->load->view('whatsapp_conversations/customer_tab', $data);
    echo '</div>';
}