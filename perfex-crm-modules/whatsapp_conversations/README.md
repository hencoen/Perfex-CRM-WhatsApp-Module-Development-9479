# WhatsApp Conversations Module for Perfex CRM

A robust custom module that adds WhatsApp conversation management to customer profiles in Perfex CRM.

## 🚀 Features

- **Customer Integration**: Seamlessly integrates with existing customer profiles
- **Conversation Management**: Add, edit, delete, and view WhatsApp conversations
- **Summary Support**: Each conversation can have a brief summary for quick reference
- **Permission System**: Full permission control (view, create, edit, delete)
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Activity Logging**: All actions are logged in the system activity log
- **Database Compatibility**: Handles different Perfex CRM versions and database configurations

## 📋 Installation

### Method 1: Standard Installation
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

### Method 2: Manual Database Setup (If Installation Fails)

If you encounter database errors during installation, you can manually create the required table:

```sql
-- Replace 'tbl' with your actual database prefix
CREATE TABLE `tblwhatsapp_conversations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `conversation` text NOT NULL,
    `summary` text,
    `date_added` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `customer_id` (`customer_id`),
    KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

## 🎯 Usage

1. **Navigate** to any customer profile
2. **Look for** the "WhatsApp Conversations" tab (appears after Notes tab)
3. **Click** to add, edit, or manage conversations
4. **Add conversations** by clicking "Add New WhatsApp Conversation"
5. **Edit** existing conversations using the edit button
6. **Delete** conversations with proper confirmation

## 🛠️ Troubleshooting

### Common Issues

**Issue**: `Table 'tblpermissions' doesn't exist`
**Solution**: The module now includes fallback mechanisms for different Perfex CRM versions. If this error persists:
1. Check your database prefix in `application/config/database.php`
2. Ensure your Perfex CRM installation is complete
3. Use the manual database setup method above

**Issue**: Permission functions not working
**Solution**: The module includes compatibility checks for different Perfex CRM versions. Older versions will still work with basic functionality.

**Issue**: Module not appearing
**Solution**: 
1. Check file permissions (folders should be 755, files should be 644)
2. Ensure the module folder is in the correct location
3. Clear any caches if your hosting provider uses caching

## 📊 Database Structure

The module creates a `tblwhatsapp_conversations` table with:
- `id`: Primary key
- `customer_id`: Links to customer (foreign key)
- `staff_id`: Staff member who added the conversation
- `conversation`: Full conversation content (TEXT)
- `summary`: Brief summary (TEXT, optional)
- `date_added`: Timestamp of creation

## 🔧 Technical Details

- **Framework**: CodeIgniter 3 (Perfex CRM standard)
- **Architecture**: Follows MVC pattern
- **Security**: CSRF protection, permission-based access
- **Styling**: Matches Perfex CRM design system
- **JavaScript**: jQuery-based with AJAX functionality
- **Compatibility**: Works with Perfex CRM 2.3.0+

## 📁 File Structure

```
modules/whatsapp_conversations/
├── whatsapp_conversations.php     # Main module file
├── install.php                    # Installation script
├── controllers/
│   └── Whatsapp_conversations.php # Controller
├── models/
│   └── Whatsapp_conversations_model.php # Model
├── views/
│   └── customer_tab.php           # Customer tab view
├── language/
│   └── english/
│       └── whatsapp_conversations_lang.php # Language file
├── assets/
│   ├── css/
│   │   └── whatsapp_conversations.css # Styles
│   └── js/
│       └── whatsapp_conversations.js  # JavaScript
└── README.md                      # This file
```

## 🎨 Customization

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

## 🔒 Security Features

- **Permission-based access control**
- **CSRF token protection**
- **Input sanitization**
- **SQL injection prevention**
- **XSS protection**

## 📈 Version History

- **1.0.0**: Initial release with core functionality
- **1.0.1**: Added database compatibility fixes
- **1.0.2**: Enhanced error handling and fallback mechanisms

## 🆘 Support

This module follows Perfex CRM coding standards and best practices. For customizations or issues:

1. Check the troubleshooting section above
2. Verify your Perfex CRM version compatibility
3. Ensure proper file permissions
4. Check server error logs for detailed error messages

## 📄 License

This module is provided as-is for Perfex CRM installations. Modify as needed for your specific requirements.