<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a class for Freshsales API.
 */
if ( ! class_exists( 'CF7_Freshsales_REST_API' ) ) {
    class CF7_Freshsales_REST_API {
        
        var $url;
        var $key;
        
        function __construct( $url, $key ) {
            
            $this->url = rtrim( $url, '/' );
            $this->key = $key;  
        }
        
        function getModuleFields( $module ) {
           
            $url = $this->url.'/api/settings/'.$module.'/fields';
			
			$headers       = array(
			'Authorization' => "Token token=$this->key",
			'Content-Type'  => 'application/json',
				);
			$response      = wp_remote_get( $url, [ 'headers' => $headers ] );
			$response_code = wp_remote_retrieve_response_code( $response );
			
			if ( 200 != $response_code ) {
				
			
                $log = "Error: ".json_decode($response['body'])."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";

                file_put_contents( cf7_fs_go_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
				
            
				
			} else {
					$response = json_decode($response['body'] );
					$fields = array();
					if ( isset( $response->fields ) && $response->fields != null ) {
						foreach( $response->fields as $field ) {
							if ( $field->base_model == 'LeadCompany' ) {
								$field->name = 'company###'.$field->name;
							} else if ( $field->base_model == 'LeadDeal' ) {
								$field->name = 'deal###'.$field->name;
							}
							
							$fields[$field->name] = array(
								'label'     => $field->label,
								'type'      => $field->type,  
								'required'  => 0,
							);
							
							if ( $field->required ) {
								$fields[$field->name]['required'] = 1;
							}
						}
					}
			
			}
			
			           
            return $fields;
        }
        
        function addRecord( $module, $data ) {
            
		
            if ( $module == 'leads' ) {
                $data = array(
                    'lead'  => $data,
                );
            } else {
                $data = array(
                    'contact'   => $data,
                );
            }
            
            $data = json_encode( $data );
			$url = $this->url.'/api/'.$module;
					
			$headers       = array(
			'Authorization' => "Token token=$this->key",
			'Content-Type'  => 'application/json1',
			);
			
			
			$response = wp_remote_post(
			$url,
			array(
				'method'  => 'POST',
				'headers' => $headers,
				'body'    => $data,
			)
			);
					
			
			$response_code = wp_remote_retrieve_response_code( $response );
			
			if ( 200 == $response_code ) {
			 $response = json_decode($response['body'] );
			} else {
				$log = "Error: ".json_decode(  $response['body'] )."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";
                
                file_put_contents( cf7_fs_go_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
				
			}
			
			return $response;
        }
    }
}