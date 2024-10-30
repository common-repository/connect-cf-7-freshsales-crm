<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that integrate form.
 * $cf7 variable return current form data.
 */
if ( ! function_exists( 'cf7_fs_go_integration' ) ) {
    add_action( 'wpcf7_before_send_mail', 'cf7_fs_go_integration', 20, 1 );
    function cf7_fs_go_integration( $cf7 ) {
        
        $submission = WPCF7_Submission::get_instance();
        if ( $submission ) {
          $request = $submission->get_posted_data();     
        }
        
        $form_id = 0;
        if ( isset( $request['_wpcf7'] ) ) {
            $form_id = intval( $request['_wpcf7'] );
        }
        
        if ( $form_id ) {
            $cf7_fs_go = get_post_meta( $form_id, 'cf7_fs_go', true );
            if ( $cf7_fs_go ) {
                $cf7_fs_go_fields = get_post_meta( $form_id, 'cf7_fs_go_fields', true );
                if ( $cf7_fs_go_fields != null ) {
                    $data = array();
                    foreach ( $cf7_fs_go_fields as $cf7_fs_go_field_key => $cf7_fs_go_field ) {
                        if ( isset( $cf7_fs_go_field['key'] ) && $cf7_fs_go_field['key'] ) {
                            if ( is_array( $request[$cf7_fs_go_field_key] ) ) {
                                $request[$cf7_fs_go_field_key] = implode( ';', $request[$cf7_fs_go_field_key] );
                            }
                            
                            if ( strpos( $cf7_fs_go_field['key'], 'cf_' ) !== false ) {
                                $data['custom_field'][$cf7_fs_go_field['key']] = strip_tags( $request[$cf7_fs_go_field_key] );
                            } else if ( strpos( $cf7_fs_go_field['key'], '###' ) !== false ) {
                                $cf7_fs_go_field_data = explode( '###', $cf7_fs_go_field['key'] );
                                $data[$cf7_fs_go_field_data[0]][$cf7_fs_go_field_data[1]] = strip_tags( $request[$cf7_fs_go_field_key] );
                            } else {
                                $data[$cf7_fs_go_field['key']] = strip_tags( $request[$cf7_fs_go_field_key] );
                            }
                        }
                    }
                    
                    if ( $data != null ) {
                        $module = get_post_meta( $form_id, 'cf7_fs_go_module', true );
                        $instance_url = get_option( 'cf7_fs_go_instance_url' );
                        $api_key = get_option( 'cf7_fs_go_api_key' );
                        $freshsales = new CF7_Freshsales_REST_API( $instance_url, $api_key );
                        $freshsales->addRecord( $module, $data );
                    }
                }
            }
        }
    }
}