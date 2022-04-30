<?php

// Se integra con el código del theme en donde se muestra un botón a través de javascript
// para usar la url del enlace de producto con depósitos
// DCMS_META_LINK_PRODUCT = WooCommerce_link_product = key meta

namespace dcms\orders\metabox;
use dcms\orders\reports\Database;

class Metabox{

    public function __construct(){
        add_action( 'add_meta_boxes', [$this, 'dcms_add_metabox_product2'] );
        add_action( 'save_post_stm-courses', [$this, 'dcms_save_metabox_content'], 20, 2 );
    }

    public function dcms_add_metabox_product2(){
            add_meta_box(
                'dcms_metabox_product2',
                'URL Producto con Depósitos',
                [$this, 'dcms_add_metabox_content'],
                'stm-courses',
                'side'
            );
    }

    public function dcms_add_metabox_content( $post ){
        $link_product = get_post_meta( $post->ID, DCMS_META_LINK_PRODUCT, true );
        $db = new Database;
        $count = $db->search_duplicate_linkproduct( $link_product );

        if ( $count > 1) {
            echo "<div><strong>⚠️ Este enlace ya esta siendo usado en $count cursos, sólo debería estar en un curso</strong></div><br>";
        }
        ?>
            <label for="link-product" >URL</label>
            <input id="link-product" name="link-product" type="text" value="<?= $link_product ?>" >
        <?php
    }

    public function dcms_save_metabox_content( $post_id, $post ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        $link_product = $_POST['link-product'];
        update_post_meta( $post_id, DCMS_META_LINK_PRODUCT,$link_product );
    }
}