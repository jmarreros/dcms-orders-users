<?php

namespace dcms\orders\includes;

class Database{

    private $wpdb;
    private $table_post;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_post   = $this->wpdb->prefix.'posts';
    }


    // Validate if an order has partial payments enable
    public function order_has_deposits($id_order):bool{
        $sql = "SELECT COUNT(ID) FROM {$this->table_post}
                WHERE post_parent = {$id_order} AND post_type = 'wcdp_payment'";

        return boolval( $this->wpdb->get_var($sql) );
    }

    // Get parameters url payment for the next partial payment
    public function data_partial_payment($id_order){
        $sql = "SELECT ID, post_password AS key_url FROM {$this->table_post}
                WHERE post_parent = {$id_order}
                        AND post_type = 'wcdp_payment'
                        AND post_status = 'wc-pending'
                ORDER BY ID LIMIT 1";

        return $this->wpdb->get_row($sql, ARRAY_A);
    }

    // Get total pending payment partial deposits
    public function get_total_payment_pending($order_id){
        $amount_pending = 0;
        $args = [
            'type' => 'wcdp_payment',
            'parent' => $order_id,
            'status' => 'pending' //Added status filter
        ];

        $orders = wc_get_orders( $args );

        if ( $orders ) {
            foreach ($orders as $order) {
                $total = $order->get_total();
                $amount_pending += $total;
            }
        }

        return $amount_pending;
    }
}


// $status = $order->get_status();
//if ( $status == 'pending' ) { //|| $status == 'on-hold'
//}
