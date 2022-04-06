<?php

namespace dcms\orders\includes;

class Database{

    private $wpdb;
    private $table_post;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_post   = $this->wpdb->prefix.'posts';
    }


    public function order_has_deposits($id_order):bool{
        $sql = "SELECT COUNT(ID) FROM {$this->table_post}
                WHERE post_parent = {$id_order} AND post_type = 'wcdp_payment'";

        return boolval( $this->wpdb->get_var($sql) );
    }

}