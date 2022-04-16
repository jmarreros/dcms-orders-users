<?php

namespace dcms\orders\includes;

use dcms\orders\helpers\Helper;

// Custom post type class
class Attachment{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_add_file',[ $this, 'dcms_add_file_order' ]);
        add_action('wp_ajax_dcms_ajax_get_files',[ $this, 'dcms_get_uploaded_files' ]);
        add_action('wp_ajax_dcms_ajax_remove_file', [ $this, 'dcms_remove_file']);
        add_action('add_meta_boxes', [ $this, 'dcms_show_list_attachments']);
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

            if ( isset($res['filename']) ){
                $file_url = content_url( DCMS_UPLOAD_FOLDER. $res['filename'] );
                $this->_update_metadata( $order, $file_url );
            }

        }

        echo json_encode($res);
        wp_die();
    }

    // Process upload file with Ajax
    private function _upload_file(){

        if( isset($_FILES['file']) ) {

            $name_file = $_FILES['file']['name'];
            $tmp_name = $_FILES['file']['tmp_name'];

            // Validate extension
            $res = $this->_validate_extension_file($name_file);
            if (isset($res['status']) && $res['status'] == 0) return $res;

            // Move file
            $content_directory = Helper::path_content_folder(true);
            $name_file = $this->_rename_file($name_file);

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

    // Remove order metadata filename
    private function _remove_metadata($id_order, $filename){
        $order = Helper::get_order_user($id_order);

        if ( $order ){
            $values = $order->get_meta(DCMS_ORDERS_KEY_META);
            if ( $values ){
                foreach ($values as $key => $value) {
                    if ( $filename === basename($value) ){
                        unset($values[$key]);

                        $values = array_values($values);
                        $order->update_meta_data(DCMS_ORDERS_KEY_META, $values);
                        $order->save();
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // Rename file with unix time
    private function _rename_file( $name_file ){
        $path_parts = pathinfo($name_file);

        $name = $path_parts['filename'];
        $ext = $path_parts['extension'];
        $name = $name.'_'.time();

        return $name.'.'.$ext;
    }

    // Validate the extension file
    private function _validate_extension_file( $name_file ){
        $allow_extensions = ['png','jpg','jpeg','webp','pdf'];

        // File type validation
        $path_parts = pathinfo($name_file);
        $ext = $path_parts['extension'];

        if ( ! in_array($ext, $allow_extensions) ) {
            return [
                'status' => 0,
                'message' => "Extensión de archivo no permitida"
            ];
        }
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


    // Show list attachments in order details
    public function dcms_show_list_attachments(){
        add_meta_box( 'dcms_attachments', 'Adjuntos orden', [$this, 'dcms_content_metabox'], 'shop_order', 'normal', 'high' );
    }

    // metabox content
    public function dcms_content_metabox(){
        $id_order = get_the_ID();
        $order = wc_get_order( $id_order );

        if ( $order ) {
            $data = $order->get_meta(DCMS_ORDERS_KEY_META);

            if ( $data ){
                echo "<ul class='dcms-attachments'>";
                foreach($data as $item){
                    $name = basename($item);
                    echo "<li><a href='$item' target='_blank'>$name</a></li>";
                }
                echo "</ul>";
            }
        }

    }

    // Remove files front-end user
    public function dcms_remove_file(){
        Helper::validate_nonce('ajax-nonce-attachment');

        $filename = $_POST['filename'];
        $id_order = $_POST['id_order']??0;

        $res =  [
            'status' => 0,
            'message' => "Hubo un error al borrar el archivo $filename"
        ];

        if ( $this->_remove_metadata($id_order, $filename) ){
            $path_file = Helper::path_content_folder().$filename;

            if ( unlink($path_file) ){
                $res =  [
                    'status' => 1,
                    'message' => "Archivo $filename eliminado"
                ];
            }
        }

        echo json_encode($res);
        wp_die();
    }

}
