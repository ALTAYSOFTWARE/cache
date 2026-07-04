<?php
namespace WCU;

if ( ! defined( 'ABSPATH' ) ) exit;

class Core {
    public static function init() {
        require_once WCU_DIR . 'includes/Cleaner.php';
        require_once WCU_DIR . 'admin/Dashboard.php';
        require_once WCU_DIR . 'admin/Settings.php';
        require_once WCU_DIR . 'includes/Integrations/CommonPurge.php';
        require_once WCU_DIR . 'includes/Integrations/Cloudflare.php';
        require_once WCU_DIR . 'includes/Integrations/WP_Rocket.php';
        require_once WCU_DIR . 'includes/Integrations/RedisAdapter.php';

        // Admin UI
        add_action( 'admin_menu', array( 'WCU\\Dashboard', 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( 'WCU\\Dashboard', 'enqueue_assets' ) );

        // Settings registration (no submenu duplication)
        add_action( 'admin_init', array( 'WCU\\Settings', 'register_settings' ) );

        // AJAX endpoints
        add_action( 'wp_ajax_wcu_start_job', array( 'WCU\\Cleaner', 'ajax_start_job' ) );
        add_action( 'wp_ajax_wcu_process_step', array( 'WCU\\Cleaner', 'ajax_process_step' ) );
        add_action( 'wp_ajax_wcu_get_job', array( 'WCU\\Cleaner', 'ajax_get_job' ) );
        add_action( 'wp_ajax_wcu_quick_action', array( 'WCU\\Cleaner', 'ajax_quick_action' ) );

        // Cron processing
        add_action( 'wcu_process_job_cron', array( 'WCU\\Cleaner', 'cron_process' ) );

        // WP-CLI
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            \WP_CLI::add_command( 'wcu clear', function() {
                $res = Cleaner::run_all_blocking();
                \WP_CLI::success( 'WCU: clear finished' );
                \WP_CLI::log( wp_json_encode( $res ) );
            } );
        }
    }

    public static function activate() {
        if ( ! get_option( 'wcu_settings' ) ) {
            add_option( 'wcu_settings', array(
                'transient_batch' => 500,
                'cloudflare_token' => '',
                'cloudflare_zone'  => '',
                'redis_enabled' => false,
                'redis_host' => '127.0.0.1',
                'redis_port' => 6379,
                'redis_password' => '',
            ) );
        }

        if ( ! wp_next_scheduled( 'wcu_process_job_cron' ) ) {
            add_filter( 'cron_schedules', function( $s ) {
                if ( ! isset( $s['wcu_every_minute'] ) ) {
                    $s['wcu_every_minute'] = array( 'interval' => 60, 'display' => 'Every Minute' );
                }
                return $s;
            } );
            wp_schedule_event( time(), 'wcu_every_minute', 'wcu_process_job_cron' );
        }
    }

    public static function deactivate() {
        $ts = wp_next_scheduled( 'wcu_process_job_cron' );
        if ( $ts ) wp_unschedule_event( $ts, 'wcu_process_job_cron' );
    }
}
