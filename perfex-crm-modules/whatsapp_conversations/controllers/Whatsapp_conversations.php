<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_conversations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('whatsapp_conversations_model');
    }

    /**
     * Add new WhatsApp conversation
     */
    public function add($customer_id)
    {
        if (!has_permission('whatsapp_conversations', '', 'create')) {
            access_denied('whatsapp_conversations');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['customer_id'] = $customer_id;
            $data['staff_id'] = get_staff_user_id();
            $data['date_added'] = date('Y-m-d H:i:s');

            $id = $this->whatsapp_conversations_model->add($data);
            
            if ($id) {
                set_alert('success', _l('added_successfully', _l('whatsapp_conversation')));
            } else {
                set_alert('danger', _l('problem_adding', _l('whatsapp_conversation')));
            }
        }

        redirect(admin_url('clients/client/' . $customer_id . '?group=whatsapp_conversations'));
    }

    /**
     * Edit WhatsApp conversation
     */
    public function edit($id)
    {
        if (!has_permission('whatsapp_conversations', '', 'edit')) {
            access_denied('whatsapp_conversations');
        }

        $conversation = $this->whatsapp_conversations_model->get($id);
        
        if (!$conversation) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->whatsapp_conversations_model->update($id, $data);
            
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('whatsapp_conversation')));
            } else {
                set_alert('danger', _l('problem_updating', _l('whatsapp_conversation')));
            }
            
            redirect(admin_url('clients/client/' . $conversation->customer_id . '?group=whatsapp_conversations'));
        }

        $data['conversation'] = $conversation;
        $this->load->view('whatsapp_conversations/edit', $data);
    }

    /**
     * Delete WhatsApp conversation
     */
    public function delete($id)
    {
        if (!has_permission('whatsapp_conversations', '', 'delete')) {
            access_denied('whatsapp_conversations');
        }

        $conversation = $this->whatsapp_conversations_model->get($id);
        
        if (!$conversation) {
            show_404();
        }

        $success = $this->whatsapp_conversations_model->delete($id);
        
        if ($success) {
            set_alert('success', _l('deleted', _l('whatsapp_conversation')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('whatsapp_conversation')));
        }

        redirect(admin_url('clients/client/' . $conversation->customer_id . '?group=whatsapp_conversations'));
    }

    /**
     * Get conversation for editing via AJAX
     */
    public function get_conversation($id)
    {
        if (!has_permission('whatsapp_conversations', '', 'view')) {
            access_denied('whatsapp_conversations');
        }

        $conversation = $this->whatsapp_conversations_model->get($id);
        
        if ($conversation) {
            echo json_encode($conversation);
        } else {
            echo json_encode(['error' => 'Conversation not found']);
        }
    }
}