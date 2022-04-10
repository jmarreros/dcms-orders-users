<?php
defined( 'ABSPATH' ) || exit;

?>
<section class="attachment-container">
    <section class="uploaded-files">
        <h4>Archivos para la orden <mark><?= $order_id ?></mark>:</h4>


    </section>

    <section class="form-container">

        <h4>Subir archivo para la orden <?= $order_id ?>:</h4>

        <form action="" enctype="multipart/form-data" method="post" id="attach-form">
            <div>
                <span>Selecciona algún archivo: </span>
                <input type="file" id="file" name="upload-file"/>
            </div>
            <input type="hidden" id="order" name="order" value="<?= $order_id ?>">
            <input type="submit" id="submit" value="Enviar archivo" />
        </form>

        <div id="message"></div>
    </section>
</section>

