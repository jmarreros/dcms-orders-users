<?php
// $data : asociative array of data
?>
<table>
  <tr>
    <th>Pedido</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Depósito</th>
    <th>Total</th>
    <th>Acciones</th>
  </tr>

  <?php foreach ($data as $row): ?>
    <tr>
      <td>
        <a href="#" class="dcms-show-order" data-order="<?= $row['id'] ?>"><?= $row['id'] ?></a>
      </td>
      <td><?= $row['date'] ?></td>
      <td><?= $row['status'] ?></td>
      <td><?= $row['deposit']?'Si':'No' ?></td>
      <td><?= $row['total'] ?></td>
      <td>
        <a href="#" class="dcms-show-order" data-order="<?= $row['id'] ?>">Ver</a>
        <a href="#" class="dcms-pay-order" data-order="<?= $row['id'] ?>">Pagar</a>
        <a href="#" class="dcms-attach-order" data-order="<?= $row['id'] ?>">Adjuntos</a>
      </td>
    </tr>
  <?php endforeach; ?>

</table>