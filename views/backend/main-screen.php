<?php
// Pass values:
$courses = [];
?>

<div id="report-courses" class="wrap" >

    <h1><?php _e('Reporte de Cursos', 'dcms-orders-users') ?></h1>

    <form method="post">
        <section class="report-header">
            <div class="dates-box">
                <div><label for="dstart">Fecha Inicio: </label><input id="dstart" name="dstart" type="date" value=""> </div>
                <div><label for="dend">Fecha Fin: </label><input id="dend" name="dend" type="date" value=""> </div>
                <input type="submit" id="search-submit" name="search-submit" class="button" value="Buscar cursos">
            </div>
        </section>
    </form>

    <table class="dcms-table-report striped">
        <tr>
            <th>Fecha</th>
            <th>Nombre</th>
            <th>Inscritos</th>
            <th>Total</th>
            <th>Pagado</th>
            <th>Pendiente</th>
            <th></th>
        </tr>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?= date_format(date_create($course['date_course']), 'd/m/Y') ?></td>
            <td><?= $course['name_course'] ?></td>
            <td><?= $course['count_students'] ?></td>
            <td><?= wc_price($course['total_course']) ?></td>
            <td><?= wc_price($course['total_paid']) ?></td>
            <td><?= wc_price($course['total_course'] - $course['total_paid']) ?></td>
            <td><a class="button" href="#">Detalle</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

