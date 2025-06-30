# WhatsApp Conversations Module for Perfex CRM

A robust custom module that adds WhatsApp conversation management to customer profiles in Perfex CRM.

## 🚨 **TROUBLESHOOTING GUIDE**

If the WhatsApp tab is not appearing in customer profiles, follow these steps:

### **Step 1: Check Module Installation**
1. Go to **Setup → Modules** in admin panel
2. Find "WhatsApp Conversations" 
3. If not installed, click **Install**
4. If already installed, try **Uninstall** then **Install** again

### **Step 2: Check Permissions**
1. Go to **Setup → Staff → Roles**
2. Edit your role (or Admin role)
3. Find "WhatsApp Conversations" permissions
4. Enable at least **View** permission
5. Save changes

### **Step 3: Check Debug Log**
1. Look for `whatsapp_debug.log` in your Perfex CRM root directory
2. You should see these entries when visiting a customer page:
   ```
   Tab function called for customer: [ID]
   Tab content function called for customer: [ID]
   ```
3. If these are missing, the hooks aren't being triggered

### **Step 4: Test Direct Access**
1. Access: `your-site.com/modules/whatsapp_conversations/test_direct.php?customer_id=1`
2. This will test if the module files are accessible
3. Check browser console for JavaScript messages

### **Step 5: Browser Console Check**
1. Open customer profile page
2. Press F12 → Console tab  
3. Look for these messages:
   - `WhatsApp Module: Force adding tab via JavaScript`
   - `WhatsApp Module: Tab added for customer: [ID]`
   - `WhatsApp Module: Fallback tab successfully added`

### **Step 6: Manual Tab Addition (Fallback)**
If hooks aren't working, the module now includes JavaScript fallback that:
- Automatically detects tab containers
- Adds the WhatsApp tab via JavaScript
- Shows a placeholder message

## 🔧 **Enhanced Features (v1.0.5)**

- **Multiple Hook Registration**: Tries all possible hook names for different Perfex versions
- **JavaScript Fallback**: Automatically adds tab if PHP hooks fail
- **Enhanced Debugging**: Comprehensive logging of all operations
- **Permission Fallback**: Allows admin access even if permissions aren't set
- **URI Detection**: Better detection of customer pages
- **Direct Testing**: Includes test file for troubleshooting

## 📋 **Installation**

1. **Upload Module**: Copy `whatsapp_conversations` folder to `/modules/`
2. **Install**: Setup → Modules → Install "WhatsApp Conversations"
3. **Set Permissions**: Setup → Staff → Roles → Enable WhatsApp permissions
4. **Test**: Visit any customer profile page

## 🎯 **Expected Behavior**

When working correctly, you should see:
- New "WhatsApp Conversations" tab in customer profiles
- Tab appears after existing tabs (Notes, Files, etc.)
- Click tab to add/view/edit conversations
- Full CRUD operations with proper permissions

## 🔍 **Still Not Working?**

1. **Check PHP Error Logs**: Look for any PHP errors in server logs
2. **Check File Permissions**: Ensure module files are readable (644/755)
3. **Check Database**: Verify `tblwhatsapp_conversations` table exists
4. **Check Perfex Version**: Module requires Perfex CRM 2.3.0+
5. **Contact Support**: Provide debug log and browser console output

## 📊 **Debug Information**

The module logs to `whatsapp_debug.log` in your Perfex root:
- Module loading status
- Hook registration attempts
- Customer page detection
- Permission checks
- Tab rendering attempts

Check this file for detailed troubleshooting information.

## 🚀 **Version History**

- **1.0.5**: Added JavaScript fallback, enhanced debugging, multiple hook registration
- **1.0.4**: Fixed jQuery loading issues
- **1.0.3**: Enhanced error handling
- **1.0.2**: Database compatibility fixes
- **1.0.1**: Initial release

---

**If you can see the WhatsApp tab now, the module is working correctly!**