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
                    pmp.id_product,
                    uc.count_students,
                    pms.prices,
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
        INNER JOIN (
            SELECT pm.post_id, GROUP_CONCAT(pm.meta_value) AS prices
            FROM {$this->wpdb->prefix}postmeta pm
            INNER JOIN {$this->wpdb->prefix}posts p ON p.ID = pm.post_id
            WHERE p.post_type = 'stm-courses'
            AND p.post_status = 'publish'
            AND pm.meta_key IN ( 'price', 'sale_price' )
            GROUP BY post_id
        ) pms ON pms.post_id = p.ID
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

        error_log(print_r($sql,true));

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function get_items_orders_by_ids_product($ids_product){
        $str_ids = implode(',', $ids_product);

        $sql ="SELECT
                oi.order_id,
                p.post_status,
                oi.order_item_id,
                deposit_info,
                item_total
                FROM {$this->wpdb->prefix}woocommerce_order_items oi
                INNER JOIN {$this->wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
                LEFT JOIN (
                    SELECT order_item_id, meta_value AS deposit_info
                    FROM {$this->wpdb->prefix}woocommerce_order_itemmeta
                    WHERE meta_key = 'wc_deposit_meta'
                ) oimd ON oimd.order_item_id = oi.order_item_id
                INNER JOIN (
                    SELECT order_item_id, meta_value AS item_total
                    FROM {$this->wpdb->prefix}woocommerce_order_itemmeta
                    WHERE meta_key = '_line_total'
                ) oimt ON oimt.order_item_id = oi.order_item_id
                INNER JOIN {$this->wpdb->prefix}posts p ON p.ID = oi.order_id
                WHERE
                p.post_status IN ('wc-completed','wc-on-hold','wc-partially-paid','wc-processing')
                AND oi.order_item_type = 'line_item'
                AND oim.meta_key = '_product_id'
                AND oim.meta_value IN ({$str_ids})";

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

}