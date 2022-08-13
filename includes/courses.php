<?php

// For manage by-courses shorcode and template

namespace dcms\orders\includes;

use dcms\orders\includes\Database;
use dcms\orders\helpers\Helper;

class Courses{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_list_courses_orders',[ $this, 'list_courses_orders' ]);
    }

    // List courses orders
    public function list_courses_orders(){
        // Validate nonce
        Helper::validate_nonce('ajax-nonce-courses-orders');
        $res = [];

        $db = new Database();

        $current_user_id = get_current_user_id();
        $courses = $db->get_courses_order_user($current_user_id);

        // Group courses by name with addinional fields
        $data_courses = [];
        foreach ($courses as $course) {
            $data_courses[$course->course_name][$course->order_id] = [
                            'item' => $course->order_item_id,
                            'flexible' => $course->flexible,
                            'has_deposits' => intval( ! $course->flexible && $db->order_has_deposits($course->order_id) )
                        ];
        }

        //  Add additional data by type item
        foreach ($data_courses as $name_course => $data_course) {
            
            // Add additional data info_item_order to data_courses
            foreach ($data_course as $order => $data_order) {
                $info_item_order = [];
                if ( ! $data_order['flexible'] && ! $data_order['has_deposits']){ // normal order
                    $info_item_order = $this->get_normal_info_item_order($order, $data_order['item']);
                    $data_courses[$name_course][$order]['info_item_order'] = $info_item_order;

                } elseif ( $data_order['has_deposits'] ){ // deposit order - internal validation item has deposit
                    $info_item_order = $this->get_deposit_info_item_order($order, $data_order['item']);
                    $data_courses[$name_course][$order]['info_item_order'] = $info_item_order;

                } elseif( $data_order['flexible'] ){ // flexible order
                        $info_item_order = $this->get_flexible_info_item_order($order, $data_order['item']);
                        $data_courses[$name_course][$order]['info_item_order'] = $info_item_order;
                }

            }

            // Adding pending to items_flexible by course
            $items_flexible = array_filter($data_courses[$name_course], fn($data_order) => $data_order['flexible'] == 1);
            if ( $items_flexible ) {
                $items_flexible_with_pending = $this->add_pending_field_item_flexible($items_flexible);
                // TODO: fusionar arrays agregando el pending
                // $data_courses[$name_course] = array_merge($data_courses[$name_course], $items_flexible_with_pending);
            }

        }


        error_log(print_r($data_courses, true));

        $res = [
            'status' => 1,
            // 'data' => ['hola']
        ];

        wp_send_json($res);
    }


    // Get basic info for an item standard order
    private function get_normal_info_item_order($order_id, $item_order_id){
        $db = new Database();
        $info = $db->get_basic_order_info($order_id);
        $item_order_info = $db->get_basic_item_order_info($item_order_id);

        // Add data to $info var
        foreach ($item_order_info as $metadata) {
            $info[$metadata['meta_key']] = $metadata['meta_value'];
        }

        return $info;
    }

    // Get info item order for a product with flexible price
    private function get_flexible_info_item_order($order_id, $item_order_id){
        $db = new Database();
        $info = $db->get_basic_order_info($order_id);
        $item_order_flexible_info = $db->get_flexible_item_order_info($item_order_id);

        // Add data to $info var
        foreach ($item_order_flexible_info as $metadata) {
            $info[$metadata['meta_key']] = $metadata['meta_value'];
        }

        return $info;
    }

    // Get basic info for a item deposit order
    private function get_deposit_info_item_order($order_id, $item_order_id){
        $db = new Database();
        $desposit_meta = $db->item_order_deposit_meta($item_order_id);
        $info = $this->get_normal_info_item_order($order_id, $item_order_id);

        // Validate if item has deposit
        if (! $desposit_meta || ($desposit_meta['enable']??'no') === 'no'  ){
            return $info;
        }

        $info_deposit = $this->get_payments_deposit($desposit_meta, $order_id);
        $info['total_deposit'] = $info_deposit['total'];
        $info['pending_deposit'] = $info_deposit['pending'];

        return $info;
    }

    // Get current payments for every desposit
    private function get_payments_deposit($deposit_meta, $order_id){
        $db = new Database();        
        $status_orders = $db->get_status_child_orders($order_id);

        // Get payments deposit
        $payments = [];
        $payments[] = $deposit_meta['deposit']; // first payment
        foreach ($deposit_meta['payment_schedule'] as $item) {
            $payments[] = $item['amount'];
        }

        // Detect pending
        $states = ['wc-completed','wc-on-hold'];
        $total_pay = 0;
        foreach ($payments as $key => $payment) {
            if ( in_array( $status_orders[$key], $states ) ){
                $total_pay += $payment;
            }
        }

        $info_deposit = [];
        $info_deposit['total'] = $deposit_meta['total'];
        $info_deposit['pending'] = $info_deposit['total'] - $total_pay;

        return $info_deposit;
    }


    // For adding pending payments in flexible orders
    private function add_pending_field_item_flexible( $items_flexible ){
        // Verify if all has the same money
        $currency = $items_flexible[array_key_first($items_flexible)]['info_item_order']['order_currency'];
        $different_currency = array_filter($items_flexible, fn($data_item) => $data_item['info_item_order']['order_currency'] != $currency);

        if ( ! $different_currency ){
            $items_flexible_add = array_reverse($items_flexible, true);
            
            // Initial pending
            $first = array_key_first($items_flexible_add);
            $total_course = floatval($items_flexible_add[$first]['info_item_order']['curso_precio']);
            $total_paid = floatval($items_flexible_add[$first]['info_item_order']['_line_total']);
            $accummulate_pending = $total_course - $total_paid;
            $items_flexible_add[$first]['info_item_order']['pending'] = $accummulate_pending;

            // Sustract pending
            foreach ($items_flexible_add as $order => $_) {

                if ( ! isset($items_flexible_add[$order]['info_item_order']['pending']) ){
                    $total_paid = floatval($items_flexible_add[$order]['info_item_order']['_line_total']);                    
                    $accummulate_pending = $accummulate_pending - $total_paid;
                    $items_flexible_add[$order]['info_item_order']['pending'] = $accummulate_pending;
                }

            }
        }
        return array_reverse($items_flexible_add, true);
    }
    
}