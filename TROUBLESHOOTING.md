# Troubleshooting: Shortcode Not Showing

## Quick Fixes

### 1. Check Plugin Activation
- Go to **Plugins** in WordPress admin
- Make sure "Dream Online Ticket Selling" is **Activated**
- If not, click "Activate"

### 2. Clear Cache
- Clear your browser cache
- If using a caching plugin (WP Super Cache, W3 Total Cache, etc.), clear that cache
- If using a CDN, clear CDN cache

### 3. Check for PHP Errors
- Enable WordPress debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
- Check `/wp-content/debug.log` for errors

### 4. Verify Shortcode Syntax
Make sure you're using the correct shortcode:
```
[dream_tickets_list]
```
NOT:
- `[dream-tickets-list]` (wrong - uses dashes)
- `dream_tickets_list` (missing brackets)

### 5. Test in Text Widget
1. Go to **Appearance > Widgets**
2. Add a **Text** widget
3. Add: `[dream_tickets_list]`
4. Save and check frontend

### 6. Check if Events Exist
- Go to **Dream Tickets > Events**
- Make sure you have at least one event created
- Make sure the event status is set to **"Published"**

### 7. Deactivate Other Plugins
- Temporarily deactivate other plugins
- Check if shortcode works
- If it works, reactivate plugins one by one to find the conflict

### 8. Switch Theme Temporarily
- Switch to a default WordPress theme (Twenty Twenty-Three)
- Check if shortcode works
- If it works, your theme might be interfering

### 9. Check Database Tables
- Go to **Dream Tickets > Dashboard**
- If you see errors, the database tables might not be created
- Deactivate and reactivate the plugin to recreate tables

### 10. Manual Shortcode Test
Add this to your theme's `functions.php` temporarily to test:
```php
add_shortcode('test_dream', function() {
    return 'Shortcode is working!';
});
```
Then use `[test_dream]` on a page. If this works, the issue is with the plugin shortcode.

## Common Issues

### Issue: "Event ID is required" message
**Solution:** Make sure you're using:
```
[dream_ticket_form event_id="1"]
```
Replace `1` with your actual event ID.

### Issue: "No events found"
**Solution:**
1. Create an event in **Dream Tickets > Events**
2. Set status to **Published**
3. Make sure event date is set

### Issue: Shortcode shows but nothing displays
**Solution:**
1. Check that events exist and are published
2. Check browser console for JavaScript errors (F12)
3. Check if CSS is loading properly

### Issue: Plugin activated but menu not showing
**Solution:**
1. Check user permissions - you need `manage_options` capability
2. Try logging out and back in
3. Check if another plugin is hiding the menu

## Still Not Working?

1. **Check Error Logs:**
   - WordPress debug log: `/wp-content/debug.log`
   - Server error log (check with your hosting provider)

2. **Test with Default Content:**
   Create a simple test page with just:
   ```
   [dream_tickets_list]
   ```

3. **Check PHP Version:**
   - Plugin requires PHP 7.2+
   - Check in **Tools > Site Health > Info**

4. **Verify File Permissions:**
   - Plugin files should be readable
   - Check `/wp-content/plugins/dream-online-ticket-selling/` folder permissions

## Getting Help

If none of the above works, check:
1. WordPress version (should be 5.0+)
2. PHP version (should be 7.2+)
3. Any error messages in browser console (F12)
4. Any error messages in WordPress debug log

