<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    $total = 0;
?>
<?php if ( $payments && ! empty ( $payments ) ) : ?>
    <?php foreach ( $payments as $i => $payment ) : ?>
    <tr>
        <td><?php echo date( get_option( 'date_format' ), strtotime( $payment['created'] ) ) ?></td>
        <td><?php echo AB_Payment::typeToString( $payment['type'] ) ?></td>
        <td><?php echo $payment['customer'] ?></td>
        <td><?php echo $payment['provider'] ?></td>
        <td><?php echo esc_html( $payment['service'] ) ?></td>
        <td><div class="text-right"><?php echo AB_Utils::formatPrice( $payment['total'] ) ?></div></td>
        <td><?php if ( $payment['type'] != AB_Payment::TYPE_LOCAL ) echo AB_Payment::statusToString( $payment['status'] ) ?></td>
        <td><?php echo $payment['coupon'] ?></td>
        <td><?php if ( $payment['start_date'] ) echo date( get_option( 'date_format' ), strtotime( $payment['start_date'] ) ) ?></td>
        <?php $total += $payment['total'] ?>
    </tr>
    <?php endforeach ?>
    <tr>
        <td colspan=6><div class=pull-right><strong><?php _e( 'Total', 'bookly' ) ?>: <?php echo AB_Utils::formatPrice( $total ) ?></strong></div></td>
        <td colspan=3></td>
    </tr>
<?php endif ?>