<?php
/*
Plugin Name: Connect CF 7 - Freshsales CRM
Description: This plugin can integrate Contacts and Leads between your WordPress Contact Form 7 and Freshsales CRM. Easily add automatically Contacts and Leads into Freshsales CRM when people submit a Contact Form 7 form on your site.
Version:     1.1.1
Author:      Rakesh Rathore
Author URI:  https://go4logics.com/
License:     GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a constant variable for plugin path.
 */
define( 'cf7_fs_go_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/*
 * This is a file for includes core functionality.
 */
include_once cf7_fs_go_PLUGIN_PATH . 'includes/includes.php';

/*
 * This is a function that run when plugin activation.
 */
if ( ! function_exists( 'cf7_fs_go_register_activation_hook' ) ) {
    register_activation_hook( __FILE__, 'cf7_fs_go_register_activation_hook' );
    function cf7_fs_go_register_activation_hook() {
        
        update_option( 'cf7_fs_go_modules', 'a:2:{s:8:"contacts";s:8:"Contacts";s:5:"leads";s:5:"Leads";}' );
    }
}