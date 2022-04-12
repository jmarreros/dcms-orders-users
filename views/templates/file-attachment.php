<?php
defined( 'ABSPATH' ) || exit;

?>
<section id="attachment-container" class="attachment-container">

    <section class="uploaded-files">
        <section class="order-resume">
            <div>
                <h4>Archivos para la orden <mark><?= $order_id ?></mark>:</h4>
            </div>
            <div>
                <a href="<?= $current_url ?>"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </section>

        <div class="no-items" v-if="!loadingFiles && results.length==0">
            No hay aún ningún archivo adjunto
        </div>
        <ul v-if="!loadingFiles && results.length">
            <li v-for="item in results">
                <a :href="item" target="_blank">
                    <i class="fa fa-file"></i> {{ item.substring(item.lastIndexOf('/')+1) }}
                </a>
            </li>
        </ul>

        <section v-if="loadingFiles" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>

    </section>

    <section class="form-container" v-show="(uploadingFile === null && ! loadingFiles) || uploadingFile !== null">

        <h4>Subir archivo para la orden <?= $order_id ?>:</h4>

        <form action="" enctype="multipart/form-data" method="post" id="attach-form" v-on:submit.prevent="onSubmit">
            <div>
                <span>Selecciona algún archivo: </span>
                <input type="file" id="file" name="upload-file"/>
            </div>
            <input type="hidden" id="order" name="order" value="<?= $order_id ?>">
            <input type="submit" id="submit" value="Enviar archivo" class="button" />
        </form>

        <div id="message" v-if="!uploadingFile && message.length">{{ message }}</div>

        <section v-if="uploadingFile" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>

    </section>
</section>

