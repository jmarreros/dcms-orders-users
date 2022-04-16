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

    public static function get_current_url(){
        global $wp;
        return home_url( $wp->request );
    }


    public static function get_order_user($order_id){
        $order = wc_get_order( $order_id );

        // Validation order by user
        if ( ! $order || get_current_user_id() != $order->get_customer_id() ) {
            return null;
        }

        return $order;
    }


    // Return wp_cpontente folder absolute path
    public static function path_content_folder($create_folder = false){
        global $wp_filesystem;
        WP_Filesystem();

        $content_folder = $wp_filesystem->wp_content_dir() . DCMS_UPLOAD_FOLDER;
        if ( $create_folder ) $wp_filesystem->mkdir( $content_folder );

        return $content_folder;
    }
}