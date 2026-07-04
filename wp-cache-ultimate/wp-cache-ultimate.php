<?php
/**
 * Plugin Name: WP Cache Ultimate (Modern UI)
 * Plugin URI:  https://bilgikasabasi.com
 * Description: Gelişmiş cache yönetimi: modern dashboard, background job, Cloudflare/Redis/WP Rocket entegrasyonları, WP-CLI.
 * Version:     1.0.2
 * Author:      Altay Yazılım
 * Text Domain: wp-cache-ultimate
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License:     GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WCU_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCU_URL', plugin_dir_url( __FILE__ ) );
define( 'WCU_VERSION', '1.0.2' );

require_once WCU_DIR . 'includes/Core.php';

register_activation_hook( __FILE__, array( 'WCU\\Core', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WCU\\Core', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WCU\\Core', 'init' ) );
