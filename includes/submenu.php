<?php

namespace dcms\orders\includes;

use dcms\orders\reports\Process;
use dcms\orders\includes\Enqueue;

/**
 * Class for creating a dashboard submenu
 */
class Submenu{
    // Constructor
    public function __construct(){
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    // Register submenu
    public function register_submenu(){
        add_submenu_page(
            DCMS_ORDERS_SUBMENU,
            __('Orders Users Details','dcms-orders-users'),
            __('Orders Users Details','dcms-orders-users'),
            'manage_options',
            'orders-users',
            [$this, 'submenu_page_callback']
        );
    }

    // Callback, show view
    public function submenu_page_callback(){
        Enqueue::enqueue_scripts_backend();
        wp_enqueue_style('dcms-admin-order');

        include_once (DCMS_ORDERS_PATH. '/views/backend/main-screen.php');
    }
}