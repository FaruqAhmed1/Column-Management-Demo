<?php 
/**
 * Plugin Name:       Column Demo
 * Plugin URI:        
 * Description:       Assets Mangment System 
 * Version:           1.0.0
 * Author:            Faruq Ahmed
 * Author URI:       
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       column_demo
 * Domain Path:       /languages
 *
 */


function coldemo_column_bootstrap() {
 load_plugin_textdomain ( 'column_demo',false, dirname(__FILE__).'languages' );
}
add_action( 'plugins_loaded', 'coldemo_column_bootstrap' );


function coldemo_post_column( $columns ){

    unset($columns['tags']);
    $columns['id'] = __( 'ID','column_demo' );
    return $columns;
}
add_filter( 'manage_posts_columns','coldemo_post_column' );

function coldemo_post_colum_data($columns ,$post_id) {
    echo $post_id;
}
add_action( 'manage_posts_custom_column','coldemo_post_colum_data',10,2 );




