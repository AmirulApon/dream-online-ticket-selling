# Dream Online Ticket Selling

Author URI: https://profiles.wordpress.org/dreamscarnival/ 
Plugin URI: https://wordpress.org/plugins/dream-online-ticket-selling/
Author: Dream Carnival
Contributors: dreamscarnival
Tags: online ticket selling, Ticket, Compitition, Dream, ticket selling
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
Requires at least:** 6.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A comprehensive WordPress plugin for selling event tickets online with full admin control and customer management.


## Features

### Admin Panel Features

- **Dashboard**: Overview of total sales, upcoming events, and recent ticket purchases with shortcode display
- **Event Management**: Create, edit, duplicate, and publish/unpublish events
  - Add Event Type (Concert, Conference, Workshop, etc.)
  - Set ticket price and available stock directly
  - View Event ID for shortcode usage
  - Display price and stock in events list
- **Promo Codes**: Create and manage discount codes
  - Percentage or fixed amount discounts
  - Set usage limits and validity dates
  - Track usage statistics
- **Custom Form Fields**: Drag-and-drop form builder with various field types
  - Auto-generates default fields (name, email, phone, address)
- **Customer Management**: View all customers, search, filter, and export data
- **Sales Reports**: Track revenue, tickets sold, payment statuses, and Event Type
- **Settings**: Configure currency (with auto-updating symbol), timezone, email notifications, and payment gateways

### Frontend Features

- **Event Listings**: Display upcoming events with filters
- **Single Event Page**: Detailed event information with ticket purchase form
- **Dynamic Ticket Forms**: Forms generated based on admin-defined fields (auto-generates: name, email, phone, address)
- **Real-time Price Calculation**: Automatic price updates based on quantity and promo codes
- **Order Confirmation**: QR code generation for event check-in
- **Promo Code Support**: Apply discount codes (percentage or fixed amount) during purchase
- **QR Code Tickets**: Scan QR codes to view ticket details or download PDF
- **Event Type Categorization**: Organize events by type (Concert, Conference, Workshop, etc.)

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

- `wp_dots_events` - Event information (with event_type, ticket_price, tickets_available)
- `wp_dots_ticket_categories` - Ticket types and pricing (legacy, optional)
- `wp_dots_custom_fields` - Custom form fields
- `wp_dots_customers` - Customer information
- `wp_dots_promo_codes` - Promo codes and discounts
- `wp_dots_sales` - Purchase records

## Settings

### General Settings

- **Currency**: Select from USD, EUR, GBP, BDT, INR, AUD, CAD, JPY, CNY, SGD, AED, SAR
- **Currency Symbol**: Auto-updates based on currency selection, can be customized
- **Timezone**: Set your timezone
- **Max Tickets per Customer**: Limit ticket purchases

### Email Notifications

- Enable/disable email notifications
- Configure admin email for alerts

### Security

- Enable CAPTCHA for forms (requires additional setup)

## Payment Integration

The plugin supports multiple payment gateways:

### PayPal
- Integration via PayPal API
- Requires Client ID and Secret
- Configure in **Settings > Payment**

### Stripe
- Credit/Debit card payments via Stripe.js
- Requires Publishable Key and Secret Key
- Configure in **Settings > Payment**

### SSLCommerz
- Popular payment gateway for Bangladesh and South Asia
- Requires Store ID and Store Password
- Supports both Sandbox and Live modes
- Test via **Dream Tickets > Test SSLCommerz**

### Bank Transfer
- Manual payment processing
- Orders marked as "Pending" until manual confirmation
- Tickets not deducted until payment confirmed

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

== External services ==

This plugin relies on several third-party external services to provide its core functionalities (payment processing and QR code generation).

1. **PayPal**
It connects to the PayPal API (`api-m.paypal.com` / `api-m.sandbox.paypal.com`) to process online ticket transactions. When a user selects PayPal as the payment method, order details and amount are sent to PayPal.
This service is provided by PayPal Inc: [Terms of Use](https://www.paypal.com/us/webapps/mpp/ua/legalhub-full), [Privacy Policy](https://www.paypal.com/us/webapps/mpp/ua/privacy-full).

2. **Stripe**
It loads Stripe.js (`https://js.stripe.com/v3/`) and connects to the Stripe API (`api.stripe.com`) to securely process credit and debit card payments. Order details are transmitted during checkout.
This service is provided by Stripe Inc: [Terms of Service](https://stripe.com/legal/end-users), [Privacy Policy](https://stripe.com/privacy).

3. **SSLCommerz**
It connects to the SSLCommerz API (`securepay.sslcommerz.com` / `sandbox.sslcommerz.com`) to process payments primarily in South Asia. Order and basic customer details are sent to facilitate the transaction.
This service is provided by SSLCommerz: [Terms and Conditions](https://sslcommerz.com/terms-conditions/), [Privacy Policy](https://sslcommerz.com/privacy-policy/).

4. **QR Server**
It uses the free API provided by goqr.me (`api.qrserver.com`) to dynamically generate QR codes for order confirmations and event check-ins. The generated QR code encodes the order number.
This service is provided by Egoditor GmbH: [Terms of Service](https://goqr.me/api/doc/), [Privacy Policy](https://goqr.me/privacy-policy/).

## Changelog

### Version 1.0.0
- Initial release
- Event management with Event Type categorization
- Direct ticket price and stock management (no categories needed)
- Ticket form builder with auto-generated fields
- Customer management
- Sales tracking with Event Type display
- Order confirmation with QR codes
- QR code scanning to view ticket details/PDF
- Promo code management system
- Multiple payment gateway support (PayPal, Stripe, SSLCommerz, Bank Transfer)
- Auto-updating currency symbols
- Shortcode management in dashboard
- Event ID display for easy shortcode usage

## License

GPL v2 or later

## Author

**Dream Carnival**

Plugin developed by Dream Carnival. For support and updates, visit the plugin page.

## Translation

The plugin uses the text domain `dream-online-ticket-selling` for translations. Translation files should be placed in:

```
/wp-content/plugins/dream-online-ticket-selling/languages/
```

To create translations:
1. Use tools like Poedit to generate `.po` and `.mo` files
2. Name files as: `dream-online-ticket-selling-{locale}.po` (e.g., `dream-online-ticket-selling-es_ES.po` for Spanish)
3. Place both `.po` and `.mo` files in the `languages` directory

