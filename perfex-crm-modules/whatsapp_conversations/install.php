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

// Try to add module permissions with enhanced error handling
$permission_added = false;

// Try different possible table names for permissions
$possible_permission_tables = [
    $db_prefix . 'permissions',
    $db_prefix . 'tblpermissions', 
    $db_prefix . 'staff_permissions',
    'permissions',
    'tblpermissions'
];

foreach ($possible_permission_tables as $table) {
    if ($CI->db->table_exists($table)) {
        try {
            // Get table structure to understand column names
            $fields = $CI->db->field_data($table);
            $column_names = array();
            foreach ($fields as $field) {
                $column_names[] = $field->name;
            }
            
            // Try different possible column name combinations
            $name_column = null;
            $shortname_column = null;
            
            // Check for common permission column names
            if (in_array('name', $column_names)) {
                $name_column = 'name';
            } elseif (in_array('permission_name', $column_names)) {
                $name_column = 'permission_name';
            } elseif (in_array('perm_name', $column_names)) {
                $name_column = 'perm_name';
            }
            
            if (in_array('shortname', $column_names)) {
                $shortname_column = 'shortname';
            } elseif (in_array('short_name', $column_names)) {
                $shortname_column = 'short_name';
            } elseif (in_array('perm_short', $column_names)) {
                $shortname_column = 'perm_short';
            }
            
            // Only try to insert if we found appropriate columns
            if ($name_column) {
                // Check if permission already exists
                $where_condition = array($name_column => 'whatsapp_conversations');
                $existing_permission = $CI->db->get_where($table, $where_condition)->row();
                
                if (!$existing_permission) {
                    $insert_data = array($name_column => 'whatsapp_conversations');
                    if ($shortname_column) {
                        $insert_data[$shortname_column] = 'whatsapp_conversations';
                    }
                    
                    $CI->db->insert($table, $insert_data);
                    $permission_added = true;
                    log_message('info', 'WhatsApp Conversations Module: Permission added to table ' . $table);
                    break;
                }
            }
            
        } catch (Exception $e) {
            // Log the error but continue
            log_message('error', 'WhatsApp Conversations Module: Error with permissions table ' . $table . ': ' . $e->getMessage());
            continue;
        }
    }
}

if (!$permission_added) {
    // Log that permissions couldn't be added but don't fail the installation
    log_message('info', 'WhatsApp Conversations Module: Could not add permissions automatically. Module installed with basic functionality.');
}

// Create a simple log entry to confirm installation
if (function_exists('log_activity')) {
    log_activity('WhatsApp Conversations Module Installed Successfully');
}

// Log installation success
log_message('info', 'WhatsApp Conversations Module: Installation completed successfully');