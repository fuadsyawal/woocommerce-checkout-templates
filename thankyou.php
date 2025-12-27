<?php
/**
 * WooCommerce Custom Thank You Page - THE FINAL EDITION
 * Features: Copy to Clipboard, Soft Typography, Futuristic Timeline, and Robust Redirects.
 */

defined( 'ABSPATH' ) || exit;

// --- 1. SAFETY GUARD ---
if ( ! isset( $order ) || ! is_object( $order ) ) {
    $url_order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
    if ( $url_order_id ) { $order = wc_get_order( $url_order_id ); }
}

if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
    echo '<div style="text-align:center; padding:50px 20px;"><h3>Pesanan tidak ditemukan.</h3></div>';
    return;
}

// --- 2. CLEANUP ---
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
remove_action( 'woocommerce_thankyou', 'woocommerce_customer_details', 20 );

// --- 3. IDENTIFIKASI DATA ---
$parent_id = $order->get_parent_id();
$main_order_id = ( $parent_id && $parent_id > 0 ) ? $parent_id : $order->get_id();
$main_order = wc_get_order( $main_order_id );

$schedule = $main_order->get_meta( '_awcdp_deposits_payment_schedule', true );
$is_fully_paid = ( $main_order->has_status( 'completed' ) );
$current_status_paid = $order->has_status( array( 'completed', 'processing' ) );

// --- 4. MIDTRANS RETRY LOGIC ---
$mt_redirect = $order->get_meta( '_mt_payment_url' );
if ( empty( $mt_redirect ) && is_array( $schedule ) ) {
    if ( ! empty( $schedule['unlimited']['id'] ) ) {
        $second_order = wc_get_order( absint( $schedule['unlimited']['id'] ) );
        if ( $second_order ) { $mt_redirect = $second_order->get_meta( '_mt_payment_url' ); }
    }
    if ( empty( $mt_redirect ) && ! empty( $schedule['deposit']['id'] ) ) {
        $deposit_order = wc_get_order( absint( $schedule['deposit']['id'] ) );
        if ( $deposit_order ) { $mt_redirect = $deposit_order->get_meta( '_mt_payment_url' ); }
    }
}
if ( empty( $mt_redirect ) ) { $mt_redirect = $order->get_checkout_payment_url(); }
?>

<div style="background:#fcfcfc; padding:10px 15px 60px 15px; min-height: 80vh; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width:550px; margin:0 auto;">

        <?php if ( $current_status_paid ) : ?>
            <div style="text-align:center; padding:10px 0 25px;">
                <div style="width:50px; height:50px; background:#e6f9ed; color:#00a37d; border-radius:50%; line-height:50px; margin:0 auto 12px; font-size:22px;">✓</div>
                <h2 style="margin:0; font-size:20px; color:#1a1a1a; font-weight:600;">Pembayaran Berhasil</h2>
                <p style="margin:5px 0 0; color:#666; font-size:14px;">Terima kasih <strong><?php echo esc_html( $order->get_billing_first_name() ); ?></strong>, pesanan Anda telah kami terima.</p>
            </div>
        <?php else : ?>
            <div style="text-align:center; margin-bottom:20px; padding:30px; background:#fff; border-radius:16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border:1px solid #f0f0f0;">
                <div style="width:50px; height:50px; background:#fff4e6; color:#d97706; border-radius:50%; line-height:50px; margin:0 auto 12px; font-size:20px;">!</div>
                <h2 style="margin:0; font-size:18px; color:#1a1a1a; font-weight:600;">Menunggu Pembayaran</h2>
                <p style="margin:8px 0 20px; color:#666; font-size:13px;">Silakan selesaikan transaksi Anda untuk mengakses file pesanan.</p>
                <a href="<?php echo esc_url( $mt_redirect ); ?>" style="display:block; background:#00a37d; color:#fff; padding:12px; border-radius:10px; text-decoration:none; font-weight:600; font-size:13px;">BAYAR SEKARANG</a>
            </div>
        <?php endif; ?>

        <?php if ( $is_fully_paid ) : 
            $downloads = $main_order->get_downloadable_items();
            $custom_link = trim( (string) get_post_meta( $main_order_id, 'download_links', true ) );
            if ( ! empty( $custom_link ) || ! empty( $downloads ) ) :
        ?>
            <div style="background:#fff; border-radius:16px; padding:25px; margin-bottom:20px; border:1px solid #eef2f1; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <h3 style="margin:0 0 15px; font-size:14px; color:#1a1a1a; font-weight:700;">Akses Unduhan</h3>
                
                <div style="font-size:13px; color:#555; background:#f9f9f9; padding:15px; border-radius:12px; margin-bottom:20px; border-left: 3px solid #00a37d;">
                    <p style="margin:0 0 10px 0;">Akses link akan aktif selama <strong>2 bulan</strong> sejak hari ini.</p>
                    <p style="margin:0 0 10px 0;">Untuk menghindari kehilangan file, mohon segera unduh dan simpan foto Anda.</p>
                    <p style="margin:0; font-size:12px; color:#888;"><em><strong>Catatan:</strong> Kami menyimpan backup sementara untuk keperluan internal, namun kami tidak menjamin ketersediaannya setelah link kadaluarsa.</em></p>
                </div>

                <?php if ( ! empty( $custom_link ) ) : ?>
                    <div style="margin-bottom:10px;">
                        <a href="<?php echo esc_url( $custom_link ); ?>" target="_blank" style="display:block; text-align:center; background:#00a37d; color:#fff; padding:12px; border-radius:10px; text-decoration:none; font-weight:700; font-size:13px; margin-bottom:8px;">
                            BUKA LINK CLOUD STORAGE
                        </a>
                        <button onclick="copyToClipboard('<?php echo esc_url($custom_link); ?>', this)" style="width:100%; background:#fff; border:1px solid #ddd; color:#666; padding:10px; border-radius:10px; cursor:pointer; font-size:11px; font-weight:600; transition:all 0.2s;">
                            🔗 SALIN LINK UNTUK BERBAGI
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $downloads ) ) : ?>
                    <?php foreach ( $downloads as $download ) : ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; background:#fff; border:1px solid #eee; padding:10px 15px; border-radius:10px; margin-bottom:8px;">
                            <span style="font-size:12px; color:#333; font-weight:500;"><?php echo esc_html( $download['product_name'] ); ?></span>
                            <div style="display:flex; gap:10px;">
                                <span onclick="copyToClipboard('<?php echo esc_url($download['download_url']); ?>', this)" style="color:#999; cursor:pointer; font-size:11px; font-weight:700;">SALIN</span>
                                <a href="<?php echo esc_url( $download['download_url'] ); ?>" style="color:#00a37d; text-decoration:none; font-size:11px; font-weight:700; text-transform:uppercase;">Unduh</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div style="background:#fff; border-radius:16px; padding:25px; margin-bottom:20px; border:1px solid #f0f0f0;">
            <h3 style="margin:0 0 15px; font-size:13px; color:#999; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Rincian Pesanan</h3>
            <table width="100%" style="font-size:14px; border-collapse:collapse;">
                <?php 
                $display_order = $is_fully_paid ? $main_order : $order;
                foreach ( $display_order->get_order_item_totals() as $key => $total ) : 
                    if ( $is_fully_paid && strpos( strtolower($total['label']), 'sisa' ) !== false ) continue;
                    $is_total = (strpos(strtolower($key), 'total') !== false);
                ?>
                    <tr>
                        <td align="left" style="padding:8px 0; color:#666; font-weight:400;"><?php echo esc_html( $total['label'] ); ?></td>
                        <td align="right" style="padding:8px 0; color:#1a1a1a; font-weight:<?php echo $is_total ? '600' : '400'; ?>; font-size:<?php echo $is_total ? '16px' : '14px'; ?>;"><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <?php if ( is_array( $schedule ) ) : ?>
            <div style="padding:5px 20px;">
                <h4 style="font-size:11px; color:#bbb; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:15px; font-weight:700;">Riwayat Pembayaran</h4>
                <?php 
                    $dp_done = false; $final_done = false;
                    if (!empty($schedule['deposit']['id'])) {
                        $dp_o = wc_get_order($schedule['deposit']['id']);
                        if($dp_o && $dp_o->has_status(['completed','processing'])) $dp_done = true;
                    }
                    if (!empty($schedule['unlimited']['id'])) {
                        $fn_o = wc_get_order($schedule['unlimited']['id']);
                        if($fn_o && $fn_o->has_status(['completed','processing'])) $final_done = true;
                    }
                ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <div style="display:flex; align-items:center;">
                        <div style="width:8px; height:8px; background:<?php echo $dp_done ? '#00a37d' : '#ddd'; ?>; border-radius:50%; margin-right:12px;"></div>
                        <span style="font-size:13px; color:<?php echo $dp_done ? '#1a1a1a' : '#aaa'; ?>;">DP (Down Payment)</span>
                    </div>
                    <span style="font-size:11px; font-weight:700; color:<?php echo $dp_done ? '#00a37d' : '#d97706'; ?>;"><?php echo $dp_done ? 'LUNAS' : 'PENDING'; ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center;">
                        <div style="width:8px; height:8px; background:<?php echo $final_done ? '#00a37d' : '#ddd'; ?>; border-radius:50%; margin-right:12px;"></div>
                        <span style="font-size:13px; color:<?php echo $final_done ? '#1a1a1a' : '#aaa'; ?>;">Pelunasan</span>
                    </div>
                    <span style="font-size:11px; font-weight:700; color:<?php echo $final_done ? '#00a37d' : '#d97706'; ?>;"><?php echo $final_done ? 'LUNAS' : 'PENDING'; ?></span>
                </div>
            </div>
        <?php endif; ?>
<?php 
/** * Show refresh notice only for fully paid orders 
 * (Filters out 'pending', 'on-hold', and 'partially-paid')
 */
if ( $order->has_status( array( 'processing', 'completed' ) ) ) : 
?>
    <div style="margin-top: 10px; padding: 10px; background: #fdfaea; border-radius: 8px; border: 1px solid #f5e79e; font-size: 13px; line-height: 1.4; color: #7a6a1a; text-align: center;">
        Link belum muncul? <a href="javascript:location.reload();" style="font-weight: 600; color: #00a37d; text-decoration: none;">Refresh Halaman</a>. 
        Masih Bermasalah? Hubungi <a href="https://wa.me/6285175223948" style="font-weight: 600; color: #00a37d; text-decoration: none;">WhatsApp</a>.
    </div>
<?php endif; ?>

<script>
function copyToClipboard(text, element) {
    var dummy = document.createElement("textarea");
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
    
    var originalText = element.innerHTML;
    element.innerHTML = "✅ BERHASIL DISALIN!";
    element.style.color = "#00a37d";
    
    setTimeout(function(){
        element.innerHTML = originalText;
        element.style.color = "";
    }, 2000);
}
</script>