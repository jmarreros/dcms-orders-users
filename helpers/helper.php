<?php

namespace dcms\orders\helpers;

// Custom post type class
class Helper{

    // Aux - Security, verify nonce
    public static function validate_nonce( $nonce_name ){
        if ( ! wp_verify_nonce( $_POST['nonce'], $nonce_name ) ) {
            $res = [
                'status' => 0,
                'message' => '✋ Error nonce validation!!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

}