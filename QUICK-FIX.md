# Quick Fix: Shortcode Not Showing

## Step-by-Step Solution

### Step 1: Test if Shortcodes Work
Add this test shortcode to any page:
```
[dream_tickets_test]
```

If you see a green box saying "Dream Tickets Plugin is Working!", then shortcodes are working and the issue is with the specific shortcode.

### Step 2: Check Plugin is Activated
1. Go to **Plugins** in WordPress admin
2. Find "Dream Online Ticket Selling"
3. Make sure it says **"Activated"** (not "Deactivate")
4. If not activated, click **"Activate"**

### Step 3: Deactivate and Reactivate Plugin
1. Go to **Plugins**
2. Click **"Deactivate"** on Dream Online Ticket Selling
3. Wait 5 seconds
4. Click **"Activate"** again
5. This will recreate database tables if needed

### Step 4: Check if Events Exist
1. Go to **Dream Tickets > Events**
2. If the list is empty, create an event:
   - Click **"Add New"**
   - Fill in event details
   - Set Status to **"Published"**
   - Click **"Save Event"**

### Step 5: Test the Shortcode
On any page, add:
```
[dream_tickets_list]
```

### Step 6: Clear Cache
- Clear browser cache (Ctrl+F5 or Cmd+Shift+R)
- If using a caching plugin, clear its cache
- If using WP Super Cache: **Settings > WP Super Cache > Delete Cache**

### Step 7: Check for Conflicts
1. Temporarily switch to a default theme (Twenty Twenty-Three)
2. Test the shortcode
3. If it works, your theme might be the issue

### Step 8: Check PHP Errors
Add to `wp-config.php` (before "That's all, stop editing!"):
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `/wp-content/debug.log` for errors.

## Common Mistakes

❌ **Wrong:** `[dream-tickets-list]` (with dashes)  
✅ **Correct:** `[dream_tickets_list]` (with underscores)

❌ **Wrong:** `dream_tickets_list` (missing brackets)  
✅ **Correct:** `[dream_tickets_list]`

❌ **Wrong:** Using in a code block or pre-formatted text  
✅ **Correct:** Use in regular page content

## Still Not Working?

1. **Check Browser Console:**
   - Press F12
   - Go to "Console" tab
   - Look for red errors

2. **Check WordPress Version:**
   - Go to **Dashboard > Updates**
   - Make sure WordPress is 5.0 or higher

3. **Check PHP Version:**
   - Go to **Tools > Site Health > Info**
   - PHP should be 7.2 or higher

4. **Contact Support:**
   - Share what you see when using `[dream_tickets_test]`
   - Share any error messages from browser console
   - Share PHP version and WordPress version

