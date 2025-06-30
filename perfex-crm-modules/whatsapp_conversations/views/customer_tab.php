<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <!-- Debug Info -->
        <div class="alert alert-info">
            <strong>WhatsApp Conversations Module Active</strong><br>
            Customer ID: <?php echo $customer_id; ?><br>
            Conversations Found: <?php echo count($conversations); ?><br>
            Can Create: <?php echo $can_create ? 'Yes' : 'No'; ?><br>
            Can Edit: <?php echo $can_edit ? 'Yes' : 'No'; ?><br>
            Can Delete: <?php echo $can_delete ? 'Yes' : 'No'; ?>
        </div>

        <?php if ($can_create) { ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#whatsapp_conversation_form" data-toggle="collapse" aria-expanded="false" class="collapsed">
                            <i class="fa fa-plus"></i> Add New WhatsApp Conversation
                        </a>
                    </h4>
                </div>
                <div id="whatsapp_conversation_form" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('whatsapp_conversations/add/' . $customer_id), array('id' => 'whatsapp-conversation-form')); ?>
                        
                        <div class="form-group">
                            <label for="summary">Conversation Summary</label>
                            <input type="text" class="form-control" name="summary" id="summary" placeholder="Brief summary of the conversation...">
                        </div>
                        
                        <div class="form-group">
                            <label for="conversation">Conversation Content <span class="text-danger">*</span></label>
                            <textarea name="conversation" id="conversation" rows="8" class="form-control" placeholder="Full conversation details..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-info pull-right">
                            <i class="fa fa-save"></i> Save
                        </button>
                        <div class="clearfix"></div>
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (count($conversations) > 0) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-whatsapp"></i> WhatsApp Conversations
                        <span class="badge"><?php echo count($conversations); ?></span>
                    </h4>
                </div>
                <div class="panel-body">
                    <?php foreach ($conversations as $conversation) { ?>
                        <div class="whatsapp-conversation-item" data-conversation-id="<?php echo $conversation->id; ?>">
                            <div class="conversation-header">
                                <div class="pull-left">
                                    <strong><?php echo htmlspecialchars($conversation->staff_name); ?></strong>
                                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($conversation->date_added)); ?></small>
                                </div>
                                <div class="pull-right">
                                    <?php if ($can_edit || $can_delete) { ?>
                                        <div class="btn-group">
                                            <?php if ($can_edit) { ?>
                                                <button type="button" class="btn btn-default btn-xs edit-conversation" data-conversation-id="<?php echo $conversation->id; ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            <?php } ?>
                                            <?php if ($can_delete) { ?>
                                                <a href="<?php echo admin_url('whatsapp_conversations/delete/' . $conversation->id); ?>" 
                                                   class="btn btn-danger btn-xs delete-conversation" 
                                                   onclick="return confirm('Are you sure you want to delete this conversation?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            
                            <?php if (!empty($conversation->summary)) { ?>
                                <div class="conversation-summary">
                                    <strong>Summary:</strong> <?php echo nl2br(htmlspecialchars($conversation->summary)); ?>
                                </div>
                            <?php } ?>
                            
                            <div class="conversation-content">
                                <?php echo nl2br(htmlspecialchars($conversation->conversation)); ?>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <i class="fa fa-whatsapp fa-3x text-muted"></i>
                    <h4 class="text-muted">No WhatsApp conversations found</h4>
                    <?php if ($can_create) { ?>
                        <p class="text-muted">Click the "Add New" button above to add your first conversation.</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Edit Conversation Modal -->
<?php if ($can_edit) { ?>
    <div class="modal fade" id="edit-conversation-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit WhatsApp Conversation</h4>
                </div>
                <div class="modal-body">
                    <form id="edit-conversation-form">
                        <div class="form-group">
                            <label for="edit_summary">Conversation Summary</label>
                            <input type="text" class="form-control" name="summary" id="edit_summary">
                        </div>
                        <div class="form-group">
                            <label for="edit_conversation">Conversation Content <span class="text-danger">*</span></label>
                            <textarea name="conversation" id="edit_conversation" rows="8" class="form-control" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="save-conversation-edit">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>