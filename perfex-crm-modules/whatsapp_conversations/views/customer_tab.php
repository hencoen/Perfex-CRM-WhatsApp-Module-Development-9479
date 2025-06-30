<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <?php if ($can_create) { ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#whatsapp_conversation_form" data-toggle="collapse" aria-expanded="false" class="collapsed">
                            <i class="fa fa-plus"></i> 
                            <?php echo _l('add_new', _l('whatsapp_conversation')); ?>
                        </a>
                    </h4>
                </div>
                <div id="whatsapp_conversation_form" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('whatsapp_conversations/add/' . $customer_id), array('id' => 'whatsapp-conversation-form')); ?>
                        <div class="form-group">
                            <label for="summary"><?php echo _l('whatsapp_conversation_summary'); ?></label>
                            <input type="text" class="form-control" name="summary" id="summary" 
                                   placeholder="<?php echo _l('whatsapp_conversation_summary_placeholder'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="conversation">
                                <?php echo _l('whatsapp_conversation_content'); ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="conversation" id="conversation" rows="8" class="form-control" 
                                      placeholder="<?php echo _l('whatsapp_conversation_content_placeholder'); ?>" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-info pull-right">
                            <i class="fa fa-save"></i> <?php echo _l('save'); ?>
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
                        <i class="fa fa-whatsapp"></i> 
                        <?php echo _l('whatsapp_conversations'); ?>
                        <span class="badge"><?php echo count($conversations); ?></span>
                    </h4>
                </div>
                <div class="panel-body">
                    <?php foreach ($conversations as $conversation) { ?>
                        <div class="whatsapp-conversation-item" data-conversation-id="<?php echo $conversation->id; ?>">
                            <div class="conversation-header">
                                <div class="pull-left">
                                    <strong><?php echo htmlspecialchars($conversation->staff_name); ?></strong>
                                    <small class="text-muted"><?php echo _dt($conversation->date_added); ?></small>
                                </div>
                                <div class="pull-right">
                                    <?php if ($can_edit || $can_delete) { ?>
                                        <div class="btn-group">
                                            <?php if ($can_edit) { ?>
                                                <button type="button" class="btn btn-default btn-xs edit-conversation" 
                                                        data-conversation-id="<?php echo $conversation->id; ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            <?php } ?>
                                            <?php if ($can_delete) { ?>
                                                <a href="<?php echo admin_url('whatsapp_conversations/delete/' . $conversation->id); ?>" 
                                                   class="btn btn-danger btn-xs delete-conversation">
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
                                    <strong><?php echo _l('summary'); ?>:</strong> 
                                    <?php echo nl2br(htmlspecialchars($conversation->summary)); ?>
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
                    <h4 class="text-muted"><?php echo _l('no_whatsapp_conversations_found'); ?></h4>
                    <?php if ($can_create) { ?>
                        <p class="text-muted"><?php echo _l('click_to_add_first_conversation'); ?></p>
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
                    <h4 class="modal-title"><?php echo _l('edit_whatsapp_conversation'); ?></h4>
                </div>
                <div class="modal-body">
                    <form id="edit-conversation-form">
                        <div class="form-group">
                            <label for="edit_summary"><?php echo _l('whatsapp_conversation_summary'); ?></label>
                            <input type="text" class="form-control" name="summary" id="edit_summary">
                        </div>
                        <div class="form-group">
                            <label for="edit_conversation">
                                <?php echo _l('whatsapp_conversation_content'); ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="conversation" id="edit_conversation" rows="8" class="form-control" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-info" id="save-conversation-edit">
                        <i class="fa fa-save"></i> <?php echo _l('save'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
console.log('WhatsApp Module: Customer tab view loaded for customer <?php echo $customer_id; ?>');
</script>