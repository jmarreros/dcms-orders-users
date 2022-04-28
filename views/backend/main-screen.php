<?php
defined( 'ABSPATH' ) || exit;

?>
<div id="report-courses" class="wrap" >

    <h1><?php _e('Reporte de Cursos', 'dcms-orders-users') ?></h1>

    <form method="post"  v-on:submit.prevent="onSubmit">
        <section class="report-header">
            <div class="dates-box">
                <div><label for="dstart">Fecha Inicio: </label><input id="dstart" name="dstart" type="date" value="<?= date('Y-m-d', strtotime("first day of previous month")) ?>"> </div>
                <div><label for="dend">Fecha Fin: </label><input id="dend" name="dend" type="date" value="<?= date('Y-m-d') ?>"> </div>
                <div><input type="search" id="tcourse" placeholder="Ingresa algún texto" /></div>
                <input type="submit" id="search-submit" name="search-submit" class="button" value="Buscar cursos">
            </div>
            <div class="export-box">
                <!-- <input type="button" id="export" name="export" class="button" value="Exportar Cursos"> -->
            </div>
        </section>
    </form>

    <section class="message-container">
        <section v-if="loading" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>
        <section class="total-container">
            <span>Total : {{ results.length }}</span>
        </section>
    </section>

    <div style="overflow-x: auto;">
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
            <tr v-for="item in results" :key="item.id_course">
                <td>{{ format_date(item.date_course) }}</td>
                <td>{{ item.name_course }}</td>
                <td>{{ item.count_students }}</td>
                <td v-html="item.total_course"></td>
                <td v-html="item.total_paid"></td>
                <td v-html="item.total_pending"></td>
                <td><a class="button" href="#">Detalle</a></td>
            </tr>
        </table>
    </div>

    <div v-if="!results.length && !loading" class="no-items">No hay ningún curso</div>

</div>

