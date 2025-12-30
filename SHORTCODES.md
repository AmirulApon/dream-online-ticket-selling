# Dream Online Ticket Selling - Shortcodes Guide

## Available Shortcodes

### 1. Display Events List

Display a list of all published events on any page or post.

**Basic Usage:**
```
[dream_tickets_list]
```

**With Options:**
```
[dream_tickets_list limit="5" status="published"]
```

**Parameters:**
- `limit` (optional) - Number of events to display. Default: -1 (all events)
- `status` (optional) - Event status to display. Options: `published`, `draft`. Default: `published`

**Examples:**
```
// Display all published events
[dream_tickets_list]

// Display only 5 upcoming events
[dream_tickets_list limit="5"]

// Display all events including drafts (admin only)
[dream_tickets_list status="draft"]
```

---

### 2. Display Ticket Purchase Form

Display a ticket purchase form for a specific event.

**Basic Usage:**
```
[dream_ticket_form event_id="1"]
```

**Parameters:**
- `event_id` (required) - The ID of the event you want to display the form for

**Example:**
```
[dream_ticket_form event_id="1"]
```

**How to find Event ID:**
1. Go to **Dream Tickets > Events** in WordPress admin
2. Hover over an event name
3. Look at the URL - it will show `id=1` or similar
4. Use that number as the `event_id`

---

## How to Use Shortcodes

### Method 1: In Page/Post Editor

1. Edit any page or post
2. Add the shortcode directly in the content area
3. Publish or update the page

### Method 2: In Theme Templates

Add shortcodes in your theme PHP files:

```php
<?php echo do_shortcode('[dream_tickets_list]'); ?>
```

### Method 3: In Widgets

1. Go to **Appearance > Widgets**
2. Add a **Text** or **Shortcode** widget
3. Paste the shortcode
4. Save

---

## Complete Examples

### Example 1: Events Page

Create a page called "Events" and add:

```
<h2>Upcoming Events</h2>
[dream_tickets_list limit="10"]
```

### Example 2: Single Event Page with Form

Create a page for a specific event:

```
<h1>Concert Tickets</h1>
<p>Purchase your tickets below:</p>
[dream_ticket_form event_id="1"]
```

### Example 3: Featured Events Section

```
<h2>Featured Events</h2>
[dream_tickets_list limit="3"]
```

---

## Direct Event URLs

Events are also accessible via direct URLs:

- **Single Event Page:** `yoursite.com/dream-tickets/event/1`
- **Order Confirmation:** `yoursite.com/dream-tickets/order/DOTS-1234567890-1234`

Replace `1` with your event ID and the order number with the actual order number.

---

## Troubleshooting

**Shortcode not displaying:**
- Make sure the plugin is activated
- Check that events are set to "Published" status
- Verify the event ID is correct

**Form not showing:**
- Ensure the event ID exists
- Check that the event status is "Published"
- Make sure ticket categories are added to the event

**Events list is empty:**
- Create at least one event
- Set event status to "Published"
- Check that event date is in the future (if filtering by date)

