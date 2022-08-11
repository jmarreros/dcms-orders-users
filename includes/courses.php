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
            $data_courses[ $course->course_name ][$course->order_id] = [
                            'item' => $course->order_item_id,
                            'flexible' => $course->flexible,
                            'has_deposits' => intval( ! $course->flexible && $db->order_has_deposits($course->order_id) )
                        ];
        }

        // Add additional data to data_courses
        foreach ($data_courses as $name_course => $data_course) {
            error_log(print_r($name_course, true));
            error_log(print_r($data_course, true));
        }


        // TODO
        // Para detectar el pendiente de pago en una orden con depósitos, revisar las órdenes hijas si estan pagadas
        // y haciendo correspondencia con el campo wc_deposit_meta de la tabla wp_woocommerce_order_itemmeta

        // error_log(print_r($data_courses, true));

        $res = [
            'status' => 1,
            'data' => ['hola']
        ];

        wp_send_json($res);
    }

}