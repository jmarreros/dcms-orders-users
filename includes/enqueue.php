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

        // Orders
        wp_register_script('dcms-attachment-script',
                DCMS_ORDERS_URL.'assets/js/attachment.js',
                ['vue.js', 'jquery'],
                DCMS_ORDERS_VERSION,
                true);


        wp_register_style('dcms-orders-style',
                DCMS_ORDERS_URL.'assets/css/orders.css',
                [],
                DCMS_ORDERS_VERSION );
    }

    public static function enqueue_style(){
        wp_enqueue_style('dcms-orders-style');
    }

    // Enqueue script orders
    public static function enqueue_scripts_orders(){
        wp_enqueue_script('dcms-orders-script');

        wp_localize_script('dcms-orders-script',
                            'dcmsOrders',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                                'nonce' => wp_create_nonce('ajax-nonce-orders')]);
    }

    // Enqueue script attachments
    public static function enqueue_scripts_attachment(){
        wp_enqueue_script('dcms-attachment-script');

        wp_localize_script('dcms-attachment-script',
                            'dcmsAttach',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                                'nonce' => wp_create_nonce('ajax-nonce-attachment')]);
    }



}