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
        // Check permission with fallback
        if (function_exists('has_permission') && !has_permission('whatsapp_conversations', '', 'create')) {
            if (function_exists('access_denied')) {
                access_denied('whatsapp_conversations');
            } else {
                show_error('Access denied', 403);
            }
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['customer_id'] = $customer_id;
            $data['staff_id'] = function_exists('get_staff_user_id') ? get_staff_user_id() : 1;
            $data['date_added'] = date('Y-m-d H:i:s');

            $id = $this->whatsapp_conversations_model->add($data);

            if ($id) {
                if (function_exists('set_alert')) {
                    set_alert('success', _l('added_successfully', _l('whatsapp_conversation')));
                }
            } else {
                if (function_exists('set_alert')) {
                    set_alert('danger', _l('problem_adding', _l('whatsapp_conversation')));
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
        // Check permission with fallback
        if (function_exists('has_permission') && !has_permission('whatsapp_conversations', '', 'edit')) {
            if (function_exists('access_denied')) {
                access_denied('whatsapp_conversations');
            } else {
                show_error('Access denied', 403);
            }
        }

        $conversation = $this->whatsapp_conversations_model->get($id);
        if (!$conversation) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->whatsapp_conversations_model->update($id, $data);

            if ($success) {
                if (function_exists('set_alert')) {
                    set_alert('success', _l('updated_successfully', _l('whatsapp_conversation')));
                }
            } else {
                if (function_exists('set_alert')) {
                    set_alert('danger', _l('problem_updating', _l('whatsapp_conversation')));
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
        // Check permission with fallback
        if (function_exists('has_permission') && !has_permission('whatsapp_conversations', '', 'delete')) {
            if (function_exists('access_denied')) {
                access_denied('whatsapp_conversations');
            } else {
                show_error('Access denied', 403);
            }
        }

        $conversation = $this->whatsapp_conversations_model->get($id);
        if (!$conversation) {
            show_404();
        }

        $success = $this->whatsapp_conversations_model->delete($id);

        if ($success) {
            if (function_exists('set_alert')) {
                set_alert('success', _l('deleted', _l('whatsapp_conversation')));
            }
        } else {
            if (function_exists('set_alert')) {
                set_alert('danger', _l('problem_deleting', _l('whatsapp_conversation')));
            }
        }

        redirect(admin_url('clients/client/' . $conversation->customer_id . '?group=whatsapp_conversations'));
    }

    /**
     * Get conversation for editing via AJAX
     */
    public function get_conversation($id)
    {
        // Check permission with fallback
        if (function_exists('has_permission') && !has_permission('whatsapp_conversations', '', 'view')) {
            if (function_exists('access_denied')) {
                access_denied('whatsapp_conversations');
            } else {
                show_error('Access denied', 403);
            }
        }

        $conversation = $this->whatsapp_conversations_model->get($id);

        if ($conversation) {
            echo json_encode($conversation);
        } else {
            echo json_encode(['error' => 'Conversation not found']);
        }
    }
}