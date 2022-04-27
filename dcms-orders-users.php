<?php
/*
Plugin Name: Orders Users Details
Plugin URI: https://decodecms.com
Description: Plugin orders users, show details in front end with a shortcode, integrates with woocommerce depostis plugin
Version: 1.0
Author: Jhon Marreros Guzmán
Author URI: https://decodecms.com
Text Domain: dcms-orders-users
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\orders;

use dcms\orders\includes\Plugin;
use dcms\orders\includes\Enqueue;
use dcms\orders\includes\Submenu;
use dcms\orders\includes\Shortcode;
use dcms\orders\includes\Orders;
use dcms\orders\includes\Attachment;
use dcms\orders\reports\Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class to handle settings constants and loading files
**/
final class Loader{

	// Define all the constants we need
	public function define_constants(){
		define ('DCMS_ORDERS_VERSION', '1.0');
		define ('DCMS_ORDERS_PATH', plugin_dir_path( __FILE__ ));
		define ('DCMS_ORDERS_URL', plugin_dir_url( __FILE__ ));
		define ('DCMS_ORDERS_BASE_NAME', plugin_basename( __FILE__ ));
		define ('DCMS_ORDERS_SUBMENU', 'options-general.php');
		define ('DCMS_ORDERS_KEY_META', '_uploaded_files');
		define ('DCMS_UPLOAD_FOLDER', 'uploads/archivos-subidos/'); // inside wp-content folder
	}

	// Load all the files we need
	public function load_includes(){
		include_once ( DCMS_ORDERS_PATH . '/helpers/helper.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/plugin.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/submenu.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/shortcode.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/orders.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/database.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/enqueue.php');
		include_once ( DCMS_ORDERS_PATH . '/includes/attachment.php');
		include_once ( DCMS_ORDERS_PATH . '/backend/reports/database.php');
		include_once ( DCMS_ORDERS_PATH . '/backend/reports/process.php');
	}

	// Load tex domain
	public function load_domain(){
		add_action('plugins_loaded', function(){
			$path_languages = dirname(DCMS_ORDERS_BASE_NAME).'/languages/';
			load_plugin_textdomain('dcms-orders-users', false, $path_languages );
		});
	}

	// Add link to plugin list
	public function add_link_plugin(){
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ){
			return array_merge( array(
				'<a href="' . esc_url( admin_url( DCMS_ORDERS_SUBMENU . '?page=orders-users' ) ) . '">' . __( 'Settings', 'dcms-orders-users' ) . '</a>'
			), $links );
		} );
	}

	// Initialize all
	public function init(){
		$this->define_constants();
		$this->load_includes();
		$this->load_domain();
		$this->add_link_plugin();
		new Plugin();
		new Enqueue();
		new SubMenu();
		new Shortcode();
		new Orders();
		new Attachment();
		new Process();
	}

}

$dcms_orders_process = new Loader();
$dcms_orders_process->init();
