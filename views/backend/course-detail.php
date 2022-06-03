<?php
// $items
// $id_products
// $id_course

use dcms\orders\helpers\Helper;

defined( 'ABSPATH' ) || exit;
$return_url = admin_url() . DCMS_ORDERS_SUBMENU . '?page='. DCMS_ORDERS_MAINPAGE;
$item_total = 0;
$total_paid = 0;
$total_pending = 0;

?>
<div id="report-course" class="wrap" >

    <h1><?= $course_name ?></h1>

        <section class="report-header">
            <div>
                <a href="<?= $return_url ?>" class="button">Regresar</a>
            </div>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
                <div class="export-box">
                    <input type="submit" id="export" name="export" class="button" value="Exportar">
                    <input type="hidden" name="id_products" value="<?= implode(',',$id_products) ?>">
                    <input type="hidden" name="id_course" value="<?= $id_course ?>">
                    <input type="hidden" name="action" value="export_course">
                </div>
            </form>
        </section>

    <div style="overflow-x: auto;">
        <table class="dcms-table-report dcms-table-details striped">
            <tr>
                <th>Nombre</th>
                <th>#Orden</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Pendiente</th>
                <th>Flexible</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <?php
                    $item_total += $item['item_total'];
                    $total_paid += $item['total_paid'];
                    $total_pending += $item['total_pending'];
                ?>
                <tr>
                    <td><?= $item['user_name'] . ' ' . $item['user_lastname'] ?></td>
                    <td>
                    <?= Helper::generate_order_links( $item['order_id'] ); ?>
                    </td>
                    <td><?= number_format($item['item_total'], 2) ?></td>
                    <td><?= number_format($item['total_paid'], 2) ?></td>
                    <td><?= number_format($item['total_pending'], 2) ?></td>
                    <td><?= $item['flexible']?'Si':'No' ?></td>
                </tr>
            <?php endforeach; ?>
            <tfoot>
                <tr>
                    <td>Totales</td>
                    <td></td>
                    <td><?= number_format($item_total, 2) ?></td>
                    <td><?= number_format($total_paid, 2) ?></td>
                    <td><?= number_format($total_pending, 2) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

