jQuery(document).ready(function($) {
    'use strict';

    // --- Dynamic Payment Form Logic ---

    const clientSelector = $('#jtsm_client_id');
    const consumerForm = $('#jtsm-consumer-form');
    const sellerForm = $('#jtsm-seller-form');
    const expanseForm = $('#jtsm-expanse-form');
    const submitContainer = $('#jtsm-submit-button-container');

    // --- Add Client Form Logic ---
    const userTypeField = $('#jtsm_user_type');
    const serviceField = $('#product_service').closest('div');
    const panelKwField = $('#product_kw').closest('div');
    const proposalField = $('#proposal_amount').closest('div');
    const companyField = $('#jtsm_company_name').closest('div');
    const shortDescField = $('#jtsm_short_description').closest('div');

    function toggleClientFields(userType) {
        if (userType === 'seller') {
            serviceField.hide();
            panelKwField.hide();
            proposalField.hide();
            companyField.show();
            shortDescField.hide();
            $('#product_service').prop('required', false);
        } else if (userType === 'expanse') {
            serviceField.hide();
            panelKwField.hide();
            proposalField.hide();
            companyField.hide();
            shortDescField.show();
            $('#product_service').prop('required', false);
        } else {
            serviceField.show();
            panelKwField.show();
            proposalField.show();
            companyField.show();
            shortDescField.hide();
            $('#product_service').prop('required', true);
        }
    }

    // Initialize client fields based on current selection
    if (userTypeField.length) {
        toggleClientFields(userTypeField.val());
        userTypeField.on('change', function () {
            toggleClientFields($(this).val());
        });
    }

    // Function to show/hide payment forms based on client type
    function togglePaymentForms(clientType) {
        // Hide all forms and the submit button first
        consumerForm.hide();
        sellerForm.hide();
        expanseForm.hide();
        submitContainer.hide();

        if (clientType === 'consumer') {
            consumerForm.show();
            submitContainer.show();
        } else if (clientType === 'seller') {
            sellerForm.show();
            submitContainer.show();
        } else if (clientType === 'expanse') {
            expanseForm.show();
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
