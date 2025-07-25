jQuery(document).ready(function($) {
    'use strict';

    // Get the metaboxes
    const consumerBox = $('#jtsm_consumer_payment_details');
    const sellerBox = $('#jtsm_seller_payment_details');
    const clientSelector = $('#jtsm_linked_client');

    // Function to show/hide payment boxes
    function togglePaymentBoxes(clientType) {
        if (clientType === 'consumer') {
            consumerBox.show();
            sellerBox.hide();
        } else if (clientType === 'seller') {
            consumerBox.hide();
            sellerBox.show();
        } else {
            consumerBox.hide();
            sellerBox.hide();
        }
    }

    // Initially hide both boxes
    togglePaymentBoxes(null);

    // Check on page load if a client is already selected
    if (clientSelector.val()) {
        fetchClientType(clientSelector.val());
    }

    // Event listener for when a client is selected
    clientSelector.on('change', function() {
        const clientId = $(this).val();
        if (clientId) {
            fetchClientType(clientId);
        } else {
            togglePaymentBoxes(null);
        }
    });

    // AJAX function to get the client's user type
    function fetchClientType(clientId) {
        $.ajax({
            url: jtsm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jtsm_get_client_type',
                client_id: clientId,
                _ajax_nonce: jtsm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    togglePaymentBoxes(response.data.user_type);
                }
            }
        });
    }

    // --- Seller Payment GST Calculation ---
    const amountWithoutGstField = $('#jtsm_amount_without_gst');
    const gstRateField = $('#jtsm_gst_rate');
    const amountWithGstField = $('#jtsm_amount_with_gst');

    function calculateGst() {
        const baseAmount = parseFloat(amountWithoutGstField.val());
        const gstRate = parseFloat(gstRateField.val());

        if (!isNaN(baseAmount) && !isNaN(gstRate)) {
            const gstAmount = baseAmount * (gstRate / 100);
            const totalAmount = baseAmount + gstAmount;
            amountWithGstField.val(totalAmount.toFixed(2));
        } else {
            amountWithGstField.val('');
        }
    }

    // Calculate on input change
    amountWithoutGstField.on('input', calculateGst);
    gstRateField.on('change', calculateGst);

});
