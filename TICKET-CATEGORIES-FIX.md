# Fix: Ticket Categories Not Showing

## Issue
Ticket categories, prices, and availability are not displaying on the frontend.

## Common Causes

### 1. No Categories Created
**Problem:** You haven't added ticket categories to your event yet.

**Solution:**
1. Go to **Dream Tickets > Events** in WordPress admin
2. Click **Edit** on your event
3. Scroll down to **Ticket Categories** section
4. Click **Add Category**
5. Fill in:
   - Category Name (e.g., "General Admission", "VIP")
   - Price (e.g., 25.00)
   - Availability (e.g., 100)
   - Max per Customer (e.g., 10)
6. Click **Save Event**

### 2. Event Not Published
**Problem:** Event status is set to "Draft" instead of "Published".

**Solution:**
1. Go to **Dream Tickets > Events**
2. Edit your event
3. Change **Status** from "Draft" to "Published"
4. Save the event

### 3. Database Tables Not Created
**Problem:** Database tables weren't created during plugin activation.

**Solution:**
1. Go to **Plugins** in WordPress admin
2. **Deactivate** "Dream Online Ticket Selling"
3. Wait 5 seconds
4. **Activate** it again
5. This will recreate the database tables

### 4. Variable Scope Issue
**Problem:** Categories variable not being passed to the view.

**Solution:** Already fixed in the latest update. Make sure you have the latest version.

## How to Verify Categories Are Working

### Step 1: Check Admin
1. Go to **Dream Tickets > Events**
2. Edit an event
3. Check if ticket categories are listed in the **Ticket Categories** section
4. If empty, add at least one category

### Step 2: Check Frontend
1. Visit your event page
2. You should see ticket option cards with:
   - Ticket name
   - Price (large, prominent)
   - Availability count
3. If you see "No ticket categories available" message, add categories in admin

### Step 3: Test Purchase Form
1. Select a ticket type (radio button)
2. You should see:
   - Selected ticket info box appears
   - Price per ticket displayed
   - Tickets available count
3. Change quantity - price should update automatically

## Quick Test

Add this to any page to test:
```
[dream_ticket_form event_id="1"]
```
Replace `1` with your actual event ID.

## Still Not Working?

1. **Check Browser Console:**
   - Press F12
   - Go to Console tab
   - Look for JavaScript errors

2. **Check PHP Errors:**
   - Enable WordPress debug mode
   - Check `/wp-content/debug.log`

3. **Verify Event ID:**
   - Make sure you're using the correct event ID
   - Check in admin: Events list shows event IDs

4. **Clear Cache:**
   - Clear browser cache
   - Clear WordPress cache if using caching plugin

## Expected Behavior

When working correctly, users should see:

1. **Ticket Options as Cards:**
   - Each ticket type as a selectable card
   - Large price display
   - Availability count with icon
   - "Sold Out" indicator if no tickets available

2. **Selected Ticket Info:**
   - Shows when a ticket is selected
   - Displays ticket name, price, and availability

3. **Real-time Updates:**
   - Price updates when quantity changes
   - Availability updates when ticket type changes

