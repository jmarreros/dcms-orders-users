<?php

namespace dcms\orders\includes;

use dcms\orders\includes\Database;

class Orders{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_list_orders',[ $this, 'dcms_list_orders' ]);
    }

    // Get current user's object orders
    public function get_object_orders(int $page = 1){

        $id_user = get_current_user_id();
        if ( ! $id_user ) return false;

        $args = [ 'customer_id' => $id_user,
                  'paginate' => true,
                  'limit' => 2,
                  'paged' => $page
                ];

        $orders = wc_get_orders($args);

        return $orders;
    }

    // List orders by user
    public function dcms_list_orders(){
          // Validate nonce
        $this->validate_nonce('ajax-nonce-orders');

        $res = [];
        $page  = $_POST['page']??1;


        $db = new Database();
        $items = $this->get_object_orders();

        $data = [];
        foreach ($items->orders as $key => $item) {
            $order_id = $item->get_id();

            $data[$key]['id'] = $order_id;
            $data[$key]['status'] = wc_get_order_status_name( $item->get_status() );
            $data[$key]['date'] = wc_format_datetime( $item->get_date_created() );
            $data[$key]['total'] = wc_price($item->get_total());
            $data[$key]['deposit'] = $db->order_has_deposits($order_id);
        }


        $res = [
            'status' => 1,
            'message' => "Página $page",
            'data' => $data
        ];

        echo json_encode($res);
        wp_die();
    }


    // Aux - Security, verify nonce
    private function validate_nonce( $nonce_name ){
        if ( ! wp_verify_nonce( $_POST['nonce'], $nonce_name ) ) {
            $res = [
                'status' => 0,
                'message' => '✋ Error nonce validation!!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

}