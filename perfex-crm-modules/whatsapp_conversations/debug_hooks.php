<?php
// Debug file to check if hooks are being called
// Place this in the module root to test hook execution

defined('BASEPATH') or exit('No direct script access allowed');

// Log when hooks are called
function debug_log_hooks() {
    log_message('debug', 'WhatsApp Conversations: Hook system active');
    error_log('WhatsApp Conversations: Hook system active');
}

function debug_customer_tab($customer_id) {
    log_message('debug', 'WhatsApp Conversations: customer_profile_tab called for customer ' . $customer_id);
    error_log('WhatsApp Conversations: customer_profile_tab called for customer ' . $customer_id);
    
    // Force show tab for debugging
    echo '<li role="presentation" style="background-color: yellow;">
        <a href="#whatsapp_conversations" aria-controls="whatsapp_conversations" role="tab" data-toggle="tab">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp DEBUG
        </a>
    </li>';
}

function debug_customer_tab_content($customer_id) {
    log_message('debug', 'WhatsApp Conversations: customer_profile_tab_content called for customer ' . $customer_id);
    error_log('WhatsApp Conversations: customer_profile_tab_content called for customer ' . $customer_id);
    
    echo '<div role="tabpanel" class="tab-pane" id="whatsapp_conversations">
        <div class="alert alert-success">
            <h4>WhatsApp Conversations Debug Mode</h4>
            <p>Customer ID: ' . $customer_id . '</p>
            <p>Hook is working! Time: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </div>';
}

// Register debug hooks
hooks()->add_action('admin_init', 'debug_log_hooks');
hooks()->add_action('customer_profile_tab', 'debug_customer_tab');
hooks()->add_action('customer_profile_tab_content', 'debug_customer_tab_content');
hooks()->add_action('client_profile_tab', 'debug_customer_tab');
hooks()->add_action('client_profile_tab_content', 'debug_customer_tab_content');