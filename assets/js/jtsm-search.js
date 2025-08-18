jQuery(document).ready(function($) {
    'use strict';

    var clientSearch = $('#jtsm-client-search');
    var paymentSearch = $('#jtsm-payment-search');

    function searchClients() {
        var query = clientSearch.val();
        var filter = $('#filter').val();
        $.ajax({
            url: jtsm_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'jtsm_search_clients',
                _ajax_nonce: jtsm_ajax_object.nonce,
                search: query,
                filter: filter
            },
            success: function(response) {
                if (response.success) {
                    $('#jtsm-clients-table-body').html(response.data.html);
                }
            }
        });
    }

    function searchPayments() {
        var query = paymentSearch.val();
        var filter = $('#filter').val();
        $.ajax({
            url: jtsm_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'jtsm_search_payments',
                _ajax_nonce: jtsm_ajax_object.nonce,
                search: query,
                filter: filter
            },
            success: function(response) {
                if (response.success) {
                    $('#jtsm-payments-table-body').html(response.data.html);
                }
            }
        });
    }

    if (clientSearch.length) {
        clientSearch.on('keyup', searchClients);
    }
    if (paymentSearch.length) {
        paymentSearch.on('keyup', searchPayments);
    }
});
