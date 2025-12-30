jQuery(document).ready(function($) {
    
    // Calculate Price
    function calculatePrice() {
        var categorySelect = $('#ticket_category');
        var quantityInput = $('#ticket_quantity');
        var price = 0;
        
        if (categorySelect.length && categorySelect.val()) {
            var selectedOption = categorySelect.find('option:selected');
            var unitPrice = parseFloat(selectedOption.data('price')) || 0;
            var quantity = parseInt(quantityInput.val()) || 0;
            var maxQuantity = parseInt(selectedOption.data('max')) || 10;
            
            // Validate quantity
            if (quantity > maxQuantity) {
                quantity = maxQuantity;
                quantityInput.val(maxQuantity);
            }
            
            var availability = parseInt(selectedOption.data('availability')) || 0;
            if (quantity > availability) {
                quantity = availability;
                quantityInput.val(availability);
            }
            
            price = unitPrice * quantity;
            
            // Update quantity info
            $('.dots-quantity-info').text('Max ' + maxQuantity + ' per customer. ' + availability + ' available.');
        }
        
        var discount = parseFloat($('#dots-discount').data('amount')) || 0;
        var total = price - discount;
        var currencySymbol = dotsFrontend.currency_symbol || '$';
        
        $('#dots-subtotal').text(currencySymbol + price.toFixed(2));
        $('#dots-total').text(currencySymbol + (total > 0 ? total : 0).toFixed(2));
        
        if (discount > 0) {
            $('.dots-discount').show();
            $('#dots-discount').text('-' + currencySymbol + discount.toFixed(2));
        } else {
            $('.dots-discount').hide();
        }
    }
    
    // Update price on change
    $('#ticket_category, #ticket_quantity').on('change input', function() {
        calculatePrice();
    });
    
    // Apply Promo Code
    $('.dots-apply-promo').on('click', function(e) {
        e.preventDefault();
        var promoCode = $('#promo_code').val();
        var currencySymbol = dotsFrontend.currency_symbol || '$';
        var total = parseFloat($('#dots-subtotal').text().replace(currencySymbol, '').replace(/[^0-9.]/g, ''));
        
        if (!promoCode) {
            alert('Please enter a promo code.');
            return;
        }
        
        $.ajax({
            url: dotsFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_apply_promo',
                nonce: dotsFrontend.nonce,
                promo_code: promoCode,
                total: total
            },
            success: function(response) {
                if (response.success) {
                    var discount = response.data.discount || 0;
                    $('#dots-discount').data('amount', discount);
                    calculatePrice();
                    alert('Promo code applied!');
                } else {
                    alert(response.data.message || 'Invalid promo code.');
                }
            }
        });
    });
    
    // Process Purchase
    $('#dots-ticket-purchase-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('.dots-submit-button');
        var $message = $form.find('.dots-form-message');
        
        // Validate form
        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }
        
        // Disable submit button
        $submitBtn.prop('disabled', true).text('Processing...');
        $message.hide();
        
        // Collect form data
        var formData = $form.serialize();
        formData += '&action=dots_process_purchase&nonce=' + dotsFrontend.nonce;
        
        // Collect custom fields
        var customerData = {};
        $form.find('[name^="customer_data"]').each(function() {
            var name = $(this).attr('name').replace('customer_data[', '').replace(']', '');
            if ($(this).attr('type') === 'checkbox') {
                if (!customerData[name]) {
                    customerData[name] = [];
                }
                if ($(this).is(':checked')) {
                    customerData[name].push($(this).val());
                }
            } else {
                customerData[name] = $(this).val();
            }
        });
        
        formData += '&customer_data=' + JSON.stringify(customerData);
        
        $.ajax({
            url: dotsFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Redirect to confirmation page
                    window.location.href = response.data.redirect_url;
                } else {
                    $message.removeClass('success').addClass('error').text(response.data.message || 'An error occurred. Please try again.').show();
                    $submitBtn.prop('disabled', false).text('Purchase Tickets');
                }
            },
            error: function() {
                $message.removeClass('success').addClass('error').text('An error occurred. Please try again.').show();
                $submitBtn.prop('disabled', false).text('Purchase Tickets');
            }
        });
    });
    
    // Initial price calculation
    calculatePrice();
});

