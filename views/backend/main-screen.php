<?php
defined( 'ABSPATH' ) || exit;

?>
<div id="report-courses" class="wrap" >

    <h1><?php _e('Reporte de Cursos', 'dcms-orders-users') ?></h1>


        <section class="report-header">
            <form method="post"  v-on:submit.prevent="onSubmit">
                <div class="dates-box">
                    <div><label for="dstart">Fecha Inicio: </label><input id="dstart" name="dstart" type="date" value="<?= date('Y-m-d', strtotime("first day of previous month")) ?>" :disabled="loading" > </div>
                    <div><label for="dend">Fecha Fin: </label><input id="dend" name="dend" type="date" value="<?= date('Y-m-d') ?>" :disabled="loading" > </div>
                    <div><input type="search" id="tcourse" placeholder="Ingresa algún texto" :disabled="loading" ></div>
                    <input type="submit" id="search-submit" name="search-submit" class="button" value="Buscar cursos" :disabled="loading" >
                </div>
            </form>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
                <div class="export-box">
                    <input type="submit" id="export" name="export" class="button" value="Exportar Cursos">
                    <input type="hidden" name="action" value="export_courses">
                </div>
            </form>
        </section>

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
                <td>{{ Intl.NumberFormat('en-US').format(item.total_course) }}</td>
                <td>{{ Intl.NumberFormat('en-US').format(item.total_paid) }}</td>
                <td>{{ Intl.NumberFormat('en-US').format(item.total_pending) }}</td>
                <td><a class="button" href="#">Detalle</a></td>
            </tr>
        </table>
    </div>

    <div v-if="!results.length && !loading" class="no-items">No hay ningún curso</div>

</div>

