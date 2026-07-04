<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;
delete_option( 'wcu_settings' );
delete_option( 'wcu_job' );
