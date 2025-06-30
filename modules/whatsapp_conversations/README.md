# WhatsApp Conversations Module for Perfex CRM

A custom module that adds WhatsApp conversation management to customer profiles in Perfex CRM.

## Features

- **Customer Integration**: Seamlessly integrates with existing customer profiles
- **Conversation Management**: Add, edit, delete, and view WhatsApp conversations
- **Summary Support**: Each conversation can have a brief summary for quick reference
- **Permission System**: Full permission control (view, create, edit, delete)
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Activity Logging**: All actions are logged in the system activity log

## Installation

1. Upload the `whatsapp_conversations` folder to your Perfex CRM `modules` directory
2. Go to **Setup > Modules** in your admin panel
3. Find "WhatsApp Conversations" and click **Install**
4. Configure permissions under **Setup > Staff > Roles**

## Usage

### Adding Conversations
1. Navigate to any customer profile
2. Click on the "WhatsApp Conversations" tab (located below "Notes")
3. Click "Add New WhatsApp Conversation"
4. Fill in the conversation summary (optional) and full conversation content
5. Click Save

### Managing Conversations
- **View**: All conversations are displayed in chronological order (newest first)
- **Edit**: Click the edit button on any conversation (requires edit permission)
- **Delete**: Click the delete button with confirmation (requires delete permission)

### Permissions
Configure these permissions under **Setup > Staff > Roles**:
- **View**: Can see WhatsApp conversations
- **Create**: Can add new conversations
- **Edit**: Can modify existing conversations  
- **Delete**: Can remove conversations

## Database Structure

The module creates a `tblwhatsapp_conversations` table with:
- `id`: Primary key
- `customer_id`: Links to customer
- `staff_id`: Staff member who added the conversation
- `conversation`: Full conversation content
- `summary`: Brief summary (optional)
- `date_added`: Timestamp

## Technical Details

- **Framework**: CodeIgniter 3 (Perfex CRM standard)
- **Architecture**: Follows MVC pattern
- **Security**: CSRF protection, permission-based access
- **Styling**: Matches Perfex CRM design system
- **JavaScript**: jQuery-based with AJAX functionality

## File Structure

```
modules/whatsapp_conversations/
├── whatsapp_conversations.php          # Main module file
├── install.php                         # Installation script
├── controllers/
│   └── Whatsapp_conversations.php      # Controller
├── models/
│   └── Whatsapp_conversations_model.php # Model
├── views/
│   └── customer_tab.php                # Customer tab view
├── language/
│   └── english/
│       └── whatsapp_conversations_lang.php # Language file
├── assets/
│   ├── css/
│   │   └── whatsapp_conversations.css  # Styles
│   └── js/
│       └── whatsapp_conversations.js   # JavaScript
└── README.md                           # This file
```

## Customization

### Adding New Languages
1. Create a new language file in `language/[language_code]/`
2. Copy the structure from the English file
3. Translate all language strings

### Styling Changes
Modify `assets/css/whatsapp_conversations.css` to customize the appearance.

### Additional Fields
To add more fields:
1. Update the database schema in `install.php`
2. Modify the model methods
3. Update the view forms
4. Add corresponding language strings

## Support

This module follows Perfex CRM coding standards and best practices. For customizations or issues, refer to the Perfex CRM documentation or contact your developer.

## Version History

- **1.0.0**: Initial release with core functionality