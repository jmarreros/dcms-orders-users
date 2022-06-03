<?php

namespace dcms\orders\reports;


class Database{
    private $wpdb;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    // Main query, gets all courses with relevant info
    // Courses have two associate produts, id_product and url_product2
    // $dstart, $dend, $tcourse, filter parameters
    public function get_courses( $dstart, $dend, $tcourse ){
        $sql = "SELECT
                    p.ID AS id_course,
                    p.post_date AS date_course,
                    p.post_title AS name_course,
                    uc.count_students,
                    pmp.id_product,
                    pmu.url_product2
        FROM {$this->wpdb->prefix}posts p
        INNER JOIN (
            SELECT DISTINCT meta_value AS id_product, post_id
            FROM {$this->wpdb->prefix}postmeta WHERE meta_key = 'stm_lms_product_id'
        ) pmp ON pmp.post_id = p.ID
        LEFT JOIN (
            SELECT course_id, COUNT(user_id) AS count_students
            FROM {$this->wpdb->prefix}stm_lms_user_courses
            GROUP BY course_id
        ) uc ON uc.course_id = p.ID
        LEFT JOIN (
            SELECT DISTINCT post_id, meta_value AS url_product2
            FROM {$this->wpdb->prefix}postmeta
            WHERE  meta_key = 'WooCommerce_link_product'
        ) pmu ON pmu.post_id = p.id
        WHERE p.post_type = 'stm-courses'
        AND p.post_status = 'publish'";

        if ( ! empty($dstart) && empty($dend)){
            $sql .= " AND p.post_date >= '{$dstart}'";
        }

        if ( empty($dstart) && ! empty($dend)){
            $sql .= " AND p.post_date <= '{$dend}'";
        }

        if ( ! empty($dstart) &&  ! empty($dend) ){
            $sql .= " AND p.post_date BETWEEN '{$dstart}' AND '{$dend}'";
        }

        if ( ! empty($tcourse) ){
            $sql .= " AND p.post_title like '%{$tcourse}%'";
        }

        $sql .=" ORDER BY p.post_date DESC";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }


    // Get order items by two products ids
    public function get_items_orders_by_ids_product($ids_product, $include_user = false){
        $str_ids = implode(',', $ids_product);

        // Include user in the query
        $field_user = '';
        $inner_user = '';
        if ($include_user){
            $field_user = "pmn.meta_value AS user_name,pml.meta_value AS user_lastname,";
            $inner_user = "INNER JOIN {$this->wpdb->prefix}postmeta pmn ON pmn.post_id = p.ID AND pmn.meta_key = '_billing_first_name'
                           INNER JOIN {$this->wpdb->prefix}postmeta pml ON pml.post_id = p.ID AND pml.meta_key = '_billing_last_name'";
        }

        $sql ="SELECT
                oi.order_id,
                {$field_user}
                p.post_status,
                oi.order_item_id,
                oimd.meta_value deposit_info,
                oimt.meta_value item_total,
                pmc.meta_value currency,
                0 flexible
                FROM {$this->wpdb->prefix}woocommerce_order_items oi
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oim
                    ON oi.order_item_id = oim.order_item_id
                LEFT JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimd
                    ON oimd.order_item_id = oi.order_item_id AND oimd.meta_key = 'wc_deposit_meta'
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimt
                    ON oimt.order_item_id = oi.order_item_id AND oimt.meta_key = '_line_total'
                INNER JOIN {$this->wpdb->prefix}posts p
                    ON p.ID = oi.order_id
                INNER JOIN {$this->wpdb->prefix}postmeta pmc
                    ON pmc.post_id = oi.order_id AND pmc.meta_key = '_order_currency'
                {$inner_user}
                WHERE
                p.post_status IN ('wc-completed','wc-on-hold','wc-partially-paid','wc-processing')
                AND oi.order_item_type = 'line_item'
                AND oim.meta_key = '_product_id'
                AND oim.meta_value IN ({$str_ids})";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }


    // Get order items by flexible product price
    public function query_items_orders_flexible__product($id_course){
      $sql = "SELECT
                oi.order_id,
                pmid.meta_value user_id,
                pmn.meta_value user_name,
                pml.meta_value user_lastname,
                oimp.meta_value curso_precio,
                oimm.meta_value curso_moneda,
                oimt.meta_value item_total,
                pmc.meta_value currency,
                1 flexible
                FROM {$this->wpdb->prefix}woocommerce_order_items oi
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
                INNER JOIN {$this->wpdb->prefix}posts p ON p.ID = oi.order_id
                LEFT JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimp
                  ON oimp.order_item_id = oi.order_item_id AND oimp.meta_key = 'curso_precio'
                LEFT JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimm
                  ON oimm.order_item_id = oi.order_item_id AND oimm.meta_key = 'curso_moneda'
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oimt
                  ON oimt.order_item_id = oi.order_item_id AND oimt.meta_key = '_line_total'
                INNER JOIN {$this->wpdb->prefix}postmeta pmc ON pmc.post_id = oi.order_id AND pmc.meta_key = '_order_currency'
                INNER JOIN {$this->wpdb->prefix}postmeta pmn ON pmn.post_id = p.ID AND pmn.meta_key = '_billing_first_name'
                INNER JOIN {$this->wpdb->prefix}postmeta pml ON pml.post_id = p.ID AND pml.meta_key = '_billing_last_name'
                INNER JOIN {$this->wpdb->prefix}postmeta pmid ON pmid.post_id = p.ID AND pmid.meta_key = '_customer_user'
                WHERE
                  p.post_status IN ('wc-completed','wc-on-hold','wc-partially-paid','wc-processing')
                  AND order_item_type = 'line_item'
                  AND oim.meta_key = 'curso_id'
                  AND oim.meta_value = {$id_course}
                ORDER BY user_name, user_lastname, user_id, order_id";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function count_sub_orders_completed($parent_order_id){
        $sql = "SELECT COUNT(ID)
                FROM {$this->wpdb->prefix}posts
                WHERE
                post_parent = {$parent_order_id}
                AND post_status IN ('wc-completed', 'wc-on-hold')";

        return $this->wpdb->get_var($sql)??0;
    }

    // Get product id from course id
    public function get_id_product_from_course( $id_course ){
      $sql = "SELECT meta_value id_product
              FROM {$this->wpdb->prefix}postmeta
              WHERE post_id = {$id_course} AND meta_key = 'stm_lms_product_id' LIMIT 1";
      return $this->wpdb->get_var($sql)??0;
    }

    // Auxiliar function for getting id product form url, por product2
    public function get_product_id_from_url($product_url){
        preg_match('/producto\/(.+)\//', $product_url, $matches);
        $product_slug = $matches[1]??'';

        if ( $product_slug ){

            $sql = "SELECT ID
                    FROM {$this->wpdb->prefix}posts
                    WHERE post_name = '{$product_slug}'
                    AND post_type = 'product'";

            return $this->wpdb->get_var($sql)??0;
        }
        return 0;
    }


    // TODO
    public function get_amount_product_flexible_course_by_user(){
        $sql = "SELECT p.ID, oimt.meta_value item_total, pmc.meta_value currency, pmu.meta_value user_id
                FROM wp_woocommerce_order_itemmeta oim
                INNER JOIN wp_woocommerce_order_items oi ON oi.order_item_id = oim.order_item_id
                INNER JOIN wp_woocommerce_order_itemmeta oimt ON oimt.order_item_id = oi.order_item_id AND oimt.meta_key = '_line_total'
                INNER JOIN wp_posts p ON p.ID = oi.order_id
                INNER JOIN wp_postmeta pmc ON pmc.post_id = oi.order_id AND pmc.meta_key = '_order_currency'
                INNER JOIN wp_postmeta pmu ON pmu.post_id = oi.order_id AND pmu.meta_key = '_customer_user'
                WHERE
                p.post_status IN ('wc-completed','wc-on-hold','wc-partially-paid','wc-processing')
                AND oim.meta_key = 'curso_id' AND oim.meta_value = 6157
                AND pmu.meta_value = 7";
    }

}

