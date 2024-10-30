<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that creates admin menu.
 */
if ( ! function_exists( 'cf7_fs_go_main_menu' ) ) {
    add_action( 'admin_menu', 'cf7_fs_go_main_menu' );
    function cf7_fs_go_main_menu() {
        add_menu_page( 'Contact Form 7 - Freshsales CRM', 'CF7 - Freshsales', 'manage_options', 'cf7_fs_go_integration', 'cf7_fs_go_integration_callback', 'dashicons-migrate' );
        add_submenu_page( 'cf7_fs_go_integration', __( 'Contact Form 7 - Freshsales CRM: Integration' ), __( 'Integration' ), 'manage_options', 'cf7_fs_go_integration', 'cf7_fs_go_integration_callback' );
        add_submenu_page( 'cf7_fs_go_integration', __( 'Contact Form 7 - Freshsales CRM: Configuration' ), __( 'Configuration' ), 'manage_options', 'cf7_fs_go_configuration', 'cf7_fs_go_configuration_callback' );
       }
}

/*
 * This is a function for configuration.
 */
if ( ! function_exists( 'cf7_fs_go_configuration_callback' ) ) {
    function cf7_fs_go_configuration_callback() {
        
        if ( isset( $_REQUEST['submit'] ) ) {
            update_option( 'cf7_fs_go_instance_url', esc_url_raw($_REQUEST['cf7_fs_go_instance_url']) );
            update_option( 'cf7_fs_go_api_key', sanitize_text_field($_REQUEST['cf7_fs_go_api_key'] ));
            
            $freshsales = new CF7_Freshsales_REST_API( sanitize_text_field($_REQUEST['cf7_fs_go_instance_url']), sanitize_text_field($_REQUEST['cf7_fs_go_api_key']) );
            $fields = $freshsales->getModuleFields( 'contacts' );
            if ( $fields != null ) {
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e( 'Configuration successful.' ); ?></p>
                    </div>
                <?php
                $modules = unserialize( get_option( 'cf7_fs_go_modules' ) );                            
                $cf7_fs_go_modules_fields = array();
                if ( $modules != null ) {
                    foreach( $modules as $key => $value ) {
                        $cf7_fs_go_modules_fields[$key] = $freshsales->getModuleFields( $key );
                    }
                }
                update_option( 'cf7_fs_go_modules_fields', $cf7_fs_go_modules_fields );
            } else {
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php _e( 'Configuration failure.' ); ?></p>
                    </div>
                <?php
            }
        }
        
        $instance_url = get_option( 'cf7_fs_go_instance_url' );
        $api_key = get_option( 'cf7_fs_go_api_key' );
      
        ?>
        <div class="wrap">                
            <h1><?php _e( 'Freshsales CRM Configuration' ); ?></h1>
            <hr>
           
                <form method="post">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label><?php _e( 'Instance URL' ); ?> <span class="description">(required)</span></label></th>
                                <td>
                                    <input class="regular-text" type="text" name="cf7_fs_go_instance_url" value="<?php echo $instance_url; ?>" required />                                    
                                    <p class="description"><?php _e( 'Enter your Freshsales instance URL. Like https://{your domain}.freshsales.io/' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label><?php _e( 'API Key' ); ?> <span class="description">(required)</span></label></th>
                                <td>
                                    <input class="regular-text" type="text" name="cf7_fs_go_api_key" value="<?php echo $api_key; ?>" required />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p><input type='submit' class='button-primary' name="submit" value="<?php _e( 'Save' ); ?>" /></p>
                </form>
                
        </div>
        <?php
    }
}

/*
 * This is a function for integration.
 */
if ( ! function_exists( 'cf7_fs_go_integration_callback' ) ) {
    function cf7_fs_go_integration_callback() {
        
        ?>
            <div class="wrap">
                <h1><?php _e( 'Freshsales CRM Integration' ); ?></h1>
                <hr>
                <?php
                     if ( isset( $_REQUEST['id'] ) ) {
                        $id = intval($_REQUEST['id']);
                        if ( isset( $_REQUEST['submit'] ) ) {
							
							$cf7_fs_go_fields == array();
							if(isset($_REQUEST['cf7_fs_go_fields']) && !empty($_REQUEST['cf7_fs_go_fields'])){
								
								foreach($_REQUEST['cf7_fs_go_fields'] as $key=>$value){
									$cf7_fs_go_fields[sanitize_text_field($key)]['key'] = sanitize_text_field($value['key']);
									$cf7_fs_go_fields[sanitize_text_field($key)]['type'] = sanitize_text_field($value['type']);
									
								}
							}
						
                            update_post_meta( $id, 'cf7_fs_go', sanitize_text_field($_REQUEST['cf7_fs_go']));
							
                            update_post_meta( $id, 'cf7_fs_go_fields', $cf7_fs_go_fields );
                            ?>
                                <div class="notice notice-success is-dismissible">
                                    <p><?php _e( 'Integration settings saved.' ); ?></p>
                                </div>
                            <?php
                        } else if ( isset( $_REQUEST['filter'] ) ) { 
                            update_post_meta( $id, 'cf7_fs_go_module', sanitize_text_field($_REQUEST['cf7_fs_go_module']) );
                        }

                        $cf7_fs_go_module = get_post_meta( $id, 'cf7_fs_go_module', true );
                        $cf7_fs_go = get_post_meta( $id, 'cf7_fs_go', true );
                        $cf7_fs_go_fields = get_post_meta( $id, 'cf7_fs_go_fields', true );
						
					
						
						
                        ?>
                        <h2><?php _e( 'Form' ); ?>: <?php echo get_the_title( $id ); ?></h2>
                        <hr>
                        <form method="post">
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label><?php _e( 'Module' ); ?></label></th>
                                        <td>
                                            <select name="cf7_fs_go_module">
                                                <option value=""><?php _e( 'Select an module' ); ?></option>
                                                <?php
                                                    $modules = unserialize( get_option( 'cf7_fs_go_modules' ) );
                                                    foreach ( $modules as $key => $value ) {
                                                        $selected = '';
                                                        if ( $key == $cf7_fs_go_module ) {
                                                            $selected = ' selected="selected"';
                                                        }
                                                        ?>
                                                            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e( 'Filter module fields' ); ?></th>
                                        <td><button type="submit" name="filter" class='button-secondary'><?php _e( 'Filter' ); ?></button></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label><?php _e( 'Freshsales CRM Integration?' ); ?></label></th>
                                        <td>
                                            <input type="hidden" name="cf7_fs_go" value="0" />
                                            <input type="checkbox" name="cf7_fs_go" value="1"<?php echo ( $cf7_fs_go ? ' checked' : '' ); ?> />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                                $_form = get_post_meta( $id, '_form', true );
                                if ( $_form ) {
                                    preg_match_all( '#\[(.*?)\]#', $_form, $matches );
                                    $cf7_fields = array();
                                    if ( $matches != null ) {
                                        foreach ( $matches[1] as $match ) {
                                            $match_explode = explode( ' ', $match );
                                            $field_type = str_replace( '*', '', $match_explode[0] );
                                            if ( $field_type != 'submit' ) {
                                                if ( isset( $match_explode[1] ) ) {
                                                    $cf7_fields[$match_explode[1]] = array(
                                                        'key'   => $match_explode[1],
                                                        'type'  => $field_type,
                                                    );
                                                }
                                            }
                                        }

                                        if ( $cf7_fields != null ) {
                                            ?>
                                                <table class="widefat striped">
                                                    <thead>
                                                        <tr>
                                                            <th><?php _e( 'Contact Form 7 Form Field' ); ?></th>
                                                            <th><?php _e( 'Freshsales CRM Module Field' ); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th><?php _e( 'Contact Form 7 Form Field' ); ?></th>
                                                            <th><?php _e( 'Freshsales CRM Module Field' ); ?></th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                            $cf7_fs_go_modules_fields = get_option( 'cf7_fs_go_modules_fields' );
                                                            $fields = ( isset( $cf7_fs_go_modules_fields[$cf7_fs_go_module] ) ? $cf7_fs_go_modules_fields[$cf7_fs_go_module] : array() );
                                                            if ( ! is_array( $fields ) ) {
                                                                $fields = array();
                                                            }

                                                            foreach ( $cf7_fields as $cf7_field_key => $cf7_field_value ) {
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo $cf7_field_key; ?></td>
                                                                        <td>
                                                                            <select name="cf7_fs_go_fields[<?php echo $cf7_field_key; ?>][key]">
                                                                                <option value=""><?php _e( 'Select a field' ); ?></option>
                                                                                <?php
                                                                                    $type = '';
                                                                                    if ( $fields != null ) {
                                                                                        foreach ( $fields as $field_key => $field_value ) {
                                                                                            $selected = '';
                                                                                            if ( isset( $cf7_fs_go_fields[$cf7_field_key]['key'] ) && $cf7_fs_go_fields[$cf7_field_key]['key'] == $field_key ) {
                                                                                                $selected = ' selected="selected"';
                                                                                                $type = $field_value['type'];
                                                                                            }
                                                                                            ?><option value="<?php echo $field_key; ?>"<?php echo $selected; ?>><?php echo $field_value['label']; ?> (<?php _e( 'Data Type:' ); ?> <?php echo $field_value['type']; echo ( $field_value['required'] ? __( ' and Field: required' ) : '' ); ?>)</option><?php
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                            <input type="hidden" name="cf7_fs_go_fields[<?php echo $cf7_field_key; ?>][type]" value="<?php echo $type; ?>" />
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                            <p>
                                <input type='submit' class='button-primary' name="submit" value="<?php _e( 'Save Changes' ); ?>" />
                            </p>
                        </form>
                        <?php
                    } else {
                        ?>
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Title' ); ?></th>
                                    <th><?php _e( 'Status' ); ?></th>       
                                    <th><?php _e( 'Action' ); ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th><?php _e( 'Title' ); ?></th>
                                    <th><?php _e( 'Status' ); ?></th>       
                                    <th><?php _e( 'Action' ); ?></th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                    $args = array(
                                        'post_type'         => 'wpcf7_contact_form',
                                        'order'             => 'ASC',
                                        'posts_per_page'    => -1,
                                    );

                                    $forms = new WP_Query( $args );
                                    if ( $forms->have_posts() ) {
                                        while ( $forms->have_posts() ) {
                                            $forms->the_post();
                                            ?>
                                                <tr>
                                                    <td><?php echo get_the_title(); ?></td>
                                                    <td><?php echo ( get_post_meta( get_the_ID(), 'cf7_fs_go', true ) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no"></span>' ); ?></td>
                                                    <td><a href="<?php echo menu_page_url( 'cf7_fs_go_integration', 0 ); ?>&id=<?php echo get_the_ID(); ?>"><span class="dashicons dashicons-edit"></span></a></td>
                                                </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                            <tr>
                                                <td colspan="3"><?php _e( 'No forms found.' ); ?></td>
                                            </tr>
                                        <?php
                                    }

                                    wp_reset_postdata();
                                ?>
                            </tbody>
                        </table>
                        <?php
                    }
                
                ?>
            </div>
        <?php
    }
}




