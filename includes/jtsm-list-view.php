<?php

class JTSM_Solar_Management_List_View {


       // Singleton instance
       private static $instance = null;

       // Get instance
       public static function instance() {
           if ( self::$instance === null ) {
               self::$instance = new self();
           }
           return self::$instance;
       }


    /**
     * Render the list of all clients.
     */
    public function jtsm_render_client_list_page() {
        global $wpdb;
        $clients_table  = $wpdb->prefix . 'jtsm_clients';
        $payments_table = $wpdb->prefix . 'jtsm_payments';

        // Get filter from URL
        $filter  = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';

        $sql_clients = "SELECT * FROM $clients_table";
        if ( $filter === 'consumer' || $filter === 'seller' || $filter === 'expanse' ) {
            $sql_clients .= $wpdb->prepare( " WHERE user_type = %s", $filter );
        }
        $sql_clients .= " ORDER BY created_at DESC";

        $clients = $wpdb->get_results( $sql_clients );
        ?>
        <div class="wrap bg-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-gray-800">Clients</h1>
                <a href="?page=jtsm-add-client" class="jstm-text-color inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Add New Client</a>
            </div>
            
            <!-- Filter Row -->
            <div class="mb-4">
                <form method="get" class="inline-block bg-white p-4 rounded-lg shadow-md">
                    <input type="hidden" name="page" value="jtsm-main-menu">
                    <label for="filter" class="text-sm font-medium text-gray-700 mr-2">Filter by Type:</label>
                    <select name="filter" id="filter" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="all" <?php selected($filter, 'all'); ?>>All Types</option>
                        <option value="consumer" <?php selected($filter, 'consumer'); ?>>Consumer</option>
                        <option value="seller" <?php selected($filter, 'seller'); ?>>Seller</option>
                        <option value="expanse" <?php selected($filter, 'expanse'); ?>>Expanse</option>
                    </select>
                </form>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Added</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($clients): foreach ($clients as $client): ?>
                        <?php
                            $view_link = admin_url('admin.php?page=jtsm-view-client&client_id=' . $client->id);
                            $edit_link = admin_url('admin.php?page=jtsm-edit-client&client_id=' . $client->id);
                            $delete_link = wp_nonce_url(admin_url('admin.php?page=jtsm-main-menu&action=delete_client&client_id=' . $client->id), 'jtsm_delete_client_' . $client->id);
                            $total_paid   = $wpdb->get_var($wpdb->prepare( "SELECT SUM(CASE WHEN c.user_type = 'consumer' THEN p.amount ELSE p.amount_with_gst END) FROM $payments_table p JOIN $clients_table c ON p.client_id = c.id WHERE p.client_id = %d", $client->id ));
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="<?php echo esc_url($view_link); ?>" class="text-indigo-600 hover:text-indigo-900"><?php echo esc_html($client->first_name . ' ' . $client->last_name); ?></a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($client->company_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($client->contact_number); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $client->user_type === 'consumer' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>"><?php echo ucfirst(esc_html($client->user_type)); ?></span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format(floatval($client->proposal_amount), 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format(floatval($total_paid), 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html( date_i18n( get_option('date_format'), strtotime($client->created_at) ) ); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo esc_url($view_link); ?>" class="text-gray-600 hover:text-indigo-900">View</a> |
                                <a href="<?php echo esc_url($edit_link); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a> | 
                                <a href="<?php echo esc_url($delete_link); ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this client and all their payments?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="8" class="text-center py-4">No clients found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }


    function consumer_and_saller_total_amount($filter){

        global $wpdb;
        $clients_table = $wpdb->prefix . 'jtsm_clients';
        $payments_table = $wpdb->prefix . 'jtsm_payments';


        $sql = "SELECT p.*, c.first_name, c.last_name, c.user_type FROM $payments_table p LEFT JOIN $clients_table c ON p.client_id = c.id";

        // Add a WHERE clause if a filter is selected
        if ($filter === 'consumer' || $filter === 'seller' || $filter === 'expanse') {
            $sql .= $wpdb->prepare(" WHERE c.user_type = %s", $filter);
        }

        $sql .= " ORDER BY p.payment_date DESC";

        $payments = $wpdb->get_results($sql);
        return $payments;
    }

    /**
     * Render the list of all payments with filtering and total amount.
     */
    public function jtsm_render_payment_list_page() {
     
        // Get the current filter value from the URL
        $filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';

        $payments = $this->consumer_and_saller_total_amount($filter);

        // Calculate total amounts by type
        $total_amount = 0;
        $total_consumer_amount = 0;
        $total_seller_amount   = 0;
        $total_expanse_amount  = 0;
        if ($payments) {
            foreach ($payments as $payment) {
                if ($payment->user_type === 'consumer') {
                    $total_consumer_amount += floatval($payment->amount);
                    $total_amount += floatval($payment->amount);
                } elseif ($payment->user_type === 'seller') {
                    $total_seller_amount += floatval($payment->amount_with_gst);
                    $total_amount += floatval($payment->amount_with_gst);
                } else {
                    $total_expanse_amount += floatval($payment->amount);
                    $total_amount += floatval($payment->amount);
                }
            }
        }
        ?>
        <div class="wrap bg-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-gray-800">Payments</h1>
                <a href="?page=jtsm-add-payment" class="jstm-text-color inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">Add New Payment</a>
            </div>

            <!-- Filter and Total Amount Row -->
            <div class="grid grid-cols-1 <?php echo $filter === 'all' ? 'md:grid-cols-3' : 'md:grid-cols-2'; ?> gap-4 mb-4">
                <!-- Filter Form -->
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <form method="get">
                        <input type="hidden" name="page" value="jtsm-all-payments">
                        <label for="filter" class="text-sm font-medium text-gray-700 mr-2">Filter by Type:</label>
                        <select name="filter" id="filter" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="all" <?php selected($filter, 'all'); ?>>All Types</option>
                            <option value="consumer" <?php selected($filter, 'consumer'); ?>>Consumer</option>
                            <option value="seller" <?php selected($filter, 'seller'); ?>>Seller</option>
                            <option value="expanse" <?php selected($filter, 'expanse'); ?>>Expanse</option>
                        </select>
                    </form>
                </div>
                <!-- Totals Display -->
                <?php if ($filter === 'all'): ?>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Consumer Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_consumer_amount, 2); ?></p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Seller Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_seller_amount, 2); ?></p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Expanse Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_expanse_amount, 2); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_amount, 2); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Type</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receive/Type</th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th></tr></thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($payments): foreach ($payments as $payment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo esc_html($payment->first_name . ' ' . $payment->last_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $payment->user_type === 'consumer' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>"><?php echo ucfirst(esc_html($payment->user_type)); ?></span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($payment->payment_date); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                    if ($payment->user_type === 'seller') {
                                        $amount = $payment->amount_with_gst;
                                    } else {
                                        $amount = $payment->amount;
                                    }
                                    echo number_format(floatval($amount), 2);
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst(esc_html($payment->payment_mode)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                    if ($payment->user_type === 'expanse') {
                                        echo ucfirst(esc_html($payment->payment_type));
                                    } else {
                                        echo ucfirst(esc_html($payment->payment_receive));
                                    }
                                ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a> | <a href="#" class="text-red-600 hover:text-red-900">Delete</a></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="text-center py-4">No payments found for this filter.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}