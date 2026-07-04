<?php
namespace WCU;
if ( ! defined( 'ABSPATH' ) ) exit;
class Dashboard {
    public static function register_menu() {
        add_menu_page( 'WP Cache Ultimate', 'WP Cache Ultimate', 'manage_options', 'wcu-dashboard', array( __CLASS__, 'render_page' ), 'dashicons-performance', 58 );
        add_submenu_page( 'wcu-dashboard', 'Ayarlar', 'Ayarlar', 'manage_options', 'wcu-settings', array( 'WCU\\Settings', 'render_page' ) );
    }

    public static function enqueue_assets( $hook ) {
        if ( $hook !== 'toplevel_page_wcu-dashboard' && $hook !== 'wcu_page_wcu-settings' ) return;
        wp_enqueue_style( 'wcu-admin-css', WCU_URL . 'assets/css/admin.css', array(), WCU_VERSION );
        wp_enqueue_script( 'wcu-admin-js', WCU_URL . 'assets/js/dashboard.js', array( 'jquery' ), WCU_VERSION, true );
        wp_localize_script( 'wcu-admin-js', 'WCU', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'wcu_clear_nonce' ),
        ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Yetkisiz' );
        $job = get_option( Cleaner::JOB_OPTION, null );
        ?>
        <div class="wrap wcu-wrap">
            <div class="wcu-topbar">
                <div class="wcu-title">
                    <h1>WP Cache Ultimate <span class="wcu-pro">PRO</span></h1>
                    <p class="wcu-sub">Gelişmiş önbellek temizleme & optimizasyon</p>
                </div>
                <div class="wcu-actions">
                    <button id="wcu-clear-red" class="wcu-btn wcu-btn-danger">Tüm Önbelleği Temizle</button>
                    <a href="<?php echo admin_url( 'admin.php?page=wcu-settings' ); ?>" class="wcu-btn wcu-btn-outline">Ayarlar</a>
                </div>
            </div>

            <div class="wcu-grid">
                <aside class="wcu-left">
                    <nav class="wcu-sidebar">
                        <ul>
                            <li class="active">Dashboard</li>
                            <li>Önbellek</li>
                            <li>Ön Bellekleme</li>
                            <li>Optimizasyon</li>
                            <li>Veritabanı</li>
                            <li>Ayarlar</li>
                        </ul>
                    </nav>
                </aside>

                <main class="wcu-main">
                    <section class="wcu-cards">
                        <div class="card"><div class="label">Önbellek Boyutu</div><div class="value" id="wcu-cache-size">--</div></div>
                        <div class="card"><div class="label">Önbelleğe Alınan Sayfa</div><div class="value" id="wcu-cache-count">--</div></div>
                        <div class="card"><div class="label">Sayfa Önbelleği</div><div class="value" id="wcu-page-cache">--</div></div>
                        <div class="card"><div class="label">Nesne Önbellek</div><div class="value" id="wcu-object-cache">--</div></div>
                    </section>

                    <section class="wcu-dashboard-main">
                        <div class="wcu-score">
                            <div class="score-circle" id="wcu-score-val">100</div>
                            <div class="score-text">Optimizasyon Seviyeniz</div>
                        </div>

                        <div class="wcu-quick">
                            <h3>Hızlı İşlemler</h3>
                            <div class="quick-list">
                                <button class="button wcu-quick-btn" data-action="clear_all">Tüm Önbelleği Temizle</button>
                                <button class="button wcu-quick-btn" data-action="clear_transients">Transients Temizle</button>
                                <button class="button wcu-quick-btn" data-action="clear_object">Nesne Önbelleği Temizle</button>
                                <button class="button wcu-quick-btn" data-action="clear_cache_dirs">Cache Dizinlerini Temizle</button>
                            </div>
                        </div>

                        <div id="wcu-result" class="wcu-result" style="display:<?php echo $job && isset($job['status']) && $job['status'] === 'running' ? 'block' : 'none' ?>">
                            <h3>İş Durumu</h3>
                            <pre id="wcu-result-pre"><?php echo esc_html( wp_json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); ?></pre>
                            <div id="wcu-progress">İlerleme: <span id="wcu-progress-val"><?php echo esc_html( $job['progress'] ?? 0 ); ?></span>%</div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
        <?php
    }
}
