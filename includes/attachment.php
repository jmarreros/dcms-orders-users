<?php

namespace dcms\orders\includes;

use dcms\orders\helpers\Helper;

// Custom post type class
class Attachment{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_add_file',[ $this, 'dcms_add_file_order' ]);
    }

    public function dcms_add_file_order(){
        $res = [];
        Helper::validate_nonce('ajax-nonce-attachment');

        $id_order = $_POST['id_order']??0;

        $res = $this->upload_file();

        echo json_encode($res);
        wp_die();
    }


    private function upload_file(){

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
                    'message' => "El archivo se agregó correctamente"
                ];
            }

        }

        return [
            'status' => 0,
            'message' => "Existe un error en la subida del archivo"
        ];

    }

}








// <form action="< admin_url( 'admin-post.php' ) >" enctype="multipart/form-data" method="post">
// Selecciona algún archivo: <input name="upload-file" type="file" /> <hr>
// <input type="hidden" name="action" value="handle_attachment">
// <input type="hidden" name="data" value="xxx">
// <input type="submit" value="Enviar archivo" />
// </form>

    // add_action('admin_post_handle_attachment', [$this, 'handle_attachment']);

    // public function handle_attachment(){

    //     $data = $_POST['data'];
    //     if(isset($_FILES['upload-file'])) {
    //         global $wp_filesystem;
    //         WP_Filesystem();

    //         $name_file = $_FILES['upload-file']['name'];
    //         $tmp_name = $_FILES['upload-file']['tmp_name'];
    //         $allow_extensions = ['png','pdf'];

    //         // File type validation
    //         $path_parts = pathinfo($name_file);
    //         $ext = $path_parts['extension'];

    //         if ( ! in_array($ext, $allow_extensions) ) {
    //             echo "Error - File type not allowed";
    //             return;
    //         }

    //         $content_directory = $wp_filesystem->wp_content_dir() . 'uploads/archivos-subidos/';
    //         $wp_filesystem->mkdir( $content_directory );

    //         if( move_uploaded_file( $tmp_name, $content_directory . $name_file ) ) {
    //             echo "File was successfully uploaded";
    //         } else {
    //             echo "The file was not uploaded";
    //         }
    //     }
    // }