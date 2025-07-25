jQuery(document).ready(function($) {
    'use strict';

    // --- Dynamic Payment Form Logic ---

    const clientSelector = $('#jtsm_client_id');
    const consumerForm = $('#jtsm-consumer-form');
    const sellerForm = $('#jtsm-seller-form');
    const submitContainer = $('#jtsm-submit-button-container');

    // Function to show/hide payment forms based on client type
    function togglePaymentForms(clientType) {
        // Hide all forms and the submit button first
        consumerForm.hide();
        sellerForm.hide();
        submitContainer.hide();

        if (clientType === 'consumer') {
            consumerForm.show();
            submitContainer.show();
        } else if (clientType === 'seller') {
            sellerForm.show();
            submitContainer.show();
        }
    }

    // Initially, hide everything until a client is selected
    togglePaymentForms(null);

    // Event listener for when a client is selected from the dropdown
    clientSelector.on('change', function() {
        const clientId = $(this).val();

        if (!clientId) {
            togglePaymentForms(null);
            return;
        }

        // Make an AJAX call to get the selected client's type
        $.ajax({
            url: jtsm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'jtsm_get_client_type_table',
                _ajax_nonce: jtsm_ajax_object.nonce,
                client_id: clientId,
            },
            success: function(response) {
                if (response.success) {
                    togglePaymentForms(response.data.user_type);
                } else {
                    togglePaymentForms(null);
                    console.error('Failed to get client type.');
                }
            },
            error: function() {
                togglePaymentForms(null);
                console.error('AJAX request failed.');
            }
        });
    });


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

    // Add event listeners to calculate GST automatically
    amountWithoutGstField.on('input', calculateGst);
    gstRateField.on('change', calculateGst);

});
