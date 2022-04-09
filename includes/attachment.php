<?php

namespace dcms\orders\includes;

// Custom post type class
class Attachment{

    public function __construct(){
        add_action('admin_post_handle_attachment', [$this, 'handle_attachment']);
    }

    public function handle_attachment(){

        // TODO: Revisar:
        // https://makitweb.com/how-to-upload-image-file-using-ajax-and-jquery/

        $data = $_POST['data'];
        if(isset($_FILES['upload-file'])) {
            global $wp_filesystem;
            WP_Filesystem();

            $name_file = $_FILES['upload-file']['name'];
            $tmp_name = $_FILES['upload-file']['tmp_name'];
            $allow_extensions = ['png','pdf'];

            // File type validation
            $path_parts = pathinfo($name_file);
            $ext = $path_parts['extension'];

            if ( ! in_array($ext, $allow_extensions) ) {
                echo "Error - File type not allowed";
                return;
            }

            $content_directory = $wp_filesystem->wp_content_dir() . 'uploads/archivos-subidos/';
            $wp_filesystem->mkdir( $content_directory );

            if( move_uploaded_file( $tmp_name, $content_directory . $name_file ) ) {
                echo "File was successfully uploaded";
            } else {
                echo "The file was not uploaded";
            }
        }
    }
}