<?php

namespace dcms\orders\reports;

use dcms\orders\reports\Database;
use dcms\orders\helpers\Helper;

class Process{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_courses_report',[ $this, 'dcms_courses_report' ]);
    }

    // Ajax callback
    public function dcms_courses_report(){
        Helper::validate_nonce('ajax-nonce-report');

        // Filter parameters
        $dstart = $_POST['dstart']??'';
        $dend = $_POST['dend']??'';
        $tcourse = $_POST['tcourse']??'';

        $data = $this->get_resume_courses($dstart, $dend, $tcourse);

        $res = [
            'status' => 1,
            'data' => $data
        ];

        echo json_encode($res);
        wp_die();
    }

    // Main function for getting courses list
    public function get_resume_courses($dstart = '', $dend = '', $tcourse = ''){
        $db = new Database;
        $courses = $db->get_courses( $dstart, $dend, $tcourse);

        foreach ($courses as $key => $course) {

            // Add column id_product2
            $id_product2 = $db->get_product_id_from_url($course['url_product2']); // Add second product ID
            $courses[$key]['id_product2'] = $id_product2;

            // Build id_products
            $id_products[0] = $courses[$key]['id_product'];
            $id_products[1] = 0;
            if ( $id_product2 > 0 ) $id_products[1] = $id_product2;

            $items_orders = $db->get_items_orders_by_ids_product( $id_products );

            // Accumulate totals
            $total_course = 0;
            $total_paid = 0;

            foreach ($items_orders as $item_order) {
                $currency = $item_order['currency'];

                $total_course += Helper::currency_converter($item_order['item_total'], $currency);
                $has_deposits = $this->_has_deposit( $item_order['deposit_info']);

                // Order is completed o waiting total paid
                if ( $item_order['post_status'] === 'wc-completed' ||
                    ( $item_order['post_status'] === 'wc-on-hold' && ! $has_deposits ) ){

                    $total_paid += Helper::currency_converter($item_order['item_total'], $currency);

                } else if ( $has_deposits ) { // Order uncompleted paid

                    $count_payments = $db->count_sub_orders_completed($item_order['order_id']);
                    $amount_item = $this->_get_paid_deposit_amount($item_order['deposit_info'], $count_payments);

                    $total_paid += Helper::currency_converter($amount_item, $currency);
                }
            }

            // Add to array courses
            $courses[$key]['total_course'] = $total_course;
            $courses[$key]['total_paid'] = $total_paid;
            $courses[$key]['total_pending'] = $total_course - $total_paid;
        }

        return $courses;
    }

    // Get detail course
    public function get_detail_course($id_products){
        $db = new Database;
        $items_orders = $db->get_items_orders_by_ids_product( $id_products, true );

        foreach ($items_orders as $key => $item_order) {
            $total_paid = 0;

            $has_deposits = $this->_has_deposit( $item_order['deposit_info']);

            // Order is completed o waiting total paid
            if ( $item_order['post_status'] === 'wc-completed' ||
                ( $item_order['post_status'] === 'wc-on-hold' && ! $has_deposits ) ){

                $total_paid = $item_order['item_total'];

            } else if ( $has_deposits ) { // Order uncompleted paid

                $count_payments = $db->count_sub_orders_completed($item_order['order_id']);
                $total_paid = $this->_get_paid_deposit_amount($item_order['deposit_info'], $count_payments);
            }

            // Multicurrency support
            $currency = $item_order['currency'];
            $total_item = Helper::currency_converter($item_order['item_total'], $currency);
            $total_paid = Helper::currency_converter($total_paid, $currency);

            $items_orders[$key]['item_total'] = $total_item;
            $items_orders[$key]['total_paid'] = $total_paid;
            $items_orders[$key]['total_pending'] = $total_item - $total_paid;
        }

        return $items_orders;
    }

    // Verify if the order has deposit enabled
    private function _has_deposit($deposit_info){
        if ( empty($deposit_info) ) return false;

        $data = maybe_unserialize($deposit_info);
        $enable = $data['enable']??'no';

        return $enable === 'yes';
    }


    // Get total payment by item given count_payments var
    private function _get_paid_deposit_amount($deposit_info, $count_payments){
        $data = maybe_unserialize($deposit_info);
        $paid = 0;

        if ( $count_payments >= 1 ) $paid = $data['deposit'];

        if ( $count_payments >= 2 ){
            $schedule = $data['payment_schedule'];

            $i = 2;
            foreach ($schedule as $value) {
                if ( $i <= $count_payments ){
                    $paid += $value['amount'];
                } else {
                    break;
                }
                $i++;
            }
        }

        return $paid;
    }


}

