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
    }


    // Get all flexible payments by user and course, for calcultate pending
    public function order_flexible_payment_user_course($user_id, $course_id){
      $id_product = get_option(DCMS_PARENT_ID_PRODUCT_MULTI_PRICES);

      $sql = "SELECT
              oi.order_id,
              oimt.meta_value total,
              pmc.meta_value currency
              FROM {$this->wpdb->prefix}woocommerce_order_itemmeta oim
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_items oi ON oim.order_item_id = oi.order_item_id
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimc ON oimc.order_item_id = oi.order_item_id AND oimc.meta_key = 'curso_id'
              INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimt ON oimt.order_item_id = oi.order_item_id AND oimt.meta_key = '_line_total'
              INNER JOIN {$this->wpdb->prefix}postmeta pm ON pm.post_id = oi.order_id AND pm.meta_key = '_customer_user'
              INNER JOIN {$this->wpdb->prefix}postmeta pmc ON pmc.post_id = oi.order_id AND pmc.meta_key = '_order_currency'
              INNER JOIN {$this->wpdb->prefix}posts p ON pm.post_id = p.ID
              WHERE oim.meta_key = '_product_id'
              AND oim.meta_value = {$id_product}
              AND oimc.meta_value = {$course_id}
              AND pm.meta_value = {$user_id}
              AND p.post_status IN ('wc-completed','wc-on-hold','wc-partially-paid','wc-processing')
              ORDER BY oi.order_id DESC";

      return $this->wpdb->get_results($sql);

    }


    // Get all user orders by course
    public function get_courses_order_user($user_id){
        $id_product = get_option(DCMS_PARENT_ID_PRODUCT_MULTI_PRICES);

        // Get flexible product name
        $sql = "SELECT post_title FROM {$this->wpdb->posts} WHERE id = {$id_product}";
        $flexible_product_name = $this->wpdb->get_var($sql);

        // Rehusable subquery
        $sql_subquery = "SELECT ID FROM {$this->wpdb->prefix}posts p 
                        INNER JOIN {$this->wpdb->prefix}postmeta pm ON p.ID = pm.post_id
                        WHERE p.post_type = 'shop_order' AND pm.meta_key = '_customer_user' AND pm.meta_value = {$user_id}";

        $sql = "SELECT oi.order_id, oi.order_item_id, oi.order_item_name course_name, 0 flexible 
                FROM {$this->wpdb->prefix}woocommerce_order_items oi
                INNER JOIN ({$sql_subquery}) o ON o.ID = oi.order_id
                WHERE order_item_name != '{$flexible_product_name}'
                UNION
                SELECT oi.order_id, oi.order_item_id, oim.meta_value course_name, 1 flexible
                FROM {$this->wpdb->prefix}woocommerce_order_items oi
                INNER JOIN ({$sql_subquery}) o ON o.ID = oi.order_id
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oim
                ON oi.order_item_id = oim.order_item_id
                WHERE oi.order_item_name = '{$flexible_product_name}' AND meta_key = 'curso_nombre'
                ORDER BY order_id DESC";

        return $this->wpdb->get_results($sql);
    }

    // Get order status and currency
    public function get_basic_order_info($order_id){
        $sql = "SELECT p.post_status, meta_value order_currency  
                FROM {$this->wpdb->prefix}posts p
                INNER JOIN {$this->wpdb->prefix}postmeta pm 
                ON p.ID = pm.post_id
                WHERE p.ID = {$order_id} AND pm.meta_key = '_order_currency'";
        
        return $this->wpdb->get_row($sql, ARRAY_A);
    }

    // Get item order total and product id
    public function get_basic_item_order_info($item_order_id){
        $sql = "SELECT meta_key, meta_value 
                FROM {$this->wpdb->prefix}woocommerce_order_itemmeta 
                WHERE order_item_id = {$item_order_id} AND ( meta_key = '_line_total' OR  meta_key = '_product_id' )";
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

}

