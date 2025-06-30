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
     * Check permission with enhanced fallback
     */
    private function check_permission($permission_type)
    {
        if (function_exists('has_permission')) {
            try {
                if (!has_permission('whatsapp_conversations', '', $permission_type)) {
                    if (function_exists('access_denied')) {
                        access_denied('whatsapp_conversations');
                    } else {
                        show_error('Access denied', 403);
                    }
                }
                return true;
            } catch (Exception $e) {
                // If permission check fails, check if user is admin
                if (function_exists('is_admin') && is_admin()) {
                    return true;
                }
                show_error('Permission system error', 500);
            }
        }

        // Fallback: check if user is staff
        if (function_exists('is_staff_logged_in') && !is_staff_logged_in()) {
            show_error('Access denied', 403);
        }

        return true;
    }

    /**
     * Get tab content via AJAX
     */
    public function get_tab_content($customer_id)
    {
        $this->check_permission('view');
        
        $data['customer_id'] = $customer_id;
        $data['conversations'] = $this->whatsapp_conversations_model->get_by_customer($customer_id);
        $data['can_create'] = has_permission('whatsapp_conversations', '', 'create') || is_admin();
        $data['can_edit'] = has_permission('whatsapp_conversations', '', 'edit') || is_admin();
        $data['can_delete'] = has_permission('whatsapp_conversations', '', 'delete') || is_admin();

        $this->load->view('whatsapp_conversations/customer_tab', $data);
    }

    /**
     * Add new WhatsApp conversation
     */
    public function add($customer_id)
    {
        $this->check_permission('create');

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['customer_id'] = $customer_id;
            $data['staff_id'] = function_exists('get_staff_user_id') ? get_staff_user_id() : 1;
            $data['date_added'] = date('Y-m-d H:i:s');

            $id = $this->whatsapp_conversations_model->add($data);

            if ($id) {
                if (function_exists('set_alert')) {
                    set_alert('success', 'WhatsApp conversation added successfully');
                }
            } else {
                if (function_exists('set_alert')) {
                    set_alert('danger', 'Problem adding WhatsApp conversation');
                }
            }
        }

        redirect(admin_url('clients/client/' . $customer_id . '?group=whatsapp_conversations'));
    }

    /**
     * Edit WhatsApp conversation
     */
    public function edit($id)
    {
        $this->check_permission('edit');

        $conversation = $this->whatsapp_conversations_model->get($id);
        if (!$conversation) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->whatsapp_conversations_model->update($id, $data);

            if ($success) {
                if (function_exists('set_alert')) {
                    set_alert('success', 'WhatsApp conversation updated successfully');
                }
            } else {
                if (function_exists('set_alert')) {
                    set_alert('danger', 'Problem updating WhatsApp conversation');
                }
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
        $this->check_permission('delete');

        $conversation = $this->whatsapp_conversations_model->get($id);
        if (!$conversation) {
            show_404();
        }

        $success = $this->whatsapp_conversations_model->delete($id);

        if ($success) {
            if (function_exists('set_alert')) {
                set_alert('success', 'WhatsApp conversation deleted successfully');
            }
        } else {
            if (function_exists('set_alert')) {
                set_alert('danger', 'Problem deleting WhatsApp conversation');
            }
        }

        redirect(admin_url('clients/client/' . $conversation->customer_id . '?group=whatsapp_conversations'));
    }

    /**
     * Get conversation for editing via AJAX
     */
    public function get_conversation($id)
    {
        $this->check_permission('view');

        $conversation = $this->whatsapp_conversations_model->get($id);

        if ($conversation) {
            echo json_encode($conversation);
        } else {
            echo json_encode(['error' => 'Conversation not found']);
        }
    }
}