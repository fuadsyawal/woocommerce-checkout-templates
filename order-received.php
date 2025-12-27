<?php
defined( 'ABSPATH' ) || exit;

/** @var WC_Order $order */

$white      = '#ffffff';
$canvas_bg = '#f7f7f7';
$text      = '#333';
$heading   = '#00a37d';
$success   = '#00d64f';

// ACOWEB META (proven working)
$schedule = $order->get_meta( '_awcdp_deposits_payment_schedule', true );

// Custom download link
$custom_download = trim( (string) $order->get_meta( 'download_links' ) );
?>

<?php
$order_status = $order->get_status();

$status_label = match ($order_status) {
    'completed' => 'Pembayaran Selesai',
    'processing' => 'Sedang Diproses',
    'on-hold' => 'Menunggu Pembayaran',
    'pending' => 'Belum Dibayar',
    'failed' => 'Pembayaran Gagal',
    default => ucfirst($order_status),
};
?>
<?php
$payment_page = $order->get_meta('_midtrans_redirect_url');

if ( ! $payment_page ) {
    $payment_page = $order->get_checkout_payment_url();
}
?>
<?php
// Resolve parent order (Acoweb DP safe)
$parent_order = $order->get_parent_id()
    ? wc_get_order( $order->get_parent_id() )
    : $order;

// Custom download link (single link)
$custom_download_link = trim( (string) $parent_order->get_meta( 'download_links' ) );

// WooCommerce digital downloads
$downloads = $order->get_downloadable_items();

// Show section only if at least one source exists
if ( ! empty( $custom_download_link ) || ! empty( $downloads ) ) :

$parent_order_id = $order->get_parent_id() ? $order->get_parent_id() : $order->get_id();
$custom_download_link = get_post_meta( $parent_order_id, 'download_links', true );
?>




<div style="background:<?php echo esc_attr( $canvas_bg ); ?>;padding:16px;">
<div style="max-width:680px;margin:0 auto;">

<!-- INTRO -->
<div style="background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;">
    <p style="margin:0;font-size:15px;">
        Terima kasih <strong><?php echo esc_html( $order->get_billing_first_name() ); ?></strong>,
        pembayaran Anda telah kami terima.
    </p>
</div>

<div style="
    background:#e6f7f2;
    border-radius:10px;
    padding:16px;
    margin-bottom:24px;
    font-size:14px;
    color:#155f4a;
">
    <strong>Informasi penting:</strong><br>
    Salinan detail pesanan dan pembayaran telah dikirim ke email Anda.
    <br>Anda dapat menutup halaman ini dengan aman dan merujuk kembali ke email
    kapan saja untuk mengakses informasi pesanan dan unduhan Anda.
</div>
	
<!-- CUSTOM DOWNLOAD LINK -->
<div style="background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;">

    <h3 style="margin:0 0 12px;color:#00a37d;">
        Unduhan
    </h3>

    <!-- Intro text (always shown if section exists) -->
    <p style="margin:0 0 16px;font-size:14px;">
        File Anda sudah siap. Silakan unduh melalui tautan di bawah ini.
    </p>

	<?php if ( ! empty( $custom_download_link ) ) : ?>
<div style="
    margin:12px 0 16px;
    padding:10px 14px;
    background:#fff6e5;
    border-left:4px solid #f4a261;
    border-radius:6px;
    font-size:13px;
    color:#7a4a00;
">
    ⏳ <strong>Penting:</strong>  
    Unduhan tambahan ini hanya tersedia selama
    <strong>2 bulan</strong>.  
    Mohon segera simpan file Anda sebelum masa berlaku berakhir.
</div>
<?php endif; ?>


    <!-- Custom download link (shown first) -->
    <?php if ( ! empty( $custom_download_link ) ) : ?>
        <div style="margin-bottom:20px;">
            <a href="<?php echo esc_url( $custom_download_link ); ?>"
               target="_blank"
               style="
                   display:inline-block;
                   background:#00a37d;
                   color:#fff;
                   padding:10px 16px;
                   border-radius:6px;
                   text-decoration:none;
                   font-weight:600;
                   font-size:14px;
               ">
                Download Sekarang
            </a>
        </div>
    <?php endif; ?>

    <!-- WooCommerce digital downloads table -->
    <?php if ( ! empty( $downloads ) ) : ?>
        <table width="100%" cellpadding="6" style="font-size:14px;border-collapse:collapse;">
            <thead>
            <tr style="background:#eef2f3;">
                <th align="left">Produk</th>
                <th align="left">Kadaluarsa</th>
                <th align="left">Unduh</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $downloads as $download ) : ?>
                <tr>
                    <td><?php echo esc_html( $download['product_name'] ); ?></td>
                    <td>
                        <?php
                        echo $download['access_expires']
                            ? esc_html( date_i18n( 'd F Y', strtotime( $download['access_expires'] ) ) )
                            : esc_html__( 'Tidak pernah', 'woocommerce' );
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url( $download['download_url'] ); ?>"
                           style="color:#00a37d;font-weight:600;">
                            <?php esc_html_e( 'Download', 'woocommerce' ); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php endif; ?>


<!-- ORDER SUMMARY -->
<div style="background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;">
    <h3 style="margin:0 0 12px;color:<?php echo esc_attr( $heading ); ?>;">
        Ringkasan Pesanan
    </h3>

    <table width="100%" cellpadding="6" style="font-size:14px;">
        <?php foreach ( $order->get_items() as $item ) : ?>
        <tr>
            <td><?php echo esc_html( $item->get_name() ); ?> × <?php echo esc_html( $item->get_quantity() ); ?></td>
            <td align="right"><?php echo wc_price( $item->get_total(), [ 'currency' => $order->get_currency() ] ); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <table width="100%" cellpadding="6" style="margin-top:10px;font-size:14px;">
        <?php foreach ( $order->get_order_item_totals() as $total ) : ?>
        <tr>
            <td align="right"><strong><?php echo esc_html( $total['label'] ); ?></strong></td>
            <td align="right"><?php echo wp_kses_post( $total['value'] ); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- LUNAS BADGE -->
	<div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
    <span style="
        background:#00d64f;
        color:#fff;
        padding:6px 14px;
        border-radius:999px;
        font-size:13px;
        font-weight:600;
    ">
        LUNAS
    </span>
</div>


<!-- PARTIAL PAYMENT DETAILS -->
<?php if ( is_array( $schedule ) && ! empty( $schedule ) ) : ?>
<div style="background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;">
    <h3 style="margin:0 0 12px;color:<?php echo esc_attr( $heading ); ?>;">
        Detail Pembayaran
    </h3>

    <table width="100%" cellpadding="6" style="font-size:14px;">
        <thead>
        <tr style="background:#eef2f3;">
            <th align="left">Tahap</th>
            <th align="right">Jumlah</th>
            <th align="left">Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ( $schedule as $row ) :
            $label = $row['title'] === 'Deposit' ? 'DP' : 'Pelunasan';
        ?>
        <tr>
            <td><?php echo esc_html( $label ); ?></td>
            <td align="right"><?php echo wc_price( $row['total'], [ 'currency' => $order->get_currency() ] ); ?></td>
            <td>Selesai</td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div>
</div>
