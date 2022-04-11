<?php

namespace dcms\orders\includes;

use dcms\orders\helpers\Helper;

// Custom post type class
class Attachment{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_add_file',[ $this, 'dcms_add_file_order' ]);
        add_action('wp_ajax_dcms_ajax_get_files',[ $this, 'dcms_get_uploaded_files' ]);
    }

    // Upload file order and update metadata order
    public function dcms_add_file_order(){
        $res = [];
        Helper::validate_nonce('ajax-nonce-attachment');

        // Get order
        $id_order = $_POST['id_order']??0;
        $order = Helper::get_order_user($id_order);

        if ( $order ){
            $res = $this->_upload_file();

            $file_url = content_url( DCMS_UPLOAD_FOLDER. $res['filename'] );
            $this->_update_metadata( $order, $file_url );
        }

        echo json_encode($res);
        wp_die();
    }

    // Process upload file with Ajax
    private function _upload_file(){

        if( isset($_FILES['file']) ) {

            global $wp_filesystem;
            WP_Filesystem();

            $name_file = $_FILES['file']['name'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $allow_extensions = ['png','pdf'];

            // File type validation
            $path_parts = pathinfo($name_file);
            $ext = $path_parts['extension'];

            if ( ! in_array($ext, $allow_extensions) ) {
                return [
                    'status' => 0,
                    'message' => "Extensión de archivo no permitida"
                ];
            }

            $content_directory = $wp_filesystem->wp_content_dir() . DCMS_UPLOAD_FOLDER;
            $wp_filesystem->mkdir( $content_directory );

            if( move_uploaded_file( $tmp_name, $content_directory . $name_file ) ) {
                return [
                    'status' => 1,
                    'message' => "El archivo se agregó correctamente",
                    'filename' => $name_file
                ];
            }

        }

        return [
            'status' => 0,
            'message' => "Existe un error en la subida del archivo"
        ];

    }

    // Save files url in the order metadata
    private function _update_metadata( $order, $file_url ){
        $values = $order->get_meta(DCMS_ORDERS_KEY_META);

        if ( $values ) {
            $values[] = $file_url;
        } else {
            $values = [$file_url];
        }

        $order->update_meta_data(DCMS_ORDERS_KEY_META, $values);
        $order->save();
    }

    // Get upload files by order id
    public function dcms_get_uploaded_files(){
        $res = ['status' => 0,
                'message' => 'No se pudo recuperar los datos' ];

        // Get order
        $id_order = $_POST['id_order']??0;
        $order = wc_get_order( $id_order );

        if ( $order ) {
            $data = $order->get_meta(DCMS_ORDERS_KEY_META);
            $res =  [
                'status' => 1,
                'data' => $data
                ];
        }

        echo json_encode($res);
        wp_die();
    }
}
