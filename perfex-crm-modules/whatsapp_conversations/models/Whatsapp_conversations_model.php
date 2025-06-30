<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_conversations_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        log_message('info', 'WhatsApp Conversations Model loaded');
    }

    /**
     * Get all conversations for a customer
     * @param int $customer_id
     * @return array
     */
    public function get_by_customer($customer_id)
    {
        log_message('info', 'Getting conversations for customer: ' . $customer_id);
        
        try {
            $this->db->select('wc.*, CONCAT(s.firstname, " ", s.lastname) as staff_name');
            $this->db->from(db_prefix() . 'whatsapp_conversations wc');
            $this->db->join(db_prefix() . 'staff s', 's.staffid = wc.staff_id', 'left');
            $this->db->where('wc.customer_id', $customer_id);
            $this->db->order_by('wc.date_added', 'DESC');
            
            $result = $this->db->get()->result();
            log_message('info', 'Found ' . count($result) . ' conversations for customer: ' . $customer_id);
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error getting conversations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get single conversation
     * @param int $id
     * @return object
     */
    public function get($id)
    {
        try {
            $this->db->where('id', $id);
            $result = $this->db->get(db_prefix() . 'whatsapp_conversations')->row();
            log_message('info', 'Getting conversation ID: ' . $id . ' - ' . ($result ? 'found' : 'not found'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error getting conversation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add new conversation
     * @param array $data
     * @return int
     */
    public function add($data)
    {
        try {
            $this->db->insert(db_prefix() . 'whatsapp_conversations', $data);
            $insert_id = $this->db->insert_id();
            
            if ($insert_id && function_exists('log_activity')) {
                log_activity('New WhatsApp Conversation Added [Customer ID: ' . $data['customer_id'] . ']');
            }
            
            log_message('info', 'Added conversation with ID: ' . $insert_id);
            return $insert_id;
        } catch (Exception $e) {
            log_message('error', 'Error adding conversation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update conversation
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function update($id, $data)
    {
        try {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'whatsapp_conversations', $data);
            
            if ($this->db->affected_rows() > 0) {
                if (function_exists('log_activity')) {
                    log_activity('WhatsApp Conversation Updated [ID: ' . $id . ']');
                }
                log_message('info', 'Updated conversation ID: ' . $id);
                return true;
            }
            
            log_message('info', 'No rows affected when updating conversation ID: ' . $id);
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error updating conversation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete conversation
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        try {
            $conversation = $this->get($id);
            if (!$conversation) {
                log_message('info', 'Conversation not found for deletion ID: ' . $id);
                return false;
            }

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'whatsapp_conversations');

            if ($this->db->affected_rows() > 0) {
                if (function_exists('log_activity')) {
                    log_activity('WhatsApp Conversation Deleted [ID: ' . $id . ']');
                }
                log_message('info', 'Deleted conversation ID: ' . $id);
                return true;
            }
            
            log_message('info', 'No rows affected when deleting conversation ID: ' . $id);
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error deleting conversation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total conversations count for customer
     * @param int $customer_id
     * @return int
     */
    public function get_total_by_customer($customer_id)
    {
        try {
            $this->db->where('customer_id', $customer_id);
            $count = $this->db->count_all_results(db_prefix() . 'whatsapp_conversations');
            log_message('info', 'Total conversations for customer ' . $customer_id . ': ' . $count);
            return $count;
        } catch (Exception $e) {
            log_message('error', 'Error counting conversations: ' . $e->getMessage());
            return 0;
        }
    }
}