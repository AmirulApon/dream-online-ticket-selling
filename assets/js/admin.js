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
    var activeTab = localStorage.getItem('dots_active_tab');
    if (activeTab && $(activeTab).length) {
        $('.nav-tab').removeClass('nav-tab-active');
        $('a[href="' + activeTab + '"]').addClass('nav-tab-active');
        $('.dots-tab-content').hide();
        $(activeTab).show();
    }

    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        localStorage.setItem('dots_active_tab', target);
        
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
    
    // Copy Shortcode in Dashboard
    $('.dots-copy-shortcode').on('click', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes-alt" style="vertical-align: middle; color: #00a32a;"></span> ' + dotsAdmin.strings.copied);
        setTimeout(function() {
            $btn.html(originalText);
        }, 2000);
    });

    // Copy Shortcode in Events List
    $('.dots-copy-event-id').on('click', function(e) {
        e.preventDefault();
        var eventId = $(this).data('event-id');
        var shortcode = '[dream_ticket_form event_id="' + eventId + '"]';
        
        // Copy to clipboard
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes-alt" style="font-size: 14px; width: 14px; height: 14px; color: #00a32a;"></span>');
        $btn.css('color', '#00a32a');
        
        // Show tooltip
        var $tooltip = $('<div style="position: absolute; background: #1d2327; color: #fff; padding: 8px 12px; border-radius: 4px; font-size: 12px; z-index: 10000; margin-top: 30px; white-space: nowrap;">' + dotsAdmin.strings.shortcode_copied + '</div>');
        $btn.after($tooltip);
        
        setTimeout(function() {
            $btn.html(originalHtml);
            $btn.css('color', '');
            $tooltip.fadeOut(300, function() {
                $(this).remove();
            });
        }, 2000);
    });

    // Copy Event ID in Event Edit
    $('.dots-copy-event-id-edit').on('click', function(e) {
        e.preventDefault();
        var eventId = $(this).data('event-id');
        var shortcode = '[dream_ticket_form event_id="' + eventId + '"]';
        
        // Copy to clipboard
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes-alt" style="vertical-align: middle; color: #00a32a;"></span> ' + dotsAdmin.strings.copied);
        $btn.css('color', '#00a32a');
        
        setTimeout(function() {
            $btn.html(originalText);
            $btn.css('color', '');
        }, 2000);
    });

    // Delete Promo Code
    $('.dots-delete-promo').on('click', function(e) {
        e.preventDefault();
        if (!confirm(dotsAdmin.strings.confirm_delete_promo)) {
            return;
        }
        
        var promoId = $(this).data('promo-id');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_delete_promo_code',
                nonce: dotsAdmin.nonce,
                promo_id: promoId
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

    // Auto-uppercase promo code
    $('#promo_code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Update discount value label when type changes
    $('#discount_type').on('change', function() {
        var type = $(this).val();
        var $label = $('#discount_type_label');
        var $description = $('#discount_description');
        
        if (type === 'percentage') {
            $label.text('%');
            $description.text(dotsAdmin.strings.discount_percentage_desc);
        } else {
            $label.text(dotsAdmin.currency_symbol);
            $description.text(dotsAdmin.strings.discount_fixed_desc);
        }
    });
    
    // Save promo code
    $('#dots-promo-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=dots_save_promo_code&nonce=' + dotsAdmin.nonce;
        
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(dotsAdmin.strings.saved);
                    window.location.href = 'admin.php?page=dream-tickets-promo-codes';
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
});

