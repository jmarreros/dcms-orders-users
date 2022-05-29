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
            __('Reporte Órdenes Usuarios','dcms-orders-users'),
            __('Reporte Órdenes Usuarios','dcms-orders-users'),
            'manage_options',
            DCMS_ORDERS_MAINPAGE,
            [$this, 'submenu_page_callback'],
            0
        );
    }

    // Callback, show view
    public function submenu_page_callback(){
        wp_enqueue_style('dcms-admin-order');

        if ( isset($_GET['id_course']) && isset($_GET['course_name']) ){
            $id_products = explode('-', $_GET['id_products']??'');
            $course_name = $_GET['course_name'];
            $id_course = intval($_GET['id_course']);

            $process = new Process;
            $items = $process->get_detail_course($id_course, $id_products);

            include_once (DCMS_ORDERS_PATH. '/views/backend/course-detail.php');
        } else {
            Enqueue::enqueue_scripts_backend();
            include_once (DCMS_ORDERS_PATH. '/views/backend/main-screen.php');
        }
    }
}