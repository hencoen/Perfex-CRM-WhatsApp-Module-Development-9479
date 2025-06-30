<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Check if database connection exists
if (!isset($CI)) {
    $CI = &get_instance();
}

// Get the correct database prefix
$db_prefix = $CI->db->dbprefix;

// Create the WhatsApp conversations table if it doesn't exist
if (!$CI->db->table_exists($db_prefix . 'whatsapp_conversations')) {
    $CI->db->query('CREATE TABLE `' . $db_prefix . "whatsapp_conversations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `staff_id` int(11) NOT NULL,
        `conversation` text NOT NULL,
        `summary` text,
        `date_added` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `customer_id` (`customer_id`),
        KEY `staff_id` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// Add module permissions - Check if permissions table exists first
$permissions_table = $db_prefix . 'permissions';

// Try different possible table names for permissions
$possible_permission_tables = [
    $db_prefix . 'permissions',
    $db_prefix . 'tblpermissions', 
    $db_prefix . 'staff_permissions',
    'permissions',
    'tblpermissions'
];

$permission_table_found = false;
$active_permission_table = '';

foreach ($possible_permission_tables as $table) {
    if ($CI->db->table_exists($table)) {
        $permission_table_found = true;
        $active_permission_table = $table;
        break;
    }
}

// Only add permissions if we found the permissions table
if ($permission_table_found) {
    // Check if permission already exists
    $existing_permission = $CI->db->get_where($active_permission_table, array('name' => 'whatsapp_conversations'))->row();
    
    if (!$existing_permission) {
        $CI->db->insert($active_permission_table, array(
            'name' => 'whatsapp_conversations',
            'shortname' => 'whatsapp_conversations'
        ));
    }
} else {
    // Log that permissions table wasn't found but don't fail the installation
    log_message('info', 'WhatsApp Conversations Module: Permissions table not found. Module installed without permission registration.');
}

// Create a simple log entry to confirm installation
if (function_exists('log_activity')) {
    log_activity('WhatsApp Conversations Module Installed Successfully');
}