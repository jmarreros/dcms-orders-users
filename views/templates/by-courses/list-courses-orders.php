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

    <div style="overflow-x: auto;">
        <table id="list-orders">
            <tr>
                <th>Nombre Curso</th>
                <th>Pendiente</th>
                <th>Total</th>
            </tr>
            <tr v-for="item in results" :key="item.id">
                <td>Curso1</td>
                <td>100</td>
                <td>100</td>
            </tr>
        </table>
    </div>

    <section class="footer-container">
        <section v-if="loading" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>
    </section>
</div>
