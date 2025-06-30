<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Debug function
function whatsapp_install_debug($message) {
    log_message('info', 'WhatsApp Install Debug: ' . $message);
}

whatsapp_install_debug('Installation started');

// Check if database connection exists
if (!isset($CI)) {
    $CI = &get_instance();
}

// Get the correct database prefix
$db_prefix = $CI->db->dbprefix;
whatsapp_install_debug('Database prefix: ' . $db_prefix);

// Create the WhatsApp conversations table if it doesn't exist
$table_name = $db_prefix . 'whatsapp_conversations';
if (!$CI->db->table_exists($table_name)) {
    whatsapp_install_debug('Creating table: ' . $table_name);
    
    try {
        $CI->db->query('CREATE TABLE `' . $table_name . "` (
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
        
        whatsapp_install_debug('Table created successfully');
    } catch (Exception $e) {
        whatsapp_install_debug('Error creating table: ' . $e->getMessage());
    }
} else {
    whatsapp_install_debug('Table already exists: ' . $table_name);
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

whatsapp_install_debug('Looking for permission tables...');

foreach ($possible_permission_tables as $table) {
    whatsapp_install_debug('Checking table: ' . $table);
    
    if ($CI->db->table_exists($table)) {
        whatsapp_install_debug('Table exists: ' . $table);
        
        try {
            // Get table structure to understand column names
            $fields = $CI->db->field_data($table);
            $column_names = array();
            foreach ($fields as $field) {
                $column_names[] = $field->name;
            }
            
            whatsapp_install_debug('Table columns: ' . implode(', ', $column_names));
            
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
            
            whatsapp_install_debug('Name column: ' . ($name_column ?: 'none') . ', Short column: ' . ($shortname_column ?: 'none'));
            
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
                    whatsapp_install_debug('Permission added to table: ' . $table);
                    break;
                } else {
                    whatsapp_install_debug('Permission already exists in table: ' . $table);
                    $permission_added = true;
                    break;
                }
            } else {
                whatsapp_install_debug('No suitable name column found in table: ' . $table);
            }
            
        } catch (Exception $e) {
            whatsapp_install_debug('Error with permissions table ' . $table . ': ' . $e->getMessage());
            continue;
        }
    } else {
        whatsapp_install_debug('Table does not exist: ' . $table);
    }
}

if (!$permission_added) {
    whatsapp_install_debug('Could not add permissions automatically. Module installed with basic functionality.');
} else {
    whatsapp_install_debug('Permissions added successfully');
}

// Create a simple log entry to confirm installation
if (function_exists('log_activity')) {
    try {
        log_activity('WhatsApp Conversations Module Installed Successfully');
        whatsapp_install_debug('Activity logged');
    } catch (Exception $e) {
        whatsapp_install_debug('Error logging activity: ' . $e->getMessage());
    }
}

whatsapp_install_debug('Installation completed');