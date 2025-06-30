<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'whatsapp_conversations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "whatsapp_conversations` (
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

// Add module permissions
if (!$CI->db->get_where('tblpermissions', array('name' => 'whatsapp_conversations'))->row()) {
    $CI->db->insert('tblpermissions', array(
        'name' => 'whatsapp_conversations',
        'shortname' => 'whatsapp_conversations'
    ));
}