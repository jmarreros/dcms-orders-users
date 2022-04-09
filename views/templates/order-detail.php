<?php
/**
 * Order details Summary
 *
 * This template displays a summary of original order details, based in the original template plugin deposits
 *
 * $current_url : current url pass throughout shortcode
 * $order: Pass order object
 *
 */

defined( 'ABSPATH' ) || exit;

// $order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited


$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
    wc_get_template(
        'order/order-downloads.php',
        array(
            'downloads'  => $downloads,
            'show_title' => true,
        )
    );
}

?>
<section class="order-detail">
    <section class="order-resume">
        <div>
        <?php
            printf(
                /* translators: 1: order number 2: order date 3: order status */
                esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
                '<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                '<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                '<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
        ?>
        </div>
        <div>
            <a href="<?= $current_url ?>"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
        </div>
    </section>

    <section class="woocommerce-order-details">
        <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

        <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

        <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

            <thead>
            <tr>
                <th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php
            do_action( 'woocommerce_order_details_before_order_table_items', $order );

            foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();

                wc_get_template(
                    'order/order-details-item.php',
                    array(
                        'order'              => $order,
                        'item_id'            => $item_id,
                        'item'               => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note'      => $product ? $product->get_purchase_note() : '',
                        'product'            => $product,
                    )
                );
            }

            do_action( 'woocommerce_order_details_after_order_table_items', $order );
            ?>
            </tbody>

            <tfoot>
            <?php
            foreach ( $order->get_order_item_totals() as $key => $total ) {
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                    <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
                </tr>
                <?php
            }
            ?>
            <?php if ( $order->get_customer_note() ) : ?>
                <tr>
                    <th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                    <td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                </tr>
            <?php endif; ?>
            </tfoot>
        </table>

        <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
    </section>


    <?php
    //show partial payments summary

    if(apply_filters('wc_deposits_thankyou_show_partial_payments_summary',true,$order)){

        $payment_schedule = $order->get_meta('_wc_deposits_payment_schedule', true);

        if ( is_array($payment_schedule) ){
            wc_get_template(
                'order/wc-deposits-partial-payments-summary.php', array(
                'order_id' => $order->get_id(),
                'schedule' => $payment_schedule
            ),
                '',
                WC_DEPOSITS_TEMPLATE_PATH
            );
        }
    }

    ?>

    <?php
    if ( $show_customer_details ) {
        wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
    }
    ?>

</section>