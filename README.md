# Dream Online Ticket Selling

A comprehensive WordPress plugin for selling event tickets online with full admin control and customer management.

## Features

### Admin Panel Features

- **Dashboard**: Overview of total sales, upcoming events, and recent ticket purchases
- **Event Management**: Create, edit, duplicate, and publish/unpublish events
- **Ticket Categories**: Multiple ticket types with different prices and availability
- **Custom Form Fields**: Drag-and-drop form builder with various field types
- **Customer Management**: View all customers, search, filter, and export data
- **Sales Reports**: Track revenue, tickets sold, and payment statuses
- **Settings**: Configure currency, timezone, email notifications, and security

### Frontend Features

- **Event Listings**: Display upcoming events with filters
- **Single Event Page**: Detailed event information with ticket purchase form
- **Dynamic Ticket Forms**: Forms generated based on admin-defined fields
- **Real-time Price Calculation**: Automatic price updates based on selections
- **Order Confirmation**: QR code generation for event check-in
- **Promo Code Support**: Apply discount codes during purchase

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Dream Tickets** in the admin menu to configure settings
4. Create your first event and customize the ticket form

## Quick Start

### Creating an Event

1. Navigate to **Dream Tickets > Events**
2. Click **Add New**
3. Fill in event details (name, description, date, time, location)
4. Add ticket categories with prices and availability
5. Set status to **Published** to make it visible on the frontend
6. Click **Save Event**

### Customizing Ticket Forms

1. Go to **Dream Tickets > Ticket Forms**
2. Click **Add New Field** in the sidebar
3. Configure field name, label, type, and whether it's required
4. Drag and drop fields to reorder them
5. Fields will automatically appear in the ticket purchase form

### Displaying Events on Frontend

Use the shortcode to display events:

```
[dream_tickets_list]
```

Or display a ticket form for a specific event:

```
[dream_ticket_form event_id="1"]
```

Events are also accessible via:
- `/dream-tickets/event/{event_id}` - Single event page
- `/dream-tickets/order/{order_number}` - Order confirmation page

## Database Structure

The plugin creates the following database tables:

- `wp_dots_events` - Event information
- `wp_dots_ticket_categories` - Ticket types and pricing
- `wp_dots_custom_fields` - Custom form fields
- `wp_dots_customers` - Customer information
- `wp_dots_sales` - Purchase records

## Settings

### General Settings

- **Currency**: Select from USD, EUR, GBP, BDT
- **Currency Symbol**: Customize the currency symbol
- **Timezone**: Set your timezone
- **Max Tickets per Customer**: Limit ticket purchases

### Email Notifications

- Enable/disable email notifications
- Configure admin email for alerts

### Security

- Enable CAPTCHA for forms (requires additional setup)

## Payment Integration

Payment gateway integration is prepared for PayPal and Stripe. Full implementation requires:

1. Payment gateway API credentials
2. SDK installation
3. Configuration in settings

Currently, the plugin processes test payments. Update the `DOTS_Payment` class to integrate with your preferred payment gateway.

## Customization

### Styling

Customize the appearance by overriding CSS classes:

- `.dots-event-card` - Event listing cards
- `.dots-ticket-form` - Purchase form
- `.dots-order-confirmation` - Confirmation page

### Hooks and Filters

The plugin uses standard WordPress hooks. Key actions:

- `dots_before_event_save` - Before saving event
- `dots_after_purchase` - After successful purchase
- `dots_before_form_display` - Before displaying ticket form

## Troubleshooting

### Events Not Displaying

- Ensure events are set to **Published** status
- Check that the event date is in the future
- Verify shortcode is correctly placed

### Payment Not Processing

- Check payment gateway configuration
- Verify API credentials are correct
- Review server error logs

### Form Fields Not Showing

- Ensure custom fields are created in **Ticket Forms**
- Check field order is set correctly
- Verify form is being loaded on the correct page

## Support

For issues and questions:

1. Check the WordPress admin for error messages
2. Review server error logs
3. Ensure all plugin files are uploaded correctly
4. Verify WordPress and PHP versions meet requirements

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Changelog

### Version 1.0.0
- Initial release
- Event management
- Ticket form builder
- Customer management
- Sales tracking
- Order confirmation with QR codes

## License

GPL v2 or later

## Author

Your Name

