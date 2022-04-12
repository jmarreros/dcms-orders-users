<?php
/**
 * Custom template for listing orders using Vue.js and Ajax
 *
 *  $current_url : Current page url
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="orders-user">

    <table id="list-orders">
        <tr>
            <th>Pedido</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Depósito</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        <tr v-for="item in results" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.date }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.deposit ? 'Si' : 'No' }}</td>
            <td v-html="item.total"></td>
            <td>
                <section class="container-actions" :class="loading && 'disabled'">
                    <a :href="'<?= $current_url ?>?order=' + item.id" class="button" >Ver</a>
                    <a :href="'<?= $current_url ?>?order=' + item.id + '&action=attach'" class="button">Adjuntar</a>
                    <a v-if="item.payment_url" :href="item.payment_url" class="button">Pagar</a>
                </section>
            </td>
        </tr>
    </table>
    <div v-if="!results.length && !loading" class="no-items">No tienes aún ninguna orden</div>

    <section class="footer-container">
        <section v-if="loading" class="loading-container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </section>
        <section class="pagination-container" v-if="results.length" :class="!loading && 'fullwidth'">
            <a id="prev" href="#" v-on:click="prevPage" v-if="page > 1" :class="loading && 'disabled'">Anterior</a>
            <a id="next" href="#"  v-on:click="nextPage" v-if="page < totalPages" :class="loading && 'disabled'">Siguiente</a>
        </section>
    </section>
</div>
