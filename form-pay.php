<?php
/**
 * WooCommerce Order Pay Form - Detailed Futuristic Edition
 * Path: [your-theme]/woocommerce/checkout/form-pay.php
 */

defined( 'ABSPATH' ) || exit;

// 1. DATA PREPARATION
// We pull from the Parent Order to get the Acowebs/Tyche details
$main_order_id = $order->get_parent_id() ? $order->get_parent_id() : $order->get_id();
$main_order    = wc_get_order( $main_order_id );
?>

<div style="background:#fcfcfc; padding:20px 15px 60px 15px; min-height: 80vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width:550px; margin:0 auto;">

        <div style="text-align:center; padding-bottom:25px;">
            <div style="width:50px; height:50px; background:#fff4e6; color:#d97706; border-radius:50%; line-height:50px; margin:0 auto 12px; font-size:20px; font-weight:bold;">!</div>
            <h2 style="margin:0; font-size:20px; color:#1a1a1a; font-weight:700;">Konfirmasi Pembayaran</h2>
            <p style="margin:5px 0 0; color:#666; font-size:14px;">Mohon periksa rincian pesanan Anda sebelum membayar.</p>
        </div>

        <div style="background:#fff; border-radius:16px; padding:25px; margin-bottom:20px; border:1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 style="margin:0 0 15px; font-size:12px; color:#999; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Rincian Layanan</h3>
            
            <?php foreach ( $main_order->get_items() as $item_id => $item ) : ?>
                <div style="padding-bottom:15px; margin-bottom:15px; border-bottom:1px dashed #eee;">
                    <div style="font-size:15px; font-weight:700; color:#1a1a1a; margin-bottom:10px;">
                        <?php echo esc_html( $item->get_name() ); ?>
                    </div>

                    <div style="background:#f9f9f9; padding:15px; border-radius:12px;">
                        <?php 
                        $formatted_meta = $item->get_all_formatted_meta_data('');
                        if ( $formatted_meta ) : ?>
                            <ul style="margin:0; padding:0; list-style:none;">
                                <?php foreach ( $formatted_meta as $meta ) : 
                                    // Skip internal hidden keys
                                    if ( strpos($meta->key, '_') === 0 ) continue; 
                                ?>
                                    <li style="font-size:12px; color:#555; margin-bottom:6px; display:flex; justify-content:space-between; gap:15px; line-height:1.4;">
                                        <span style="font-weight:600; color:#888; min-width:100px;"><?php echo wp_kses_post( $meta->display_key ); ?>:</span>
                                        <span style="text-align:right; color:#333; font-weight:500;"><?php echo wp_kses_post( $meta->display_value ); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <table width="100%" style="font-size:14px; margin-top:10px; border-collapse:collapse;">
                <?php foreach ( $order->get_order_item_totals() as $key => $total ) : 
                    $is_total = (strpos(strtolower($key), 'total') !== false);
                ?>
                    <tr>
                        <td style="padding:6px 0; color:#888;"><?php echo esc_html( $total['label'] ); ?></td>
                        <td align="right" style="padding:6px 0; color:#1a1a1a; font-weight:<?php echo $is_total ? '700' : '500'; ?>; font-size:<?php echo $is_total ? '16px' : '14px'; ?>;">
                            <?php echo wp_kses_post( $total['value'] ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <form id="order_review" method="post" style="background:#fff; border-radius:16px; padding:25px; border:1px solid #f0f0f0;">
            <div id="payment">
                <?php if ( $order->needs_payment() ) : ?>
                    <ul class="wc_payment_methods payment_methods methods" style="list-style:none; padding:0; margin:0 0 20px 0;">
                        <?php
                        if ( ! empty( $available_gateways ) ) {
                            foreach ( $available_gateways as $gateway ) {
                                wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                            }
                        } else {
                            echo '<li style="font-size:13px; color:#d93025;">Metode pembayaran tidak tersedia. Silakan hubungi admin.</li>';
                        }
                        ?>
                    </ul>
                <?php endif; ?>

                <div class="form-row">
                    <input type="hidden" name="woocommerce_pay" value="1" />
                    
                    <?php if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
                        <p style="font-size:12px; color:#888; margin-bottom:15px;">
                            <label>
                                <input type="checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', false ), true ); ?> id="terms" /> 
                                Saya setuju dengan <a href="<?php echo esc_url( wc_get_page_permalink( 'terms' ) ); ?>" target="_blank">Syarat & Ketentuan</a> *
                            </label>
                        </p>
                    <?php endif; ?>

                    <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>
                    
                    <button type="submit" class="button alt" id="place_order" value="Bayar Sekarang" data-value="Bayar Sekarang" style="width:100%; background:#00a37d; color:#fff; border:none; padding:16px; border-radius:12px; font-weight:700; font-size:14px; cursor:pointer; transition: 0.3s opacity;">
                        BAYAR SEKARANG
                    </button>

                    <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>
                    <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
                </div>
            </div>
        </form>

    </div>
</div>