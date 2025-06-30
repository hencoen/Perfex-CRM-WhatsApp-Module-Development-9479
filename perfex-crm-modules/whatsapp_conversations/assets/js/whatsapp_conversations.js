/**
 * WhatsApp Conversations Module JavaScript
 */

// Wait for jQuery to be available
(function() {
    'use strict';

    // Function to initialize our module
    function initWhatsAppModule() {
        console.log('WhatsApp Module: Initializing JavaScript');

        // Use whichever jQuery reference is available
        var jq = window.jQuery || window.$;
        if (!jq) {
            console.log('WhatsApp Module: jQuery still not available in main script');
            return;
        }

        // Handle edit conversation button click
        jq(document).on('click', '.edit-conversation', function(e) {
            e.preventDefault();
            var conversationId = jq(this).data('conversation-id');
            loadConversationForEdit(conversationId);
        });

        // Handle save conversation edit
        jq(document).on('click', '#save-conversation-edit', function(e) {
            e.preventDefault();
            saveConversationEdit();
        });

        // Auto-expand textarea on focus
        jq(document).on('focus', 'textarea[name="conversation"], textarea[name="summary"]', function() {
            jq(this).animate({
                height: '150px'
            }, 200);
        });

        // Form validation
        jq('#whatsapp-conversation-form').on('submit', function(e) {
            var conversation = jq.trim(jq('#conversation').val());
            if (conversation === '') {
                e.preventDefault();
                alert('Please enter the conversation content.');
                jq('#conversation').focus();
                return false;
            }
        });

        /**
         * Load conversation data for editing
         */
        function loadConversationForEdit(conversationId) {
            var adminUrl = window.admin_url || '/admin/';
            
            jq.get(adminUrl + 'whatsapp_conversations/get_conversation/' + conversationId)
                .done(function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.error) {
                            alert('Error loading conversation: ' + data.error);
                            return;
                        }

                        jq('#edit_summary').val(data.summary || '');
                        jq('#edit_conversation').val(data.conversation || '');
                        jq('#edit-conversation-form').data('conversation-id', conversationId);
                        jq('#edit-conversation-modal').modal('show');
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
            var conversationId = jq('#edit-conversation-form').data('conversation-id');
            var summary = jq.trim(jq('#edit_summary').val());
            var conversation = jq.trim(jq('#edit_conversation').val());

            if (conversation === '') {
                alert('Please enter the conversation content.');
                jq('#edit_conversation').focus();
                return;
            }

            // Show loading state
            var saveBtn = jq('#save-conversation-edit');
            var originalText = saveBtn.html();
            saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            // Prepare data object
            var postData = {
                summary: summary,
                conversation: conversation
            };

            // Add CSRF token if available
            if (typeof csrfData !== 'undefined') {
                postData[csrfData.token_name] = csrfData.hash;
            }

            var adminUrl = window.admin_url || '/admin/';
            
            jq.post(adminUrl + 'whatsapp_conversations/edit/' + conversationId, postData)
                .done(function(response) {
                    // Update CSRF token if provided
                    if (typeof(csrfData) !== 'undefined' && response.csrf_hash) {
                        csrfData.hash = response.csrf_hash;
                    }

                    jq('#edit-conversation-modal').modal('hide');
                    // Reload the page to show updated data
                    window.location.reload();
                })
                .fail(function(xhr, status, error) {
                    alert('Error saving conversation: ' + error);
                    console.error('Save error:', status, error);
                })
                .always(function() {
                    // Restore button state
                    saveBtn.html(originalText).prop('disabled', false);
                });
        }

        /**
         * Confirm delete with better UX
         */
        jq(document).on('click', '.delete-conversation', function(e) {
            e.preventDefault();
            var href = jq(this).attr('href');
            if (confirm('Are you sure you want to delete this WhatsApp conversation? This action cannot be undone.')) {
                window.location.href = href;
            }
        });

        /**
         * Auto-save form data to localStorage (optional feature)
         */
        var formData = {};
        jq('#conversation, #summary').on('input', function() {
            var fieldName = jq(this).attr('name');
            var fieldValue = jq(this).val();
            formData[fieldName] = fieldValue;
            localStorage.setItem('whatsapp_conversation_draft', JSON.stringify(formData));
        });

        // Restore draft data on page load
        var savedDraft = localStorage.getItem('whatsapp_conversation_draft');
        if (savedDraft) {
            try {
                var draftData = JSON.parse(savedDraft);
                if (draftData.conversation && jq('#conversation').val() === '') {
                    jq('#conversation').val(draftData.conversation);
                }
                if (draftData.summary && jq('#summary').val() === '') {
                    jq('#summary').val(draftData.summary);
                }
            } catch (e) {
                // Clear invalid draft data
                localStorage.removeItem('whatsapp_conversation_draft');
            }
        }

        // Clear draft after successful form submission
        jq('#whatsapp-conversation-form').on('submit', function() {
            localStorage.removeItem('whatsapp_conversation_draft');
        });

        console.log('WhatsApp Module: JavaScript initialization complete');
    }

    // Check if jQuery is available and initialize
    function checkjQuery() {
        if (window.jQuery || window.$) {
            console.log('WhatsApp Module: jQuery found, initializing main script');
            var jq = window.jQuery || window.$;
            jq(document).ready(function() {
                initWhatsAppModule();
            });
        } else {
            console.log('WhatsApp Module: jQuery not found in main script, retrying in 100ms');
            setTimeout(checkjQuery, 100);
        }
    }

    // Start checking for jQuery
    checkjQuery();
})();