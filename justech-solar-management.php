<?php
/**
 * Plugin Name:       Justech Solar Management (Custom Tables)
 * Plugin URI:        https://example.com/
 * Description:       A plugin to manage solar clients and payments using custom database tables and Tailwind CSS.
 * Version:           2.4.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jtsm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Define constants for the plugin.
 */
define( 'JTSM_VERSION', '2.4.0' );
define( 'JTSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JTSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


// Include all the core plugin files
require_once JTSM_PLUGIN_DIR . 'includes/jtsm-crud.php';
require_once JTSM_PLUGIN_DIR . 'includes/jtsm-list-view.php';
require_once JTSM_PLUGIN_DIR . 'includes/view-client-detail.php';

require_once JTSM_PLUGIN_DIR . 'includes/jtsm-setup.php';






function jtsm_run_plugin_setup() {
     $obj = JTSM_Solar_Management_Setup::instance();


    register_activation_hook( __FILE__, [  $obj, 'jtsm_activate' ] );

    return $obj;

}
jtsm_run_plugin_setup();