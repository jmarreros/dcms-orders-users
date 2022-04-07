<?php

namespace dcms\orders\includes;

// Custom post type class
class Enqueue{

    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
    }

    // Front-end script
    public function register_scripts(){
        // Orders
        wp_register_script('dcms-orders-script',
                DCMS_ORDERS_URL.'assets/js/orders.js',
                ['vue.js','jquery'],
                DCMS_ORDERS_VERSION,
                true);

        wp_register_style('dcms-orders-style',
                DCMS_ORDERS_URL.'assets/css/orders.css',
                [],
                DCMS_ORDERS_VERSION );
    }


}