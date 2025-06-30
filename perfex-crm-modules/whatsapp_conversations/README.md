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
- **Debug Logging**: Comprehensive logging for troubleshooting

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

## 🔍 Troubleshooting

### If the tab doesn't appear:

1. **Check Module Installation**:
   - Go to **Setup → Modules**
   - Ensure "WhatsApp Conversations" shows as "Installed"
   - If not, reinstall the module

2. **Check Debug Logs**:
   - Look in your Perfex CRM logs directory (usually `application/logs/`)
   - Check for entries containing "WhatsApp Module Debug" or "WhatsApp Install Debug"
   - This will show you exactly what's happening

3. **Check File Permissions**:
   - Ensure all module files have proper permissions
   - Folders: 755, Files: 644

4. **Check Browser Console**:
   - Open browser developer tools
   - Check console for JavaScript errors or debug messages
   - Look for "WhatsApp tab added" or "WhatsApp tab content loaded" messages

5. **Manual Permission Check**:
   ```sql
   -- Check if permissions exist (replace 'tbl' with your prefix)
   SELECT * FROM tblpermissions WHERE name = 'whatsapp_conversations';
   
   -- Check if table exists
   SHOW TABLES LIKE '%whatsapp_conversations%';
   ```

6. **Clear Cache**:
   - Clear any server-side caches
   - Clear browser cache
   - Restart web server if possible

### Debug Information

The module now includes comprehensive debug logging. Check your Perfex CRM logs for:

- `WhatsApp Module Debug:` - General module operations
- `WhatsApp Install Debug:` - Installation process
- Module loading, hook registration, permission checks, etc.

### Common Issues

**Issue**: Tab not appearing
**Solution**: Check debug logs, verify module is installed, check permissions

**Issue**: Permission errors
**Solution**: The module includes fallback mechanisms - check if you're logged in as admin

**Issue**: Database errors
**Solution**: Check table creation logs, verify database permissions

## 🎯 Usage

1. **Navigate** to any customer profile
2. **Look for** the "WhatsApp Conversations" tab (appears after other tabs)
3. **Click** to add, edit, or manage conversations
4. **Add conversations** by clicking "Add New WhatsApp Conversation"
5. **Edit** existing conversations using the edit button
6. **Delete** conversations with proper confirmation

## 📊 Database Structure

The module creates a `[prefix]whatsapp_conversations` table with:

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
- **Debug Logging**: Comprehensive logging for troubleshooting

## 📈 Version History

- **1.0.0**: Initial release with core functionality
- **1.0.1**: Added database compatibility fixes
- **1.0.2**: Enhanced error handling, fallback mechanisms, and comprehensive debug logging

## 🆘 Support

This module follows Perfex CRM coding standards and best practices. For troubleshooting:

1. Check the debug logs in your Perfex CRM logs directory
2. Verify your Perfex CRM version compatibility
3. Ensure proper file permissions
4. Check server error logs for detailed error messages
5. Use browser developer tools to check for JavaScript errors

The module includes extensive debug logging to help identify any issues during installation or operation.