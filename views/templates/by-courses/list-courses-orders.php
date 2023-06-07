<?php
/**
 * Custom template for listing courses orders using Vue.js and Ajax
 *
 *  $current_url : Current page url
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="flexible-payment">
    <div class="dropdown-container">
        <a onclick="dropFlexiblePayment()" class="button btn-drop">
            Pagos Flexibles <i class="fa fa-caret-down"></i>
        </a>
        <div id="drop-flexible-payment" class="dropdown-content">
            <a href="/producto/pago-flexible/?own=1">Para mis cursos recientes</a>
            <a href="/producto/pago-flexible/?own=0">Para nuevos cursos</a>
        </div>
    </div>
</section>
<div id="courses-orders">
    <div>
        <ul id="list-courses">
            <li v-for="(items, course, index) in results" :key="index">

                <div class="course-name">{{ course }}</div>
                <div style="overflow-x: auto;">
                    <table class="course-detail">
                        <tr>
                            <th>Orden</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Pendiente</th>
                            <th>Pagado</th>
                            <th>Acciones</th>
                        </tr>
                        <tr v-for="(detail, order, index) in items" :key="index">
                            <td><a class="order-number" :href="'<?= $current_url ?>?order=' + order" >{{ order }}</a></td>
                            <td>{{ detail.date }}</td>
                            <td>{{ detail.info_item_order.post_status }}</td>
                            <td>{{ parseFloat(detail.info_item_order.pending).toFixed(2) }} {{ detail.info_item_order.order_currency }}</td>
                            <td>{{ parseFloat(detail.info_item_order._line_total).toFixed(2) }} {{ detail.info_item_order.order_currency }}</td>
                            <td>
                                <section class="container-actions">
                                    <a :href="'<?= $current_url ?>?order=' + order + '&action=attach'" class="button">Adjuntar</a>
                                    <a v-if="detail.info_item_order.payment_url" :href="detail.info_item_order.payment_url" class="button">Pagar</a>
                                </section>
                            </td>
                        </tr>
                    </table>
                </div>
            </li>
        </ul>
    </div>

    <section class="footer-container">
        <section v-if="loading" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>
    </section>
</div>
