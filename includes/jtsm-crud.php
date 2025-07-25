<?php 

class JTSM_Solar_Management_CRUD {


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
     * Render the page to add a new client.
     */
    public function jtsm_render_add_client_page() {
        if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['jtsm_add_client_nonce'] ) && wp_verify_nonce( $_POST['jtsm_add_client_nonce'], 'jtsm_add_client_action' ) ) {
            $this->jtsm_save_client_data();
        }
        ?>
        <div class="wrap bg-gray-100 p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4"><?php _e('Add New Client', 'jtsm'); ?></h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form method="POST" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'jtsm_add_client_action', 'jtsm_add_client_nonce' ); ?>
                    <div class="grid grid-cols-6 md:grid-cols-6 gap-6">

                    <div class="md:col-span-2"><label for="jtsm_user_type" class="block text-sm font-medium text-gray-700">Type of User</label><select name="jtsm_user_type" id="jtsm_user_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><option value="consumer">Consumer</option><option value="seller">Seller</option><option value="expanse">Expanse</option></select></div>

                    <div class="md:col-span-4"><label for="jtsm_company_name" class="jtsm-seller block text-sm font-medium text-gray-700">Company Name</label><input type="text" name="jtsm_company_name" id="jtsm_company_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>


                        <div class="md:col-span-3"><label for="jtsm_first_name" class="block text-sm font-medium text-gray-700">First Name</label><input type="text" name="jtsm_first_name" id="jtsm_first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>

                        <div class="md:col-span-3"><label for="jtsm_last_name" class="block text-sm font-medium text-gray-700">Last Name</label><input type="text" name="jtsm_last_name" id="jtsm_last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>

                        <div class="md:col-span-3"><label for="jtsm_email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="jtsm_email" id="jtsm_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>



                        <div class="md:col-span-3"><label for="jtsm_contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label><input type="text" name="jtsm_contact_number" id="jtsm_contact_number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>

                        <div class="md:col-span-3 jtsm-expanse"><label for="jtsm_short_description" class="block text-sm font-medium text-gray-700">Short Description</label><textarea name="jtsm_short_description" id="jtsm_short_description" class="h-24 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea></div>


                        <div class="md:col-span-2">
                        <label for="product_service" class="block text-sm font-medium text-gray-700">Service Name</label>
                        <input type="text" name="product_service" id="product_service" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-2">
                        <label for="product_kw" class="block text-sm font-medium text-gray-700">Panel K/W</label>
                        <input type="text" name="product_kw" id="product_kw" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-2">
                        <label for="proposal_amount" class="block text-sm font-medium text-gray-700">Proposal Amount</label>
                        <input type="text" name="proposal_amount" id="proposal_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>


                        <div class="md:col-span-3"><label for="jtsm_address" class="block text-sm font-medium text-gray-700">Address</label><textarea name="jtsm_address" id="jtsm_address" class="h-24 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea></div>

             


                        <div class="md:col-span-3"><label for="jtsm_file_upload" class="block text-sm font-medium text-gray-700">File Upload</label><input type="file" name="jtsm_file_upload" id="jtsm_file_upload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"></div>
                    </div>
                    <div class="mt-6">
                        <?php submit_button('Add Client', 'primary', 'submit', true, ['class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Save client data to the custom table.
     */
    private function jtsm_save_client_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jtsm_clients';
        
        $data = [
            'first_name' => sanitize_text_field($_POST['jtsm_first_name']),
            'last_name' => sanitize_text_field($_POST['jtsm_last_name']),
            'company_name' => sanitize_text_field($_POST['jtsm_company_name']),
            'contact_number' => sanitize_text_field($_POST['jtsm_contact_number']),
            'short_description' => sanitize_textarea_field($_POST['jtsm_short_description']),
            'product_service' => sanitize_text_field($_POST['product_service']),
            'product_kw' => sanitize_text_field($_POST['product_kw']),
            'proposal_amount' => floatval($_POST['proposal_amount']),
            'email' => sanitize_email($_POST['jtsm_email']),
            'address' => sanitize_textarea_field($_POST['jtsm_address']),
            'user_type' => sanitize_text_field($_POST['jtsm_user_type']),
        ];

        if ( ! empty( $_FILES['jtsm_file_upload']['name'] ) ) {
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            $uploadedfile = $_FILES['jtsm_file_upload'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                $data['file_url'] = $movefile['url'];
            }
        }

        $result = $wpdb->insert($table_name, $data);

        if ($result) {
            echo '<div class="notice notice-success is-dismissible"><p>Client added successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>There was an error adding the client. Please try again.</p></div>';
        }
    }

    /**
     * Render the page to edit an existing client.
     */
    public function jtsm_render_edit_client_page() {
        if ( ! isset( $_GET['client_id'] ) ) {
            echo '<div class="wrap"><div class="notice notice-error"><p>No client specified.</p></div></div>';
            return;
        }

        global $wpdb;
        $client_id  = intval( $_GET['client_id'] );
        $table_name = $wpdb->prefix . 'jtsm_clients';
        $client     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $client_id ) );

        if ( ! $client ) {
            echo '<div class="wrap"><div class="notice notice-error"><p>Client not found.</p></div></div>';
            return;
        }

        if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['jtsm_edit_client_nonce'] ) && wp_verify_nonce( $_POST['jtsm_edit_client_nonce'], 'jtsm_edit_client_action' ) ) {
            $this->jtsm_update_client_data( $client_id, $client );
            $client = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $client_id ) );
        }

        ?>
        <div class="wrap bg-gray-100 p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4"><?php _e('Edit Client', 'jtsm'); ?></h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form method="POST" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'jtsm_edit_client_action', 'jtsm_edit_client_nonce' ); ?>
                    <div class="grid grid-cols-6 md:grid-cols-6 gap-6">

                        <div class="md:col-span-2">
                            <label for="jtsm_user_type" class="block text-sm font-medium text-gray-700">Type of User</label>
                            <select name="jtsm_user_type" id="jtsm_user_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="consumer" <?php selected( $client->user_type, 'consumer' ); ?>>Consumer</option>
                                <option value="seller" <?php selected( $client->user_type, 'seller' ); ?>>Seller</option>
                                <option value="expanse" <?php selected( $client->user_type, 'expanse' ); ?>>Expanse</option>
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label for="jtsm_company_name" class="jtsm-seller block text-sm font-medium text-gray-700">Company Name</label>
                            <input type="text" name="jtsm_company_name" id="jtsm_company_name" value="<?php echo esc_attr( $client->company_name ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="jtsm_first_name" id="jtsm_first_name" value="<?php echo esc_attr( $client->first_name ); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="jtsm_last_name" id="jtsm_last_name" value="<?php echo esc_attr( $client->last_name ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="jtsm_email" id="jtsm_email" value="<?php echo esc_attr( $client->email ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <input type="text" name="jtsm_contact_number" id="jtsm_contact_number" value="<?php echo esc_attr( $client->contact_number ); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3 jtsm-expanse">
                            <label for="jtsm_short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                            <textarea name="jtsm_short_description" id="jtsm_short_description" class="h-24 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo esc_textarea( $client->short_description ); ?></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="product_service" class="block text-sm font-medium text-gray-700">Service Name</label>
                            <input type="text" name="product_service" id="product_service" value="<?php echo esc_attr( $client->product_service ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label for="product_kw" class="block text-sm font-medium text-gray-700">Panel K/W</label>
                            <input type="text" name="product_kw" id="product_kw" value="<?php echo esc_attr( $client->product_kw ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label for="proposal_amount" class="block text-sm font-medium text-gray-700">Proposal Amount</label>
                            <input type="text" name="proposal_amount" id="proposal_amount" value="<?php echo esc_attr( $client->proposal_amount ); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="jtsm_address" id="jtsm_address" class="h-24 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo esc_textarea( $client->address ); ?></textarea>
                        </div>

                        <div class="md:col-span-3">
                            <label for="jtsm_file_upload" class="block text-sm font-medium text-gray-700">File Upload</label>
                            <input type="file" name="jtsm_file_upload" id="jtsm_file_upload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                            <?php if ( $client->file_url ) : ?>
                                <p class="mt-2 text-sm"><a href="<?php echo esc_url( $client->file_url ); ?>" target="_blank" class="text-indigo-600 hover:underline">View Current File</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-6">
                        <?php submit_button( 'Update Client', 'primary', 'submit', true, [ 'class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500' ] ); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Update client data in the custom table.
     */
    private function jtsm_update_client_data( $client_id, $client ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jtsm_clients';

        $data = [
            'first_name'      => sanitize_text_field( $_POST['jtsm_first_name'] ),
            'last_name'       => sanitize_text_field( $_POST['jtsm_last_name'] ),
            'company_name'    => sanitize_text_field( $_POST['jtsm_company_name'] ),
            'contact_number'  => sanitize_text_field( $_POST['jtsm_contact_number'] ),
            'short_description' => sanitize_textarea_field( $_POST['jtsm_short_description'] ),
            'product_service' => sanitize_text_field( $_POST['product_service'] ),
            'product_kw'      => sanitize_text_field( $_POST['product_kw'] ),
            'proposal_amount' => floatval( $_POST['proposal_amount'] ),
            'email'           => sanitize_email( $_POST['jtsm_email'] ),
            'address'         => sanitize_textarea_field( $_POST['jtsm_address'] ),
            'user_type'       => sanitize_text_field( $_POST['jtsm_user_type'] ),
        ];

        if ( ! empty( $_FILES['jtsm_file_upload']['name'] ) ) {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            $uploadedfile     = $_FILES['jtsm_file_upload'];
            $upload_overrides = [ 'test_form' => false ];
            $movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $data['file_url'] = $movefile['url'];
            }
        }

        $result = $wpdb->update( $table_name, $data, [ 'id' => $client_id ] );

        if ( false !== $result ) {
            echo '<div class="notice notice-success is-dismissible"><p>Client updated successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>There was an error updating the client.</p></div>';
        }
    }


     /**
     * Render the page to add a new payment.
     */
    public function jtsm_render_add_payment_page() {
        if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['jtsm_add_payment_nonce'] ) && wp_verify_nonce( $_POST['jtsm_add_payment_nonce'], 'jtsm_add_payment_action' ) ) {
            $this->jtsm_save_payment_data();
        }

        global $wpdb;
        $clients = $wpdb->get_results("SELECT id, first_name, last_name FROM {$wpdb->prefix}jtsm_clients ORDER BY first_name ASC");
        ?>
        <div class="wrap bg-gray-100 p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4"><?php _e('Add New Payment', 'jtsm'); ?></h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form method="POST" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'jtsm_add_payment_action', 'jtsm_add_payment_nonce' ); ?>
                    
                    <div class="mb-6">
                        <label for="jtsm_client_id" class="block text-sm font-medium text-gray-700">Select Client</label>
                        <select name="jtsm_client_id" id="jtsm_client_id" required class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Select --</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo esc_attr($client->id); ?>"><?php echo esc_html($client->first_name . ' ' . $client->last_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                                
                    <div id="jtsm-consumer-form" class="hidden space-y-4">
                        
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Consumer Payment</h2>
                        <div><label for="jtsm_installment" class="block text-sm font-medium text-gray-700">Installment</label><select name="jtsm_installment" class="mt-1 block w-3xs rounded-md border-gray-300 shadow-sm"><option value="1">1st</option><option value="2">2nd</option><option value="3">3rd</option><option value="final">Final</option></select></div>
                        <div><label for="jtsm_amount" class="block text-sm font-medium text-gray-700">Amount</label><input type="number" step="0.01" name="jtsm_amount" class="mt-1 block w-3xs rounded-md border-gray-300 shadow-sm"></div>
                        <div><label for="jtsm_payment_mode_consumer" class="block text-sm font-medium text-gray-700">Payment Mode</label><select name="jtsm_payment_mode_consumer" class="mt-1 block w-3xs rounded-md border-gray-300 shadow-sm"><option value="upi">UPI</option><option value="cash">Cash</option><option value="netbanking">Net Banking</option><option value="other">Other</option></select></div>
                        <div><label for="jtsm_payment_receive_consumer" class="block text-sm font-medium text-gray-700">Payment Receive</label>
                    
                        <input type="text" name="jtsm_payment_receive_consumer" class="mt-1 block w-3xs rounded-md border-gray-300 shadow-sm" />
                    </div>
                    
                        <div><label for="jtsm_payment_date_consumer" class="block text-sm font-medium text-gray-700">Payment Date</label><input type="date" name="jtsm_payment_date_consumer" class="mt-1 block w-3xs rounded-md border-gray-300 shadow-sm"></div>
                    </div>

                    <div id="jtsm-seller-form" class="hidden space-y-4">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Seller Payment</h2>
                        <div><label for="jtsm_amount_without_gst" class="block text-sm font-medium text-gray-700">Amount (w/o GST)</label><input type="number" step="0.01" id="jtsm_amount_without_gst" name="jtsm_amount_without_gst" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                        <div><label for="jtsm_gst_rate" class="block text-sm font-medium text-gray-700">GST Rate</label><select id="jtsm_gst_rate" name="jtsm_gst_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="0">0%</option><option value="6">6%</option><option value="12">12%</option><option value="18">18%</option><option value="28">28%</option></select></div>
                        <div><label for="jtsm_amount_with_gst" class="block text-sm font-medium text-gray-700">Total Amount (with GST)</label><input type="text" id="jtsm_amount_with_gst" name="jtsm_amount_with_gst" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"></div>
                        <div><label for="jtsm_invoice_upload" class="block text-sm font-medium text-gray-700">Invoice Upload</label><input type="file" name="jtsm_invoice_upload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"></div>
                        <div><label for="jtsm_payment_mode_seller" class="block text-sm font-medium text-gray-700">Payment Mode</label><select name="jtsm_payment_mode_seller" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="upi">UPI</option><option value="cash">Cash</option><option value="netbanking">Net Banking</option><option value="other">Other</option></select></div>
                        <div><label for="jtsm_payment_date_seller" class="block text-sm font-medium text-gray-700">Payment Date</label><input type="date" name="jtsm_payment_date_seller" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                    </div>

                    <div id="jtsm-expanse-form" class="hidden space-y-4">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Expanse Payment</h2>
                        <div><label for="jtsm_expanse_service" class="block text-sm font-medium text-gray-700">Service</label><input type="text" name="jtsm_expanse_service" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                        <div><label for="jtsm_expanse_amount" class="block text-sm font-medium text-gray-700">Amount</label><input type="number" step="0.01" name="jtsm_expanse_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                        <div><label for="jtsm_payment_mode_expanse" class="block text-sm font-medium text-gray-700">Payment Mode</label><select name="jtsm_payment_mode_expanse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="upi">UPI</option><option value="cash">Cash</option><option value="netbanking">Net Banking</option><option value="other">Other</option></select></div>
                        <div><label for="jtsm_payment_type_expanse" class="block text-sm font-medium text-gray-700">Payment Type</label><select name="jtsm_payment_type_expanse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="receiver">Receiver</option><option value="sender">Sender</option></select></div>
                        <div><label for="jtsm_payment_date_expanse" class="block text-sm font-medium text-gray-700">Payment Date</label><input type="date" name="jtsm_payment_date_expanse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                    </div>

                    <div id="jtsm-submit-button-container" class="mt-6 hidden">
                        <?php submit_button('Add Payment', 'primary', 'submit', true, ['class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }



     /**
     * Save payment data to the custom table.
     */
    private function jtsm_save_payment_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jtsm_payments';
        $client_id = intval($_POST['jtsm_client_id']);
        $client = $wpdb->get_row($wpdb->prepare("SELECT user_type FROM {$wpdb->prefix}jtsm_clients WHERE id = %d", $client_id));
        
        if (!$client) {
            echo '<div class="notice notice-error is-dismissible"><p>Invalid client selected.</p></div>';
            return;
        }

        $data = ['client_id' => $client_id];

        if ($client->user_type === 'consumer') {
            $data['installment'] = sanitize_text_field($_POST['jtsm_installment']);
            $data['amount'] = floatval($_POST['jtsm_amount']);
            $data['payment_mode'] = sanitize_text_field($_POST['jtsm_payment_mode_consumer']);
            $data['payment_receive'] = sanitize_text_field($_POST['jtsm_payment_receive_consumer']);
            $data['payment_date'] = sanitize_text_field($_POST['jtsm_payment_date_consumer']);
        } elseif ($client->user_type === 'seller') {
            $data['amount_without_gst'] = floatval($_POST['jtsm_amount_without_gst']);
            $data['gst_rate'] = intval($_POST['jtsm_gst_rate']);
            $data['amount_with_gst'] = floatval($_POST['jtsm_amount_with_gst']);
            $data['payment_mode'] = sanitize_text_field($_POST['jtsm_payment_mode_seller']);
            $data['payment_date'] = sanitize_text_field($_POST['jtsm_payment_date_seller']);
            if ( ! empty( $_FILES['jtsm_invoice_upload']['name'] ) ) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }
                $uploadedfile = $_FILES['jtsm_invoice_upload'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    $data['invoice_url'] = $movefile['url'];
                }
            }
        } else { // Expanse
            $data['expense_service'] = sanitize_text_field($_POST['jtsm_expanse_service']);
            $data['amount'] = floatval($_POST['jtsm_expanse_amount']);
            $data['payment_mode'] = sanitize_text_field($_POST['jtsm_payment_mode_expanse']);
            $data['payment_type'] = sanitize_text_field($_POST['jtsm_payment_type_expanse']);
            $data['payment_date'] = sanitize_text_field($_POST['jtsm_payment_date_expanse']);
        }

        $result = $wpdb->insert($table_name, $data);
        if ($result) {
            echo '<div class="notice notice-success is-dismissible"><p>Payment added successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>There was an error adding the payment.</p></div>';
        }
    }



}

