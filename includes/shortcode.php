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

        $order_id = $_GET["order"]??0;
        $current_url = Helper::get_current_url();

        if ( ! $order_id ){ // Show orders
            $this->enqueue_scripts_orders();
            ob_start();
                include_once DCMS_ORDERS_PATH.'views/templates/list-orders.php';
                $html_code = ob_get_contents();
            ob_end_clean();

        } elseif ( $order_id ) { // Show order
            wp_enqueue_style('dcms-orders-style');
            ob_start();
                include_once DCMS_ORDERS_PATH.'views/templates/order-detail.php';
                $html_code = ob_get_contents();
            ob_end_clean();
        }

        return $html_code;
    }

    // Enqueue orders
    private function enqueue_scripts_orders(){
        wp_enqueue_style('dcms-orders-style');
        wp_enqueue_script('dcms-orders-script');

        wp_localize_script('dcms-orders-script',
                            'dcmsOrders',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                              'nonce' => wp_create_nonce('ajax-nonce-orders')]);
    }

}





// public function show_user_sidebar($atts, $content){
//     $id_user = get_current_user_id();

//     if ( $id_user ){
//         $db = new Database();

//         wp_enqueue_style('event-style');
//         wp_enqueue_script('event-script');

//         $user = $db->show_user_sidebar($id_user);

//         if ( $user ):
//             $email  = Helper::search_field_in_meta($user, 'email');
//             $name   = Helper::search_field_in_meta($user, 'name') . ' ' . Helper::search_field_in_meta($user, 'lastname');
//             $number = Helper::search_field_in_meta($user, 'number');
//         endif;

//         $content = $content??'';
//         $email   = $email??'';
//         $name    = $name??'';
//         $number  = $number??'';

//         ob_start();
//             include_once DCMS_EVENT_PATH.'views/user-sidebar.php';
//             $html_code = ob_get_contents();
//         ob_end_clean();

//         return $html_code;
//     }
// }
