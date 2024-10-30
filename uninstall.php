<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

/*
 * Deleted options when plugin uninstall.
 */
delete_option( 'cf7_fs_go_instance_url' );
delete_option( 'cf7_fs_go_api_key' );
delete_option( 'cf7_fs_go_modules' );