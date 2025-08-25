<?php


final class JTSM_Solar_Management_Setup {

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {




        add_action( 'admin_menu', [ $this, 'jtsm_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'jtsm_enqueue_scripts' ] );
        add_action( 'wp_ajax_jtsm_get_client_type_table', [ $this, 'jtsm_get_client_type_ajax_handler' ] );
        add_action( 'wp_ajax_jtsm_search_clients', [ JTSM_Solar_Management_List_View::instance(), 'jtsm_ajax_search_clients' ] );
        add_action( 'wp_ajax_jtsm_search_payments', [ JTSM_Solar_Management_List_View::instance(), 'jtsm_ajax_search_payments' ] );
    }

        public function role_init() {

                add_role( 'thod_admin', 'JusTech Admin', [] );

                // Copy caps from Administrator
                $admin = get_role( 'administrator' );
                $thod  = get_role( 'thod_admin' );

                if ( $admin && $thod ) {
                    foreach ( (array) $admin->capabilities as $cap => $grant ) {
                        $thod->add_cap( $cap, $grant );
                    }
                }

        }


    /**
     * Enqueue scripts and styles.
     */
    public function jtsm_enqueue_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'jtsm-') === false) {
            return;
        }

        wp_enqueue_style( 'admin_jtsm_style', JTSM_PLUGIN_URL . 'assets/css/style.css', array(), wp_get_theme()->get( 'Version' ), 'all' );

        // Enqueue Tailwind CSS
        wp_enqueue_script('tailwindcss', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', array('jquery'), JTSM_VERSION, true);

        // Enqueue JS for dynamic forms
        wp_enqueue_script( 'jtsm-admin-js', JTSM_PLUGIN_URL . 'assets/js/admin-custom-tables.js', [ 'jquery' ], JTSM_VERSION, true );
        wp_localize_script( 'jtsm-admin-js', 'jtsm_ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce('jtsm_ajax_nonce') ] );
        wp_enqueue_script( 'jtsm-search-js', JTSM_PLUGIN_URL . 'assets/js/jtsm-search.js', [ 'jquery', 'jtsm-admin-js' ], JTSM_VERSION, true );
    }

    /**
     * AJAX handler to get client type.
     */
    public function jtsm_get_client_type_ajax_handler() {
        check_ajax_referer('jtsm_ajax_nonce');
        global $wpdb;
        if ( isset( $_POST['client_id'] ) ) {
            $client_id = intval( $_POST['client_id'] );
            $user_type = $wpdb->get_var($wpdb->prepare("SELECT user_type FROM {$wpdb->prefix}jtsm_clients WHERE id = %d", $client_id));
            wp_send_json_success( [ 'user_type' => $user_type ] );
        }
        wp_send_json_error();
    }


    /**
     * Create custom database tables on plugin activation.
     */
    public function jtsm_activate() {

        $this->role_init();
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $clients_table_name = $wpdb->prefix . 'jtsm_clients';
        $sql_clients = "CREATE TABLE $clients_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name tinytext NOT NULL,
            last_name tinytext,
            company_name tinytext,
            contact_number varchar(20) DEFAULT '' NOT NULL,
            email varchar(100) DEFAULT '' NOT NULL,
            short_description text,
            address text,
            city tinytext,
            state tinytext,
            country tinytext,
            user_type varchar(20) DEFAULT 'consumer' NOT NULL,
            gstin varchar(15),
            product_service text,
            product_kw varchar(50),
            proposal_amount decimal(10,2),
            file_url varchar(255),
            status varchar(20) DEFAULT 'pending' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $payments_table_name = $wpdb->prefix . 'jtsm_payments';
        $sql_payments = "CREATE TABLE $payments_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            client_id mediumint(9) NOT NULL,
            installment varchar(20),
            amount decimal(10, 2),
            amount_without_gst decimal(10, 2),
            gst_rate tinyint(4),
            amount_with_gst decimal(10, 2),
            payment_mode varchar(20),
            payment_receive varchar(100),
            payment_date date,
            invoice_url varchar(255),
            expense_service text,
            payment_type varchar(20),
            other_client_id mediumint(9),
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_clients );
        dbDelta( $sql_payments );
    }

    /**
     * Add admin menu pages.
     */
    public function jtsm_admin_menu() {
        add_menu_page('Solar Management', 'Solar Management', 'thod_admin', 'jtsm-main-menu', [ JTSM_Solar_Management_List_View::instance(), 'jtsm_render_client_list_page' ], 'dashicons-solar-panel', 20);
       add_submenu_page('jtsm-main-menu', 'Dashboard', 'Dashboard', 'thod_admin', 'jtsm-dashboard', [ JTSM_Solar_Management_Dashboard::instance(), 'jtsm_render_dashboard_page' ]);
       add_submenu_page('jtsm-main-menu', 'All Clients', 'All Clients', 'thod_admin', 'jtsm-main-menu',  [JTSM_Solar_Management_List_View::instance(),'jtsm_render_client_list_page' ]);
       add_submenu_page('jtsm-main-menu', 'Add New Client', 'Add New Client', 'thod_admin', 'jtsm-add-client',   [JTSM_Solar_Management_CRUD::instance(),'jtsm_render_add_client_page' ]);
       add_submenu_page('jtsm-main-menu', 'All Payments', 'All Payments', 'thod_admin', 'jtsm-all-payments',  [JTSM_Solar_Management_List_View::instance(),'jtsm_render_payment_list_page' ]);
       add_submenu_page('jtsm-main-menu', 'Add New Payment', 'Add New Payment', 'thod_admin', 'jtsm-add-payment',  [JTSM_Solar_Management_CRUD::instance(),'jtsm_render_add_payment_page' ]);
   
   
       add_submenu_page(null, 'View Client', 'View Client', 'thod_admin', 'jtsm-view-client', 'jtsm_render_view_client_page');
       add_submenu_page(null, 'Edit Client', 'Edit Client', 'thod_admin', 'jtsm-edit-client', [ JTSM_Solar_Management_CRUD::instance(), 'jtsm_render_edit_client_page' ]);
       add_submenu_page(null, 'Edit Payment', 'Edit Payment', 'thod_admin', 'jtsm-edit-payment', [ JTSM_Solar_Management_CRUD::instance(), 'jtsm_render_edit_payment_page' ]);
   
   
    }


}


