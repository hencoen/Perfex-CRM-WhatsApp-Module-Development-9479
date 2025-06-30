<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_conversations_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all conversations for a customer
     * @param  int $customer_id
     * @return array
     */
    public function get_by_customer($customer_id)
    {
        $this->db->select('wc.*, CONCAT(s.firstname, " ", s.lastname) as staff_name');
        $this->db->from(db_prefix() . 'whatsapp_conversations wc');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = wc.staff_id', 'left');
        $this->db->where('wc.customer_id', $customer_id);
        $this->db->order_by('wc.date_added', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get single conversation
     * @param  int $id
     * @return object
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'whatsapp_conversations')->row();
    }

    /**
     * Add new conversation
     * @param array $data
     * @return int
     */
    public function add($data)
    {
        $this->db->insert(db_prefix() . 'whatsapp_conversations', $data);
        
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('New WhatsApp Conversation Added [Customer ID: ' . $data['customer_id'] . ']');
        }
        
        return $insert_id;
    }

    /**
     * Update conversation
     * @param  int $id
     * @param  array $data
     * @return boolean
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'whatsapp_conversations', $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('WhatsApp Conversation Updated [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Delete conversation
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $conversation = $this->get($id);
        
        if (!$conversation) {
            return false;
        }
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'whatsapp_conversations');
        
        if ($this->db->affected_rows() > 0) {
            log_activity('WhatsApp Conversation Deleted [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Get total conversations count for customer
     * @param  int $customer_id
     * @return int
     */
    public function get_total_by_customer($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        return $this->db->count_all_results(db_prefix() . 'whatsapp_conversations');
    }
}