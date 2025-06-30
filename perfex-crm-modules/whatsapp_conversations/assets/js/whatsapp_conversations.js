/**
 * WhatsApp Conversations Module JavaScript
 */

// Wait for jQuery to be available
(function() {
    'use strict';
    
    // Function to initialize our module
    function initWhatsAppModule() {
        console.log('WhatsApp Module: Initializing JavaScript');
        
        // Handle edit conversation button click
        $(document).on('click', '.edit-conversation', function(e) {
            e.preventDefault();
            var conversationId = $(this).data('conversation-id');
            loadConversationForEdit(conversationId);
        });

        // Handle save conversation edit
        $(document).on('click', '#save-conversation-edit', function(e) {
            e.preventDefault();
            saveConversationEdit();
        });

        // Auto-expand textarea on focus
        $(document).on('focus', 'textarea[name="conversation"], textarea[name="summary"]', function() {
            $(this).animate({height: '150px'}, 200);
        });

        // Form validation
        $('#whatsapp-conversation-form').on('submit', function(e) {
            var conversation = $.trim($('#conversation').val());
            if (conversation === '') {
                e.preventDefault();
                alert('Please enter the conversation content.');
                $('#conversation').focus();
                return false;
            }
        });

        /**
         * Load conversation data for editing
         */
        function loadConversationForEdit(conversationId) {
            $.get(admin_url + 'whatsapp_conversations/get_conversation/' + conversationId)
                .done(function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.error) {
                            alert('Error loading conversation: ' + data.error);
                            return;
                        }
                        
                        $('#edit_summary').val(data.summary || '');
                        $('#edit_conversation').val(data.conversation || '');
                        $('#edit-conversation-form').data('conversation-id', conversationId);
                        $('#edit-conversation-modal').modal('show');
                    } catch (e) {
                        alert('Error parsing response data.');
                        console.error('Parse error:', e);
                    }
                })
                .fail(function(xhr, status, error) {
                    alert('Error loading conversation data: ' + error);
                    console.error('AJAX error:', status, error);
                });
        }

        /**
         * Save conversation edit
         */
        function saveConversationEdit() {
            var conversationId = $('#edit-conversation-form').data('conversation-id');
            var summary = $.trim($('#edit_summary').val());
            var conversation = $.trim($('#edit_conversation').val());

            if (conversation === '') {
                alert('Please enter the conversation content.');
                $('#edit_conversation').focus();
                return;
            }

            // Show loading state
            var $saveBtn = $('#save-conversation-edit');
            var originalText = $saveBtn.html();
            $saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            // Prepare data object
            var postData = {
                summary: summary,
                conversation: conversation
            };
            
            // Add CSRF token if available
            if (typeof csrfData !== 'undefined') {
                postData[csrfData.token_name] = csrfData.hash;
            }

            $.post(admin_url + 'whatsapp_conversations/edit/' + conversationId, postData)
                .done(function(response) {
                    // Update CSRF token if provided
                    if (typeof(csrfData) !== 'undefined' && response.csrf_hash) {
                        csrfData.hash = response.csrf_hash;
                    }
                    
                    $('#edit-conversation-modal').modal('hide');
                    // Reload the page to show updated data
                    window.location.reload();
                })
                .fail(function(xhr, status, error) {
                    alert('Error saving conversation: ' + error);
                    console.error('Save error:', status, error);
                })
                .always(function() {
                    // Restore button state
                    $saveBtn.html(originalText).prop('disabled', false);
                });
        }

        /**
         * Confirm delete with better UX
         */
        $(document).on('click', '.delete-conversation', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            if (confirm('Are you sure you want to delete this WhatsApp conversation? This action cannot be undone.')) {
                window.location.href = href;
            }
        });

        /**
         * Auto-save form data to localStorage (optional feature)
         */
        var formData = {};
        $('#conversation, #summary').on('input', function() {
            var fieldName = $(this).attr('name');
            var fieldValue = $(this).val();
            formData[fieldName] = fieldValue;
            localStorage.setItem('whatsapp_conversation_draft', JSON.stringify(formData));
        });

        // Restore draft data on page load
        var savedDraft = localStorage.getItem('whatsapp_conversation_draft');
        if (savedDraft) {
            try {
                var draftData = JSON.parse(savedDraft);
                if (draftData.conversation && $('#conversation').val() === '') {
                    $('#conversation').val(draftData.conversation);
                }
                if (draftData.summary && $('#summary').val() === '') {
                    $('#summary').val(draftData.summary);
                }
            } catch (e) {
                // Clear invalid draft data
                localStorage.removeItem('whatsapp_conversation_draft');
            }
        }

        // Clear draft after successful form submission
        $('#whatsapp-conversation-form').on('submit', function() {
            localStorage.removeItem('whatsapp_conversation_draft');
        });

        console.log('WhatsApp Module: JavaScript initialization complete');
    }

    // Check if jQuery is available and initialize
    function checkJQuery() {
        if (typeof jQuery !== 'undefined') {
            console.log('WhatsApp Module: jQuery found, initializing');
            jQuery(document).ready(function($) {
                initWhatsAppModule();
            });
        } else if (typeof $ !== 'undefined') {
            console.log('WhatsApp Module: $ found, initializing');
            $(document).ready(function() {
                initWhatsAppModule();
            });
        } else {
            console.log('WhatsApp Module: jQuery not found, retrying in 100ms');
            setTimeout(checkJQuery, 100);
        }
    }

    // Start checking for jQuery
    checkJQuery();
})();