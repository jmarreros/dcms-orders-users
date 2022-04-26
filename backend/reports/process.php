<?php

namespace dcms\orders\reports;

use dcms\orders\reports\Database;

class Process{
    public function get_resume_courses(){
        $db = new Database;
        // $courses = $db->get_courses();

        // foreach ($courses as $key => $course) {
        //     $courses[$key]['id_product2'] = $db->get_product_id_from_url($course['url_product2']);
        // }

        // error_log(print_r($courses,true));

        // $id = $db->get_product_id_from_url('http://caesacademy4.local/producto/diplomado-gerencia-de-gestion-humana-5/');
        // error_log(print_r($id,true));

        $orders = $db->get_orders_by_id_product([20069, 6719]);


        foreach ($orders as $key => $order) {

            if ( $this->has_deposit($order['deposit_info']) ){
                // error_log(print_r("Si tiene depósito",true));

                $result = $this->get_paid_deposit_amount($order['deposit_info'], 3);

                error_log(print_r($result,true));

            } else {
                error_log(print_r("No tiene depósito",true));
            }

            // La orden ya esta pagada totalmente
            // if ( $order['post_status'] == 'wc-completed'){

            // } else { // La orden aún no esta pagada completamente o esta en espera

            // }

        }

        error_log(print_r($orders,true));
    }


    private function has_deposit($deposit_info){
        if ( empty($deposit_info) ) return false;

        $data = maybe_unserialize($deposit_info);
        $enable = $data['enable']??'no';

        return $enable === 'yes';
    }


    // Get total payment by item in partial payment by deposit
    private function get_paid_deposit_amount($deposit_info, $count_payments){
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
