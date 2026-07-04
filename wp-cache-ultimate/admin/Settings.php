<?php
namespace WCU;
if ( ! defined( 'ABSPATH' ) ) exit;
class Settings {
    const OPTION_NAME = 'wcu_settings';
    public static function register_settings() {
        register_setting( 'wcu_settings_group', self::OPTION_NAME, array( __CLASS__, 'sanitize' ) );
        add_settings_section( 'wcu_general', 'Genel Ayarlar', null, 'wcu-settings' );
    }

    public static function sanitize( $in ) {
        $out = array();
        $out['transient_batch'] = max(50, (int) ($in['transient_batch'] ?? 500) );
        $out['cloudflare_token'] = sanitize_text_field( $in['cloudflare_token'] ?? '' );
        $out['cloudflare_zone'] = sanitize_text_field( $in['cloudflare_zone'] ?? '' );
        $out['redis_enabled'] = ! empty( $in['redis_enabled'] ) ? true : false;
        $out['redis_host'] = sanitize_text_field( $in['redis_host'] ?? '127.0.0.1' );
        $out['redis_port'] = (int) ($in['redis_port'] ?? 6379);
        $out['redis_password'] = sanitize_text_field( $in['redis_password'] ?? '' );
        return $out;
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Yetkisiz' );
        $s = get_option( self::OPTION_NAME, array() );
        ?>
        <div class="wrap">
            <h1>WCU Ayarlar</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'wcu_settings_group' ); do_settings_sections( 'wcu-settings' ); ?>
                <table class="form-table">
                    <tr><th>Transient batch</th><td><input name="wcu_settings[transient_batch]" value="<?php echo esc_attr( $s['transient_batch'] ?? 500 ); ?>" /></td></tr>
                    <tr><th>Cloudflare Token</th><td><input size="60" name="wcu_settings[cloudflare_token]" value="<?php echo esc_attr( $s['cloudflare_token'] ?? '' ); ?>" /></td></tr>
                    <tr><th>Cloudflare Zone ID</th><td><input name="wcu_settings[cloudflare_zone]" value="<?php echo esc_attr( $s['cloudflare_zone'] ?? '' ); ?>" /></td></tr>
                    <tr><th colspan="2"><strong>Redis (opsiyonel)</strong></th></tr>
                    <tr><th>Etkin</th><td><input type="checkbox" name="wcu_settings[redis_enabled]" value="1" <?php checked( $s['redis_enabled'] ?? false, true ); ?> /></td></tr>
                    <tr><th>Host</th><td><input name="wcu_settings[redis_host]" value="<?php echo esc_attr( $s['redis_host'] ?? '127.0.0.1' ); ?>" /></td></tr>
                    <tr><th>Port</th><td><input name="wcu_settings[redis_port]" value="<?php echo esc_attr( $s['redis_port'] ?? 6379 ); ?>" /></td></tr>
                    <tr><th>Password</th><td><input name="wcu_settings[redis_password]" value="<?php echo esc_attr( $s['redis_password'] ?? '' ); ?>" /></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
