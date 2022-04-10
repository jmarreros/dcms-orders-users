<?php

namespace dcms\orders\includes;

use dcms\orders\includes\Orders;
use dcms\orders\includes\Database;
use dcms\orders\helpers\Helper;

// Clase para que crea un shortcode para la visualización de detalle de órdenes de un usuario
// El shortcode debe ser usadado en los archivos de la plantilla para que se ve en el área de cliente

Class Shortcode{

    public function __construct(){
        add_action('init', [$this, 'register_shortcode_orders_user']);
    }

    public function register_shortcode_orders_user(){
        add_shortcode('dcms_orders_user', [$this, 'create_orders_user']);
    }

    public function create_orders_user( $atts, $content ){

        $order_id = $_GET['order']??0;
        $action = $_GET['action']??'';

        $current_url = Helper::get_current_url();
        $html_code = "";

        // Enqueu general style
        wp_enqueue_style('dcms-orders-style');

        switch (true) {

            case ($order_id == 0): // List orders
                $this->enqueue_scripts_orders();
                ob_start();
                    include_once DCMS_ORDERS_PATH.'views/templates/list-orders.php';
                    $html_code = ob_get_contents();
                ob_end_clean();
                break;

            case ($order_id > 0 ):
                $order = Helper::get_order_user($order_id);

                if ( $order ){ // order exits and belongs to current user

                    switch ( $action ){
                        case '': // Specifict order
                            ob_start();
                                include_once DCMS_ORDERS_PATH.'views/templates/order-detail.php';
                                $html_code = ob_get_contents();
                            ob_end_clean();
                            break;

                        case 'attach': // Attachments order
                            $this->enqueue_scripts_attachment();
                            ob_start();
                                include_once DCMS_ORDERS_PATH.'views/templates/file-attachment.php';
                                $html_code = ob_get_contents();
                            ob_end_clean();
                            break;
                    }
                }
                break;
        }

        return $html_code;
    }

    // Enqueue orders
    private function enqueue_scripts_orders(){
        wp_enqueue_script('dcms-orders-script');

        wp_localize_script('dcms-orders-script',
                            'dcmsOrders',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                              'nonce' => wp_create_nonce('ajax-nonce-orders')]);
    }

    // Enqueue attachments
    private function enqueue_scripts_attachment(){
        wp_enqueue_script('dcms-attachment-script');

        wp_localize_script('dcms-attachment-script',
                            'dcmsAttach',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                              'nonce' => wp_create_nonce('ajax-nonce-attachment')]);
    }

}

