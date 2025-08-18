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

       function total_payment_clients($clients_table,$clientId){
                global $wpdb;

                $payments_table = $wpdb->prefix . 'jtsm_payments';

        $total_paid = $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT SUM(
                        CASE 
                            WHEN c.user_type IN ('consumer', 'seller', 'expender') THEN p.amount 
                            ELSE p.amount_with_gst 
                        END
                    )
                    FROM $payments_table p
                    JOIN $clients_table c ON p.client_id = c.id
                    WHERE p.client_id = %d
                    ",
                    $clientId
                )
            );

            return number_format(floatval($total_paid), 2);
        }


    /**
     * Render the list of all clients.
     */
    public function jtsm_render_client_list_page() {
        global $wpdb;
        $clients_table  = $wpdb->prefix . 'jtsm_clients';

        // Get filter from URL
        $filter  = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';

        $sql_clients = "SELECT * FROM $clients_table";
        if ( $filter === 'consumer' || $filter === 'seller' || $filter === 'expender' ) {
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
            
            <!-- Filter and Search Row -->
            <div class="mb-4 flex items-center space-x-4">
                <form method="get" class="inline-block bg-white p-4 rounded-lg shadow-md">
                    <input type="hidden" name="page" value="jtsm-main-menu">
                    <label for="filter" class="text-sm font-medium text-gray-700 mr-2">Filter by Type:</label>
                    <select name="filter" id="filter" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="all" <?php selected($filter, 'all'); ?>>All Types</option>
                        <option value="consumer" <?php selected($filter, 'consumer'); ?>>Consumer</option>
                        <option value="seller" <?php selected($filter, 'seller'); ?>>Seller</option>
                        <option value="expender" <?php selected($filter, 'expender'); ?>>Expender</option>
                    </select>
                </form>
                <input type="text" id="jtsm-client-search" placeholder="Search..." class="bg-white p-2 rounded-lg shadow-md border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
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
                    <tbody class="bg-white divide-y divide-gray-200" id="jtsm-clients-table-body">
                    <?php if ($clients): foreach ($clients as $client): ?>
                        <?php
                            $view_link = admin_url('admin.php?page=jtsm-view-client&client_id=' . $client->id);
                            $edit_link = admin_url('admin.php?page=jtsm-edit-client&client_id=' . $client->id);
                            $delete_link = wp_nonce_url(admin_url('admin.php?page=jtsm-main-menu&action=delete_client&client_id=' . $client->id), 'jtsm_delete_client_' . $client->id);
                  ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="<?php echo esc_url($view_link); ?>" class="text-indigo-600 hover:text-indigo-900"><?php echo esc_html($client->first_name . ' ' . $client->last_name); ?></a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($client->company_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($client->contact_number); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                $badge = 'bg-blue-100 text-blue-800';
                                if ($client->user_type === 'consumer') {
                                    $badge = 'bg-green-100 text-green-800';
                                } elseif ($client->user_type === 'seller') {
                                    $badge = 'bg-pink-100 text-pink-800';
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge; ?>"><?php echo ucfirst(esc_html($client->user_type)); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format(floatval($client->proposal_amount), 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $this->total_payment_clients($clients_table,$client->id); ?></td>
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


        $sql = "SELECT p.*, c.first_name, c.last_name, c.user_type,
                       oc.first_name AS other_first_name,
                       oc.last_name AS other_last_name
                FROM $payments_table p
                LEFT JOIN $clients_table c ON p.client_id = c.id
                LEFT JOIN $clients_table oc ON p.other_client_id = oc.id";

        // Add a WHERE clause if a filter is selected
        if ($filter === 'consumer' || $filter === 'seller' || $filter === 'expender') {
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
        $total_expender_received = 0;
        $total_expender_sent    = 0;
        $total_expender_amount  = 0;
        if ($payments) {
            foreach ($payments as $payment) {
                if ($payment->user_type === 'consumer') {
                    $total_consumer_amount += floatval($payment->amount);
                    $total_amount += floatval($payment->amount);
                } elseif ($payment->user_type === 'seller') {
                    $total_seller_amount += floatval($payment->amount);
                    $total_amount += floatval($payment->amount);
                } elseif ($payment->user_type === 'expender') {
                    if ($payment->payment_type === 'receiver') {
                        $total_expender_received += floatval($payment->amount);
                    } else {
                        $total_expender_sent += floatval($payment->amount);
                    }
                    $total_expender_amount += floatval($payment->amount);
                    $total_amount += floatval($payment->amount);
                } else {
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

            <div class="mb-4">
                <input type="text" id="jtsm-payment-search" placeholder="Search..." class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
            </div>

            <!-- Filter and Total Amount Row -->
            <div class="grid grid-cols-1 <?php echo $filter === 'all' ? 'md:grid-cols-4' : 'md:grid-cols-2'; ?> gap-4 mb-4">
                <!-- Filter Form -->
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <form method="get">
                        <input type="hidden" name="page" value="jtsm-all-payments">
                        <label for="filter" class="text-sm font-medium text-gray-700 mr-2">Filter by Type:</label>
                        <select name="filter" id="filter" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="all" <?php selected($filter, 'all'); ?>>All Types</option>
                            <option value="consumer" <?php selected($filter, 'consumer'); ?>>Consumer</option>
                            <option value="seller" <?php selected($filter, 'seller'); ?>>Seller</option>
                            <option value="expender" <?php selected($filter, 'expender'); ?>>Expender</option>
                        </select>
                    </form>
                </div>
                <!-- Totals Display -->
                <?php if ($filter === 'all'): ?>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Seller Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_seller_amount, 2); ?></p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Expender Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_expender_amount, 2); ?></p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Consumer Amount</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_consumer_amount, 2); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($filter === 'expender'): ?>
                        <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 uppercase">Total Received</p>
                                <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_expender_received, 2); ?></p>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 uppercase">Total Sent</p>
                                <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_expender_sent, 2); ?></p>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 uppercase">Final Amount</p>
                                <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($total_expender_received - $total_expender_sent, 2); ?></p>
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
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Type</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receive/Type</th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th></tr></thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="jtsm-payments-table-body">
                    <?php if ($payments): foreach ($payments as $payment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo esc_html($payment->first_name . ' ' . $payment->last_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                $badge = 'bg-blue-100 text-blue-800';
                                if ($payment->user_type === 'consumer') {
                                    $badge = 'bg-green-100 text-green-800';
                                } elseif ($payment->user_type === 'seller') {
                                    $badge = 'bg-pink-100 text-pink-800';
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge; ?>"><?php echo ucfirst(esc_html($payment->user_type)); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html($payment->payment_date); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                
                                        $amount = $payment->amount;
                              
                                    echo number_format(floatval($amount), 2);
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst(esc_html($payment->payment_mode)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                    if ($payment->user_type === 'expender') {
                                        $label = ucfirst(esc_html($payment->payment_type));
                                        if ($payment->payment_type === 'sender' && $payment->other_first_name) {
                                            $label .= ' to ' . esc_html($payment->other_first_name . ' ' . $payment->other_last_name);
                                        }
                                        echo $label;
                                    } else {
                                        echo ucfirst(esc_html($payment->payment_receive));
                                    }
                                ?>
                            </td>

                            <?php $edit_link = admin_url('admin.php?page=jtsm-edit-payment&payment_id=' . $payment->id); ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="<?php echo esc_url($edit_link); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a> | <a href="#" class="text-red-600 hover:text-red-900">Delete</a></td>
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

    /**
     * AJAX search handler for clients list.
     */
    public function jtsm_ajax_search_clients() {
        check_ajax_referer('jtsm_ajax_nonce');
        global $wpdb;
        $clients_table  = $wpdb->prefix . 'jtsm_clients';
        $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'all';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        $sql = "SELECT * FROM $clients_table";
        $where = [];
        if ( $filter === 'consumer' || $filter === 'seller' || $filter === 'expender' ) {
            $where[] = $wpdb->prepare("user_type = %s", $filter);
        }
        if ( $search !== '' ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $where[] = $wpdb->prepare("(first_name LIKE %s OR last_name LIKE %s OR company_name LIKE %s)", $like, $like, $like);
        }
        if ( $where ) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY created_at DESC';
        $clients = $wpdb->get_results( $sql );

        ob_start();
        if ( $clients ) {
            foreach ( $clients as $client ) {
                $view_link   = admin_url( 'admin.php?page=jtsm-view-client&client_id=' . $client->id );
                $edit_link   = admin_url( 'admin.php?page=jtsm-edit-client&client_id=' . $client->id );
                $delete_link = wp_nonce_url( admin_url( 'admin.php?page=jtsm-main-menu&action=delete_client&client_id=' . $client->id ), 'jtsm_delete_client_' . $client->id );
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><a href="<?php echo esc_url( $view_link ); ?>" class="text-indigo-600 hover:text-indigo-900"><?php echo esc_html( $client->first_name . ' ' . $client->last_name ); ?></a></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html( $client->company_name ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html( $client->contact_number ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php
                        $badge = 'bg-blue-100 text-blue-800';
                        if ( $client->user_type === 'consumer' ) {
                            $badge = 'bg-green-100 text-green-800';
                        } elseif ( $client->user_type === 'seller' ) {
                            $badge = 'bg-pink-100 text-pink-800';
                        }
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge; ?>"><?php echo ucfirst( esc_html( $client->user_type ) ); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format( floatval( $client->proposal_amount ), 2 ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $this->total_payment_clients( $clients_table, $client->id ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $client->created_at ) ) ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="<?php echo esc_url( $view_link ); ?>" class="text-gray-600 hover:text-indigo-900">View</a> | <a href="<?php echo esc_url( $edit_link ); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a> | <a href="<?php echo esc_url( $delete_link ); ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this client and all their payments?');">Delete</a></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="8" class="text-center py-4">No clients found.</td></tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success( [ 'html' => $html ] );
    }

    /**
     * AJAX search handler for payments list.
     */
    public function jtsm_ajax_search_payments() {
        check_ajax_referer('jtsm_ajax_nonce');
        global $wpdb;
        $clients_table = $wpdb->prefix . 'jtsm_clients';
        $payments_table = $wpdb->prefix . 'jtsm_payments';
        $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'all';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        $sql = "SELECT p.*, c.first_name, c.last_name, c.user_type, oc.first_name AS other_first_name, oc.last_name AS other_last_name FROM $payments_table p LEFT JOIN $clients_table c ON p.client_id = c.id LEFT JOIN $clients_table oc ON p.other_client_id = oc.id";
        $where = [];
        if ( $filter === 'consumer' || $filter === 'seller' || $filter === 'expender' ) {
            $where[] = $wpdb->prepare('c.user_type = %s', $filter);
        }
        if ( $search !== '' ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $where[] = $wpdb->prepare('(c.first_name LIKE %s OR c.last_name LIKE %s)', $like, $like);
        }
        if ( $where ) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY p.payment_date DESC';
        $payments = $wpdb->get_results( $sql );

        ob_start();
        if ( $payments ) {
            foreach ( $payments as $payment ) {
                $edit_link = admin_url('admin.php?page=jtsm-edit-payment&payment_id=' . $payment->id);
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo esc_html( $payment->first_name . ' ' . $payment->last_name ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php
                        $badge = 'bg-blue-100 text-blue-800';
                        if ( $payment->user_type === 'consumer' ) {
                            $badge = 'bg-green-100 text-green-800';
                        } elseif ( $payment->user_type === 'seller' ) {
                            $badge = 'bg-pink-100 text-pink-800';
                        }
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge; ?>"><?php echo ucfirst( esc_html( $payment->user_type ) ); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo esc_html( $payment->payment_date ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format( floatval( $payment->amount ), 2 ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst( esc_html( $payment->payment_mode ) ); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php
                        if ( $payment->user_type === 'expender' ) {
                            $label = ucfirst( esc_html( $payment->payment_type ) );
                            if ( $payment->payment_type === 'sender' && $payment->other_first_name ) {
                                $label .= ' to ' . esc_html( $payment->other_first_name . ' ' . $payment->other_last_name );
                            }
                            echo $label;
                        } else {
                            echo ucfirst( esc_html( $payment->payment_receive ) );
                        }
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="<?php echo esc_url( $edit_link ); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a> | <a href="#" class="text-red-600 hover:text-red-900">Delete</a></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="6" class="text-center py-4">No payments found for this filter.</td></tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success( [ 'html' => $html ] );
    }
}