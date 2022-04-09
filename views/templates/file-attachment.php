<?php
defined( 'ABSPATH' ) || exit;

?>
<div class="wrap">
    <h3>Subir archivo:</h3>
    <br>
    <form action="<?= admin_url( 'admin-post.php' ) ?>" enctype="multipart/form-data" method="post">
        Selecciona algún archivo: <input name="upload-file" type="file" /> <hr>
        <input type="hidden" name="action" value="handle_attachment">
        <input type="hidden" name="data" value="xxx">
        <input type="submit" value="Enviar archivo" />
    </form>
</div>