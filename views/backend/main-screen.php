<?php
defined( 'ABSPATH' ) || exit;
$current_url = admin_url() . DCMS_ORDERS_SUBMENU . '?page='. DCMS_ORDERS_MAINPAGE;
$course_url = admin_url() . 'post.php?post=';
$product_url = admin_url() . 'post.php?post=';
$lms_dashboard_url = admin_url() . '?page=stm-lms-dashboard#/course/';
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
                <th>Producto</th>
                <th>Inscritos</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Pendiente</th>
                <th>Detalle</th>
            </tr>
            <tr v-for="item in results" :key="item.id_course">
                <td>{{ format_date(item.date_course) }}</td>
                <td>
                    <a :href="'<?= $course_url ?>' + item.id_course + '&action=edit'" target="_blank">
                        {{ item.name_course }}
                    </a>
                </td>
                <td>
                    <small>
                        <a :href="'<?= $product_url ?>' + item.id_product + '&action=edit'" target="_blank">
                            {{item.id_product}}
                        </a><br>
                        <a :href="'<?= $product_url ?>' + item.id_product2 + '&action=edit'" target="_blank">
                            {{item.id_product2 ? item.id_product2 : ''}}
                        </a>
                    </small>
                </td>
                <td>
                    <a :href="'<?= $lms_dashboard_url ?>' + item.id_course" target="_blank">
                        {{ item.count_students }}
                    </a>
                </td>
                <td>{{ Intl.NumberFormat('en-US').format(item.total_course) }}</td>
                <td>{{ Intl.NumberFormat('en-US').format(item.total_paid) }}</td>
                <td>{{ Intl.NumberFormat('en-US').format(item.total_pending) }}</td>
                <td><a target="_blank" class="button" :href="'<?= $current_url ?>' + '&id_products=' +  item.id_product + '-' + item.id_product2 + '&course_name=' + encodeURIComponent(item.name_course)" >Detalle</a></td>
            </tr>
        </table>
    </div>

    <div v-if="!results.length && !loading" class="no-items">No hay ningún curso</div>

</div>

