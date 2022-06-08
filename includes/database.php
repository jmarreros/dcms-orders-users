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
        $sql = "SELECT ID, post_password AS key_url
                FROM {$this->table_post}
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


    // Verify if a order has flexible price product
    public function order_flexible_payment($order_id){
      if ( ! defined(DCMS_PARENT_ID_PRODUCT_MULTI_PRICES) ) {
        define(DCMS_PARENT_ID_PRODUCT_MULTI_PRICES, 'dcms-parent-product-multi-prices');
      }

      $id_product = get_option(DCMS_PARENT_ID_PRODUCT_MULTI_PRICES);

      $sql = "SELECT
              oi.order_id,
              oimc.meta_value AS course_id,
              oimcp.meta_value AS course_price,
              oimcm.meta_value AS course_currency
              FROM {$this->wpdb->prefix}woocommerce_order_itemmeta oim
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_items oi ON oim.order_item_id = oi.order_item_id
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimc ON oimc.order_item_id = oi.order_item_id AND oimc.meta_key = 'curso_id'
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimcp ON oimcp.order_item_id = oi.order_item_id AND oimcp.meta_key = 'curso_precio'
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimcm ON oimcm.order_item_id = oi.order_item_id AND oimcm.meta_key = 'curso_moneda'
              WHERE oim.meta_key = '_product_id'
              AND oim.meta_value = {$id_product}
              AND oi.order_id = {$order_id}";

      return $this->wpdb->get_row($sql);

      //TODO
      // Verificar todos los pagos flexibles que tiene un usuario
      // por curso y acumulado para ver el pendiente de pago flexible
    }

}

