
<div id="orders-user">
    <table>
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
</div>