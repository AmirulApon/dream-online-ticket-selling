jQuery(document).ready(function($) {
    
    // Save Event
    $('#dots-event-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'dots_save_event');
        formData.append('nonce', dotsAdmin.nonce);
        
        
        // Get description from editor
        if (typeof tinymce !== 'undefined' && tinymce.get('event_description')) {
            formData.set('description', tinymce.get('event_description').getContent());
        }
        
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(dotsAdmin.strings.saved);
                    if (response.data.event_id) {
                        window.location.href = 'admin.php?page=dream-tickets-events&action=edit&id=' + response.data.event_id;
                    } else {
                        window.location.href = 'admin.php?page=dream-tickets-events';
                    }
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    
    // Delete Event
    $(document).on('click', '.dots-delete-event', function(e) {
        e.preventDefault();
        if (!confirm(dotsAdmin.strings.confirm_delete)) {
            return;
        }
        
        var eventId = $(this).data('event-id');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_delete_event',
                nonce: dotsAdmin.nonce,
                event_id: eventId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    // Duplicate Event
    $(document).on('click', '.dots-duplicate-event', function(e) {
        e.preventDefault();
        var eventId = $(this).data('event-id');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_duplicate_event',
                nonce: dotsAdmin.nonce,
                event_id: eventId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    // Toggle Event Status
    $(document).on('click', '.dots-toggle-status', function(e) {
        e.preventDefault();
        var eventId = $(this).data('event-id');
        var status = $(this).data('status');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_toggle_event_status',
                nonce: dotsAdmin.nonce,
                event_id: eventId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    // Sortable Fields
    if ($.fn.sortable) {
        $('#dots-fields-list').sortable({
            handle: '.dashicons-menu',
            update: function() {
                var orders = {};
                $('#dots-fields-list .dots-field-item').each(function(index) {
                    orders[index] = $(this).data('field-id');
                });
                $.ajax({
                    url: dotsAdmin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'dots_update_field_order',
                        nonce: dotsAdmin.nonce,
                        orders: orders
                    }
                });
            }
        });
    }
    
    // Save Custom Field
    $('#dots-field-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=dots_save_custom_field&nonce=' + dotsAdmin.nonce;
        
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    // Show/hide field options
    $('#field_type, #edit_field_type').on('change', function() {
        var type = $(this).val();
        var $row = $(this).closest('table').find('#field_options_row, #edit_field_options_row');
        if (type === 'select' || type === 'checkbox') {
            $row.show();
        } else {
            $row.hide();
        }
    });
    
    // Edit Field
    $(document).on('click', '.dots-edit-field', function() {
        var fieldId = $(this).data('field-id');
        // Load field data and show modal
        // This would require an AJAX call to get field data
        $('#dots-field-modal').show();
    });
    
    // Delete Field
    $(document).on('click', '.dots-delete-field', function() {
        if (!confirm(dotsAdmin.strings.confirm_delete)) {
            return;
        }
        var fieldId = $(this).data('field-id');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_delete_custom_field',
                nonce: dotsAdmin.nonce,
                field_id: fieldId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
    
    // Close Modal
    $('.dots-modal-close, .dots-modal .dots-modal-close').on('click', function() {
        $('.dots-modal').hide();
    });
    
    // Export Customers
    $('#dots-export-customers').on('click', function(e) {
        e.preventDefault();
        window.location.href = dotsAdmin.ajax_url + '?action=dots_export_customers&nonce=' + dotsAdmin.nonce;
    });
    
    // Settings Tabs
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.dots-tab-content').hide();
        $(target).show();
    });
    
    // Auto-update currency symbol when currency changes
    var currencySymbols = {
        'USD': '$',
        'EUR': '€',
        'GBP': '£',
        'BDT': '৳',
        'INR': '₹',
        'AUD': 'A$',
        'CAD': 'C$',
        'JPY': '¥',
        'CNY': '¥',
        'SGD': 'S$',
        'AED': 'د.إ',
        'SAR': '﷼'
    };
    
    $('#currency').on('change', function() {
        var selectedCurrency = $(this).val();
        var symbol = currencySymbols[selectedCurrency] || '$';
        $('#currency_symbol').val(symbol);
        
        // Show a brief confirmation
        var $symbolField = $('#currency_symbol');
        var originalBg = $symbolField.css('background-color');
        $symbolField.css('background-color', '#d1e7dd');
        setTimeout(function() {
            $symbolField.css('background-color', originalBg);
        }, 1000);
    });
    
    // Media Uploader for Banner
    $('.dots-upload-banner').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var file_frame = wp.media({
            title: 'Select Event Banner',
            button: { text: 'Use this image' },
            multiple: false
        });
        
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#event_banner').val(attachment.url);
            if ($('#event_banner').next('p').length === 0) {
                $('#event_banner').after('<p><img src="' + attachment.url + '" style="max-width: 300px; margin-top: 10px;"></p>');
            } else {
                $('#event_banner').next('p').find('img').attr('src', attachment.url);
            }
        });
        
        file_frame.open();
    });
});

