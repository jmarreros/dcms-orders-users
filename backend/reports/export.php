<?php

namespace dcms\orders\reports;

use dcms\orders\reports\Process;

// Export class for reporting
class Export{

    public function __construct(){
        add_action( 'admin_post_export_courses', [$this, 'dcms_export_courses'] );
        add_action( 'admin_post_export_course', [$this, 'dcms_export_course'] );
    }

    public function dcms_export_courses(){
        $process = new Process;
        $data = $process->get_resume_courses();

        $this->download_send_headers("courses_export_" . date("Y-m-d") . ".csv");

        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv( $df, array_keys( reset($data) ) );
        foreach ( $data as $row ) {
            fputcsv($df, $row);
        }
        fclose($df);
        echo ob_get_clean();
        die();
    }


    public function dcms_export_course(){
        $process = new Process;
        $id_products = explode(',', $_POST['id_products']);
        $id_course = $_POST['id_course']??0;

        $data = $process->get_detail_course($id_course, $id_products);

        $this->download_send_headers("course_export_" . date("Y-m-d") . ".csv");

        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv( $df, array_keys( reset($data) ) );
        foreach ( $data as $row ) {
            fputcsv($df, $row);
        }
        fclose($df);
        echo ob_get_clean();
        die();
    }


    private function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
}