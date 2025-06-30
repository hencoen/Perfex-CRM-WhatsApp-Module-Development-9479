<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: WhatsApp Conversations
Description: Manage WhatsApp conversations for customers
Version: 1.0.0
Author: Custom Module
Requires at least: 2.3.*
*/

define('WHATSAPP_CONVERSATIONS_MODULE_NAME', 'whatsapp_conversations');

hooks()->add_action('admin_init', 'whatsapp_conversations_module_init_menu_items');
hooks()->add_action('app_admin_head', 'whatsapp_conversations_add_head_components');
hooks()->add_action('customer_profile_tab_content', 'whatsapp_conversations_tab_content');
hooks()->add_action('customer_profile_tab', 'whatsapp_conversations_tab');

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
    
    // Only register capabilities if the function exists (Perfex CRM compatibility)
    if (function_exists('register_staff_capabilities')) {
        $capabilities = [];
        $capabilities['capabilities'] = [
            'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit' => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ];
        
        register_staff_capabilities('whatsapp_conversations', $capabilities, _l('whatsapp_conversations'));
    }
}

/**
 * Add additional CSS/JS files in head
 */
function whatsapp_conversations_add_head_components()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if (!(strpos($viewuri, 'admin/clients/client') === false)) {
        echo '<link href="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/css/whatsapp_conversations.css') . '" rel="stylesheet" type="text/css" />';
        echo '<script src="' . module_dir_url(WHATSAPP_CONVERSATIONS_MODULE_NAME, 'assets/js/whatsapp_conversations.js') . '"></script>';
    }
}

/**
 * Add WhatsApp Conversations tab in customer profile
 */
function whatsapp_conversations_tab($customer_id)
{
    // Check permission with fallback for older Perfex versions
    $has_permission = function_exists('has_permission') ? 
        has_permission('whatsapp_conversations', '', 'view') : 
        true; // Fallback to true if permission function doesn't exist
        
    if ($has_permission) {
        echo '<li role="presentation">
            <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
                <i class="fa fa-whatsapp" aria-hidden="true"></i> ' . _l('whatsapp_conversations') . '
            </a>
        </li>';
    }
}

/**
 * Add WhatsApp Conversations tab content
 */
function whatsapp_conversations_tab_content($customer_id)
{
    // Check permission with fallback for older Perfex versions
    $has_permission = function_exists('has_permission') ? 
        has_permission('whatsapp_conversations', '', 'view') : 
        true; // Fallback to true if permission function doesn't exist
        
    if ($has_permission) {
        $CI = &get_instance();
        $CI->load->model('whatsapp_conversations_model');
        
        $data['customer_id'] = $customer_id;
        $data['conversations'] = $CI->whatsapp_conversations_model->get_by_customer($customer_id);
        
        // Permission checks with fallbacks
        $data['can_create'] = function_exists('has_permission') ? 
            has_permission('whatsapp_conversations', '', 'create') : true;
        $data['can_edit'] = function_exists('has_permission') ? 
            has_permission('whatsapp_conversations', '', 'edit') : true;
        $data['can_delete'] = function_exists('has_permission') ? 
            has_permission('whatsapp_conversations', '', 'delete') : true;
            
        echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">';
        $CI->load->view('whatsapp_conversations/customer_tab', $data);
        echo '</div>';
    }
}