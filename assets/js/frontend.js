jQuery(document).ready(function($) {
    
    // Calculate Price
    function calculatePrice() {
        var quantityInput = $('#ticket_quantity');
        if (!quantityInput.length) {
            return; // Form not on page
        }
        
        var quantity = parseInt(quantityInput.val()) || 1;
        var currencySymbol = (typeof dotsFrontend !== 'undefined' && dotsFrontend.currency_symbol) ? dotsFrontend.currency_symbol : '$';
        
        var unitPrice = 0;
        var maxQuantity = 10;
        var availability = 0;
        
        // Try to get from form data attribute first (most reliable on page)
        var form = $('#dots-ticket-purchase-form');
        if (form.length) {
            unitPrice = parseFloat(form.data('ticket-price')) || 0;
        }
        
        // If not in data attribute, try localized data
        if (unitPrice <= 0 && typeof dotsFrontend !== 'undefined') {
            unitPrice = parseFloat(dotsFrontend.event_ticket_price) || 0;
            maxQuantity = parseInt(dotsFrontend.event_max_tickets_per_customer) || 10;
            availability = parseInt(dotsFrontend.event_tickets_available) || 0;
        }
        
        // If still not found, try to get from price display
        if (unitPrice <= 0) {
            var priceBox = $('.dots-price-amount');
            if (priceBox.length) {
                // Extract price from displayed text - remove currency symbol and commas
                var priceText = priceBox.text().replace(currencySymbol, '').replace(/,/g, '').replace(/[^0-9.]/g, '').trim();
                unitPrice = parseFloat(priceText) || 0;
            }
        }
        
        // Debug logging
        console.log('Price calculation:', {
            unitPrice: unitPrice,
            quantity: quantity,
            fromDataAttr: form.length ? form.data('ticket-price') : 'N/A',
            fromLocalized: typeof dotsFrontend !== 'undefined' ? dotsFrontend.event_ticket_price : 'N/A'
        });
        
        // Validate quantity
        if (quantity < 1) {
            quantity = 1;
            quantityInput.val(1);
        }
        
        if (maxQuantity > 0 && quantity > maxQuantity) {
            quantity = maxQuantity;
            quantityInput.val(maxQuantity);
        }
        
        if (availability > 0 && quantity > availability) {
            quantity = availability;
            quantityInput.val(availability);
        }
        
        // Calculate prices
        var price = unitPrice * quantity;
        var discount = parseFloat($('#dots-discount').data('amount')) || 0;
        var total = Math.max(0, price - discount);
        
        // Update display
        if (unitPrice > 0) {
            $('#dots-subtotal').text(currencySymbol + price.toFixed(2));
            $('#dots-total').text(currencySymbol + total.toFixed(2));
        } else {
            $('#dots-subtotal').text(currencySymbol + '0.00');
            $('#dots-total').text(currencySymbol + '0.00');
        }
        
        if (discount > 0) {
            $('.dots-discount').show();
            $('#dots-discount').text('-' + currencySymbol + discount.toFixed(2));
        } else {
            $('.dots-discount').hide();
        }
    }
    
    // Update price on quantity change
    $('#ticket_quantity').on('change input', function() {
        calculatePrice();
    });
    
    // Legacy support for dropdown (if used)
    $('#ticket_category').on('change', function() {
        calculatePrice();
        updateAvailability();
    });
    
    // Update availability display (for dropdown)
    function updateAvailability() {
        var categorySelect = $('#ticket_category');
        var availabilityDisplay = $('#dots-availability-display');
        var availabilityCount = $('#dots-availability-count');
        
        if (categorySelect.length && categorySelect.val()) {
            var selectedOption = categorySelect.find('option:selected');
            var availability = parseInt(selectedOption.data('availability')) || 0;
            
            if (availability > 0) {
                availabilityDisplay.show();
                availabilityCount.text(availability);
                availabilityCount.removeClass('dots-sold-out').addClass('dots-available');
            } else {
                availabilityDisplay.show();
                availabilityCount.text('Sold Out');
                availabilityCount.removeClass('dots-available').addClass('dots-sold-out');
            }
        } else {
            availabilityDisplay.hide();
        }
    }
    
    // Apply Promo Code
    $('.dots-apply-promo').on('click', function(e) {
        e.preventDefault();
        var promoCode = $('#promo_code').val().trim().toUpperCase();
        var currencySymbol = dotsFrontend.currency_symbol || '$';
        
        // Get current subtotal
        var unitPrice = parseFloat(dotsFrontend.event_ticket_price) || 0;
        var quantity = parseInt($('#ticket_quantity').val()) || 1;
        var total = unitPrice * quantity;
        
        // If not in localized data, try to get from display
        if (total <= 0) {
            var subtotalText = $('#dots-subtotal').text().replace(currencySymbol, '').replace(/[^0-9.]/g, '');
            total = parseFloat(subtotalText) || 0;
        }
        
        if (!promoCode) {
            alert('Please enter a promo code.');
            return;
        }
        
        if (total <= 0) {
            alert('Please select ticket quantity first.');
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.text();
        $btn.prop('disabled', true).text('Applying...');
        
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
                $btn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    var discount = parseFloat(response.data.discount) || 0;
                    $('#dots-discount').data('amount', discount);
                    calculatePrice();
                    
                    // Show success message
                    var $message = $('.dots-form-message');
                    $message.removeClass('error').addClass('success').text(response.data.message || 'Promo code applied!').show();
                    setTimeout(function() {
                        $message.fadeOut();
                    }, 3000);
                } else {
                    var errorMsg = response.data && response.data.message ? response.data.message : 'Invalid promo code.';
                    alert(errorMsg);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(originalText);
                alert('An error occurred. Please try again.');
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
        
        formData += '&customer_data=' + encodeURIComponent(JSON.stringify(customerData));
        
        // Show loading message
        $message.removeClass('error').addClass('success').text('Processing your order...').show();
        
        $.ajax({
            url: dotsFrontend.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 30000,
            success: function(response) {
                console.log('Purchase response:', response);
                
                if (response && response.success) {
                    // Check if payment redirect is needed
                    if (response.data.redirect && response.data.redirect_url) {
                        // PayPal redirect
                        $message.text('Redirecting to payment...').show();
                        window.location.href = response.data.redirect_url;
                    } else if (response.data.stripe && response.data.client_secret) {
                        // Stripe payment - handle with Stripe.js
                        handleStripePayment(response.data.client_secret, response.data.payment_intent_id, response.data.order_number);
                    } else if (response.data.redirect_url) {
                        // Bank transfer or other - redirect to confirmation
                        $message.text('Order successful! Redirecting...').show();
                        setTimeout(function() {
                            window.location.href = response.data.redirect_url;
                        }, 500);
                    } else if (response.data.order_number) {
                        // Direct redirect to order confirmation
                        $message.text('Order created! Redirecting...').show();
                        var orderUrl = dotsFrontend.ajax_url.replace('admin-ajax.php', '') + 'dream-tickets/order/' + response.data.order_number;
                        setTimeout(function() {
                            window.location.href = orderUrl;
                        }, 1000);
                    } else {
                        $message.removeClass('success').addClass('error').text('Invalid response from server.').show();
                        $submitBtn.prop('disabled', false).text('Purchase Tickets');
                    }
                } else {
                    var errorMsg = (response && response.data && response.data.message) ? response.data.message : 'An error occurred. Please try again.';
                    $message.removeClass('success').addClass('error').text(errorMsg).show();
                    $submitBtn.prop('disabled', false).text('Purchase Tickets');
                }
            },
            error: function(xhr, status, error) {
                console.error('Purchase error:', xhr, status, error);
                var errorMsg = 'An error occurred. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    errorMsg = xhr.responseJSON.data.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMsg = response.data.message;
                        }
                    } catch(e) {
                        // Not JSON, use default message
                    }
                }
                
                $message.removeClass('success').addClass('error').text(errorMsg).show();
                $submitBtn.prop('disabled', false).text('Purchase Tickets');
            }
        });
    });
    
    // Handle Stripe payment
    var stripe = null;
    var cardElement = null;
    
    if (typeof Stripe !== 'undefined' && typeof dotsFrontend !== 'undefined' && dotsFrontend.stripe_publishable_key && $('#card-element').length) {
        stripe = Stripe(dotsFrontend.stripe_publishable_key);
        var elements = stripe.elements();
        
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        cardElement = elements.create('card', {style: style});
        cardElement.mount('#card-element');

        cardElement.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    }
    
    $('#payment_method').on('change', function() {
        if ($(this).val() === 'stripe') {
            $('#dots-stripe-element-container').slideDown();
        } else {
            $('#dots-stripe-element-container').slideUp();
        }
    });

    function handleStripePayment(clientSecret, paymentIntentId, orderNumber) {
        if (stripe && cardElement) {
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement
                }
            }).then(function(result) {
                if (result.error) {
                    $('.dots-form-message').removeClass('success').addClass('error').text('Payment failed: ' + result.error.message).show();
                    $('.dots-submit-button').prop('disabled', false).text('Purchase Tickets');
                } else {
                    // Payment succeeded, verify with backend via POST
                    $.ajax({
                        url: dotsFrontend.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'dots_verify_payment',
                            nonce: dotsFrontend.nonce,
                            payment_id: paymentIntentId,
                            payment_method: 'stripe',
                            order_number: orderNumber
                        },
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.data.redirect_url;
                            } else {
                                $('.dots-form-message').removeClass('success').addClass('error').text('Payment verification failed. Please contact support.').show();
                                $('.dots-submit-button').prop('disabled', false).text('Purchase Tickets');
                            }
                        },
                        error: function() {
                            $('.dots-form-message').removeClass('success').addClass('error').text('Network error during verification.').show();
                            $('.dots-submit-button').prop('disabled', false).text('Purchase Tickets');
                        }
                    });
                }
            });
        } else {
            alert('Stripe.js is not loaded properly. Please contact the site administrator.');
            $('.dots-submit-button').prop('disabled', false).text('Purchase Tickets');
            $('.dots-form-message').hide();
        }
    }
    
    // Initial price calculation - trigger on page load
    if ($('#dots-ticket-purchase-form').length) {
        // Wait a bit for everything to load
        setTimeout(function() {
            calculatePrice();
        }, 100);
        
        // Also trigger after a longer delay to ensure DOM is ready
        setTimeout(function() {
            calculatePrice();
        }, 500);
        
        // Trigger calculation when page becomes visible
        $(document).on('visibilitychange', function() {
            if (!document.hidden) {
                calculatePrice();
            }
        });
    }
});

