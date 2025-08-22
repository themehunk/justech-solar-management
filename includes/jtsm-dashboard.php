<?php

class JTSM_Solar_Management_Dashboard {

    private static $instance = null;

    public static function instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render dashboard page with totals and profit information.
     */
    public function jtsm_render_dashboard_page() {
        global $wpdb;

        $clients_table  = $wpdb->prefix . 'jtsm_clients';
        $payments_table = $wpdb->prefix . 'jtsm_payments';

        $total_seller_amount = $wpdb->get_var(
            "SELECT SUM(p.amount) FROM $payments_table p JOIN $clients_table c ON p.client_id = c.id WHERE c.user_type = 'seller'"
        );

        $total_expender_amount = $wpdb->get_var(
            "SELECT SUM(p.amount) FROM $payments_table p JOIN $clients_table c ON p.client_id = c.id WHERE c.user_type = 'expender'"
        );

        $total_consumer_amount = $wpdb->get_var(
            "SELECT SUM(p.amount) FROM $payments_table p JOIN $clients_table c ON p.client_id = c.id WHERE c.user_type = 'consumer'"
        );

        $total_proposal_amount = $wpdb->get_var("SELECT SUM(proposal_amount) FROM $clients_table");

        $total_profit = floatval( $total_consumer_amount ) - ( floatval( $total_seller_amount ) + floatval( $total_expender_amount ) );
        $total_remaining = floatval( $total_proposal_amount ) - floatval( $total_consumer_amount );

        ?>
        <div class="wrap bg-gray-100 p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4"><?php _e('Dashboard', 'jtsm'); ?></h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Seller Amount', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( floatval( $total_seller_amount ), 2 ); ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Expender Amount', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( floatval( $total_expender_amount ), 2 ); ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Consumer Amount', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( floatval( $total_consumer_amount ), 2 ); ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Proposal Amount', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( floatval( $total_proposal_amount ), 2 ); ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Profit', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( $total_profit, 2 ); ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase"><?php _e('Total Consumer Paid Remaining', 'jtsm'); ?></p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format( $total_remaining, 2 ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

