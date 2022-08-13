<?php
/**
 * Custom template for listing courses orders using Vue.js and Ajax
 *
 *  $current_url : Current page url
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="flexible-payment">
  <a href="/producto/pago-flexible/" class="button" target="_blank">
    Pagos Flexibles
  </a>
</section>
<div id="courses-orders">

    <input id="reloadButton" v-on:click="reloadButton" class="button" type="button" value="recargar">

    <br>

    <div>
        <ul id="list-courses">
            <li v-for="(item, course, index) in results" :key="index">

                <div class="course-name">{{ course }}</div>
                <table class="course-detail" style="overflow-x: auto;">
                    <tr>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Pendiente</th>
                        <th>Pagado</th>
                        <th>Acciones</th>
                    </tr>
                    <tr v-for="(detail, order) of item">
                        <td>{{ order }}</td>
                        <td>{{ detail.info_item_order.post_status }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </li>
        </ul>
    </div>

    <section class="footer-container">
        <section v-if="loading" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>
    </section>
</div>
