<?php
/**
 * Renders the detailed view page for a single client.
 */
function jtsm_render_view_client_page() {
    global $wpdb;

    if (!isset($_GET['client_id'])) {
        echo '<div class="wrap"><div class="notice notice-error"><p>No client specified.</p></div></div>';
        return;
    }

    $client_id = intval($_GET['client_id']);
    $clients_table = $wpdb->prefix . 'jtsm_clients';
    $payments_table = $wpdb->prefix . 'jtsm_payments';

    // Fetch client data
    $client = $wpdb->get_row($wpdb->prepare("SELECT * FROM $clients_table WHERE id = %d", $client_id));

    if (!$client) {
        echo '<div class="wrap"><div class="notice notice-error"><p>Client not found.</p></div></div>';
        return;
    }

    // Fetch payment data for this client
    $payments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $payments_table WHERE client_id = %d ORDER BY payment_date DESC", $client_id));

    // Calculate total paid amount
    $total_paid = 0;
    if ($payments) {
        foreach ($payments as $payment) {
            $amount = $client->user_type === 'consumer' ? $payment->amount : $payment->amount_with_gst;
            $total_paid += floatval($amount);
        }
    }
    ?>
    <div class="wrap bg-gray-100 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <?php echo esc_html($client->first_name . ' ' . $client->last_name); ?>
            </h1>
            <a href="<?php echo admin_url('admin.php?page=jtsm-main-menu'); ?>" class="text-indigo-600 hover:text-indigo-900">&larr; Back to All Clients</a>
        </div>

        <!-- Client Details & Total Paid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Client Info Card -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">Client Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-500">Company</p>
                        <p class="text-gray-900"><?php echo esc_html($client->company_name) ?: 'N/A'; ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Client Type</p>
                        <p class="text-gray-900"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $client->user_type === 'consumer' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>"><?php echo ucfirst(esc_html($client->user_type)); ?></span></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Contact Number</p>
                        <p class="text-gray-900"><?php echo esc_html($client->contact_number); ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Email Address</p>
                        <p class="text-gray-900"><?php echo esc_html($client->email); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="font-medium text-gray-500">Address</p>
                        <p class="text-gray-900"><?php echo nl2br(esc_html($client->address)) ?: 'N/A'; ?></p>
                    </div>
                     <?php if ($client->file_url): ?>
                    <div class="sm:col-span-2">
                         <p class="font-medium text-gray-500">Uploaded File</p>
                         <p><a href="<?php echo esc_url($client->file_url); ?>" target="_blank" class="text-indigo-600 hover:underline">View File</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Total Paid Card -->
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center">
                 <h2 class="text-xl font-semibold text-gray-800 mb-2">Total Amount Paid</h2>
                 <p class="text-4xl font-bold text-indigo-600"><?php echo number_format($total_paid, 2); ?></p>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <h2 class="text-xl font-semibold text-gray-800 p-6 border-b">Payment History</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <?php if ($client->user_type === 'consumer'): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment</th>
                        <?php else: ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receive</th>

                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($payments): foreach ($payments as $payment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($payment->payment_date); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                if ($client->user_type === 'consumer') {
                                    echo esc_html($payment->installment);
                                } else {
                                    if ($payment->invoice_url) {
                                        echo '<a href="'.esc_url($payment->invoice_url).'" target="_blank" class="text-indigo-600 hover:underline">View Invoice</a>';
                                    } else {
                                        echo 'N/A';
                                    }
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst(esc_html($payment->payment_mode)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst(esc_html($payment->payment_receive)); ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                <?php 
                                    $amount = $client->user_type === 'consumer' ? $payment->amount : $payment->amount_with_gst;
                                    echo number_format(floatval($amount), 2);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center py-10 text-gray-500">No payments have been recorded for this client.</td></tr>
                    <?php endif; ?>
                </tbody>
                 <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-bold text-zinc-950 uppercase">Total</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-zinc-950"><?php echo number_format($total_paid, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
}
