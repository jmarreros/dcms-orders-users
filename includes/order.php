<?php

namespace dcms\order\includes;

use dcms\orders\includes\Database;

class Orders{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_order_detail',[ $this, 'dcms_order_detail' ]);
    }


    // List orders by user
    public function dcms_order_detail(){
          // Validate nonce
        $this->validate_nonce('ajax-nonce-order');

        $res = [];

        echo json_encode($res);
        wp_die();
    }

}