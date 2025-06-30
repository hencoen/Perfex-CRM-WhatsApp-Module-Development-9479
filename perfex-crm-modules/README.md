# Perfex CRM Modules

This directory contains custom modules for Perfex CRM.

## WhatsApp Conversations Module

A professional module for managing WhatsApp conversations within customer profiles.

### Installation Instructions

1. **Upload Module**: Copy the entire `whatsapp_conversations` folder to your Perfex CRM installation at:
   ```
   /path/to/perfex-crm/modules/whatsapp_conversations/
   ```

2. **Install via Admin Panel**:
   - Login to your Perfex CRM admin panel
   - Navigate to **Setup → Modules**
   - Find "WhatsApp Conversations" in the list
   - Click **Install**

3. **Configure Permissions**:
   - Go to **Setup → Staff → Roles**
   - Edit the roles that should have access
   - Configure permissions:
     - **View**: Can see WhatsApp conversations
     - **Create**: Can add new conversations
     - **Edit**: Can modify existing conversations
     - **Delete**: Can remove conversations

### Usage

1. Navigate to any customer profile
2. Look for the "WhatsApp Conversations" tab (appears after Notes)
3. Click to add, edit, or manage conversations

### Features

- ✅ Seamless integration with customer profiles
- ✅ Summary + full conversation content
- ✅ Permission-based access control
- ✅ Responsive design
- ✅ AJAX-powered editing
- ✅ Activity logging
- ✅ Auto-save drafts

### Technical Details

- **Framework**: CodeIgniter 3
- **Database**: Creates `tblwhatsapp_conversations` table
- **Security**: CSRF protection, permission validation
- **Styling**: Matches Perfex CRM design system

For support or customizations, refer to the module documentation.