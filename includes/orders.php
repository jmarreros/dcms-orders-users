<?php

// For manage by-orders shorcode and template

namespace dcms\orders\includes;

use dcms\orders\includes\Database;
use dcms\orders\helpers\Helper;

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
                  'paged' => $page
                ];

        $orders = wc_get_orders($args);

        return $orders;
    }

    // List orders by user
    public function dcms_list_orders(){
          // Validate nonce
        Helper::validate_nonce('ajax-nonce-orders');

        $res = [];
        $page  = $_POST['page']??1;

        $db = new Database();
        $items = $this->get_object_orders($page);

        // error_log(print_r($items->orders,true));

        $data = [];
        foreach ($items->orders as $key => $item) {
            $order_id = $item->get_id();

            $currency = $item->get_currency();

            $data[$key]['id'] = $order_id;
            $data[$key]['status'] = wc_get_order_status_name( $item->get_status() );
            $data[$key]['date'] = wc_format_datetime( $item->get_date_created() );
            $data[$key]['total'] = $currency.' '.$item->get_total();
            $data[$key]['deposit'] = $db->order_has_deposits($order_id);
            $data[$key]['pending'] = $currency.' '.$db->get_total_payment_pending($order_id);
            $data[$key]['payment_url'] = '';

            // Conditional for showing payment link
            if ( $item->get_status() == 'partially-paid' &&  $data[$key]['deposit']){
                $data_payment =  $db->data_partial_payment($order_id);

                if ( $data_payment ){
                    $url_payment = $this->build_url_payment($data_payment);
                    $data[$key]['payment_url'] = $url_payment;
                }
            }

            // For showing flexible data
            $flexible_data = $db->order_flexible_payment($order_id);
            $data[$key]['is_flexible'] = false;
            if ( $flexible_data ){
              $data[$key]['is_flexible'] = true;

              $course_id = $flexible_data->course_id;
              $course_price = $flexible_data->course_price;
              $course_currency = $flexible_data->course_currency;
              $user_id = get_current_user_id();

              $result = $db->order_flexible_payment_user_course($user_id, $course_id);

              $amount = 0;
              foreach ($result as $item) {
                $amount += Helper::currency_converter($item->total, $item->currency);
              }

              $data[$key]['pending'] = Helper::currency_converter($course_price, $course_currency) - $amount;
              $data[$key]['pending'] = Helper::get_default_currency() . ' ' . $data[$key]['pending'];

            }
          }

        $res = [
            'status' => 1,
            'message' => "Página $page de {$items->max_num_pages}",
            'total_pages' => $items->max_num_pages,
            'data' => $data
        ];

        echo json_encode($res);
        wp_die();
    }


    // Auxiliar function for building the url partial payment
    private function build_url_payment($data_payment){
        if ( ! $data_payment ) return '';

        $id = $data_payment['ID'];
        $key = $data_payment['key_url'];

        $url = get_home_url()."/checkout/order-pay/{$id}/?pay_for_order=true&key={$key}";

        return $url;
    }

}