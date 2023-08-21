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
    $columns['thumbnail'] = __( 'Thumbnail','column_demo' );
    $columns['wordcount'] = __( 'Wordcount','column_demo' );
    return $columns;
}

add_filter( 'manage_posts_columns','coldemo_post_column' );
add_filter( 'manage_pages_columns','coldemo_post_column' );

function coldemo_post_colum_data($column ,$post_id) {
    if( 'id'== $column ){
        echo $post_id;
    }elseif( 'thumbnail' == $column ){
        echo get_the_post_thumbnail( $post_id, array( '100','100' ) );
    } elseif('wordcount'== $column) {
        // $_post = get_post( $post_id );
        // $content = $_post->post_content;
        // $wordn = str_word_count( strip_tags( $content ) );
        $wordn = get_post_meta( $post_id,'wordcount',true );
        echo $wordn;
    }  
}
add_action( 'manage_posts_custom_column','coldemo_post_colum_data',10,2 );
add_action( 'manage_pages_custom_column','coldemo_post_colum_data',10,2 );

function coldemo_shortable_column( $columns ){
    $columns['wordcount'] = 'wordn';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'coldemo_shortable_column' );

function coldemo_set_metavalue(){
    $_post = get_posts(array(
        'post_type' => 'post',
        'posts_per_page'=> -1, 
        'post_status'    => 'any',

    ));
    foreach( $_post as $sinlepost ){
        $content = $sinlepost->post_content;
        $wordn = str_word_count( strip_tags( $content ) );
        update_post_meta( $sinlepost->ID,'wordcount',$wordn );
    }
}
add_action( 'init','coldemo_set_metavalue' );


function coldemo_sort_columns( $query ){
    if( !is_admin( ) ){
        return;
    }
    $orderby = $query->get( 'orderby' );
    if( 'wordn'== $orderby ){
        $query->set( 'meta_key','wordcount' );
        $query->set( 'orderby','meta_value_num' );
    }
}

add_action( 'pre_get_posts','coldemo_sort_columns' );

function coldemo_update_sort_columns( $post_id ){
    $_post = get_post( $post_id );
    $content = $_post->post_content;
    $wordn = str_word_count( strip_tags( content) );
    update_post_meta( $_post->ID,'wordcount',$wordn );
}
add_action( 'save_post','coldemo_update_sort_columns' );


function coldemo_filter_data(){
    if( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post'  ){
        return;
    }
    $flter_val = isset( $_GET['filter_post'] ) ? $_GET['filter_post'] : '';
    $values = array(
        
        '0' => __('Select Post','column_demo'),
        '1' => __('Some Post','column_demo'),
        '2' => __('Other Post','column_demo'),
    );
    ?>
    <select name="filter_post">
        <?php 
        foreach( $values as $key=>$value ){
            
        $selected = $flter_val == $key ? "selected": '';
           
            printf("<option value='%s' %s>%s</option>",$key,$selected,$value);
        }  
        ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts','coldemo_filter_data' );

function coldemo_filter_data_active( $wpquery ){
    if( !is_admin( ) ){
        return;
    }

    $flter_val = isset( $_GET['filter_post'] ) ? $_GET['filter_post'] : '';
    if( $flter_val =='1' ){
        $wpquery->set('post__in',array( 59,50,37 ));
    }elseif( $flter_val =='2' ){
        $wpquery->set( 'post__in',array( 27,32,124 ) );
    }
}
add_action( 'pre_get_posts','coldemo_filter_data_active' );


//Thumbnal Filter

function coldemo_thumbnail_filter_data(){
    if( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post'  ){
        return;
    }
    $flter_val = isset( $_GET['thumbnail_filter'] ) ? $_GET['thumbnail_filter'] : '';
    $values = array(
        
        '0' => __('Thumbnail Status','column_demo'),
        '1' => __('Has Thumbnail','column_demo'),
        '2' => __('No Thumbnail','column_demo'),
    );
    ?>
    <select name="thumbnail_filter">
        <?php 
        foreach( $values as $key=>$value ){
            $selected = $flter_val == $key ? "selected": '';
            printf("<option value='%s' %s>%s</option>",$key,$selected,$value);
        }  
        ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts','coldemo_thumbnail_filter_data' );

function coldemo_thumbnail_filter_data_active( $wpquery ){
    if( !is_admin( ) ){
        return;
    }

    $flter_val = isset( $_GET['thumbnail_filter'] ) ? $_GET['thumbnail_filter'] : '';
    if( $flter_val =='1' ){
        $wpquery->set('meta_query',array(
            array(
                'key'=>'_thumbnail_id',
                'compare' => 'EXISTS',
            )
         ));
    } elseif( $flter_val =='2' ){
        $wpquery->set('meta_query',array(
            array(
                'key'=>'_thumbnail_id',
                'compare' => 'NOT EXISTS',
            )
         ));
    }
}
add_action( 'pre_get_posts','coldemo_thumbnail_filter_data_active' );

//Post Word Filter 


function coldemo_word_filter_data(){
    if( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post'  ){
        return;
    }
    $flter_val = isset( $_GET['word_filter'] ) ? $_GET['word_filter'] : '';
    $values = array(
        
        '0' => __('Word Count','column_demo'),
        '1' => __('Above 50','column_demo'),
        '2' => __('20 To 50','column_demo'),
        '3' => __('Below 20','column_demo'),
    );
    ?>
    <select name="word_filter">
        <?php 
        foreach( $values as $key=>$value ){
            $selected = $flter_val == $key ? "selected": '';
            printf("<option value='%s' %s>%s</option>",$key,$selected,$value);
        }  
        ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts','coldemo_word_filter_data' );

function coldemo_word_filter_data_active( $wpquery ){
    if( !is_admin( ) ){
        return;
    }

    $flter_val = isset( $_GET['word_filter'] ) ? $_GET['word_filter'] : '';
    if(  '1' == $flter_val ){
        $wpquery->set( 'meta_query', array(
			array(
				'key'     => 'wordn',
				'value'   => 50,
				'compare' => '>=',
				'type'    => 'NUMERIC'
			)
		) );
    } 
    else if( $flter_val =='2' ){
        $wpquery->set( 'meta_query', array(
			array(
				'key'     => 'wordn',
				'value'   => array(20,50),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC'
			)
		) );
    }else if( $flter_val  == '3'){
        $wpquery->set( 'meta_query', array(
			array(
				'key'     => 'wordn',
				'value'   => 20,
				'compare' => '<=',
				'type'    => 'NUMERIC'
			)
		) );

    }
}
add_action( 'pre_get_posts','coldemo_word_filter_data_active' );
