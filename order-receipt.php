<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div style="max-width:640px;margin:0 auto;padding:20px;">

	<!-- Intro Card -->
	<div style="background:#ffffff;border-radius:16px;padding:24px;margin-bottom:20px;">
		<h2 style="margin:0 0 8px;font-size:20px;">
			Pesanan Siap Diproses
		</h2>

		<p style="margin:0;font-size:14px;color:#555;">
			Detail pesanan Anda telah kami terima.
			Anda akan segera diarahkan ke halaman pembayaran untuk menyelesaikan transaksi.
		</p>
	</div>

	<!-- Order Summary -->
	<div style="background:#ffffff;border-radius:16px;padding:20px;margin-bottom:20px;">
		<table width="100%" cellpadding="6" style="font-size:14px;">
			<tr>
				<td>Nomor Pesanan</td>
				<td align="right"><strong><?php echo esc_html( $order->get_order_number() ); ?></strong></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td align="right"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
			</tr>
			<tr>
				<td>Total Pembayaran</td>
				<td align="right"><strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></td>
			</tr>
			<?php if ( $order->get_payment_method_title() ) : ?>
			<tr>
				<td>Metode Pembayaran</td>
				<td align="right"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></td>
			</tr>
			<?php endif; ?>
		</table>
	</div>

	<!-- Notice -->
	<div style="font-size:13px;color:#666;text-align:center;margin-bottom:16px;">
		Jika halaman pembayaran tidak terbuka otomatis,
		silakan tunggu beberapa saat atau aktifkan pop-up di browser Anda.
	</div>

<div class="custom-payment-action">
	<?php
	do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() );
	?>
</div>


</div>
