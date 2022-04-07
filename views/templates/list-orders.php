
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
            <td>{{ item.deposit }}</td>
            <td v-html="item.total"></td>
            <td></td>
        </tr>
    </table>
    <div v-if="!results.length && !loading" class="no-items">No tienes aún ninguna orden</div>
    <div v-if="loading" class="loading-container">
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>


    <section id="pagination" v-if="results.length">
        <a id="prev" href="#">Anterior</a>
        <a id="next" href="#">Siguiente</a>
    </section>
</div>
