<?php

if ( ! defined( 'ABSPATH' ) ) exit;

global $wpa_wpcf;

header( 'HTTP/1.1 200 OK' );
header( 'Content-Type: text/plain' );

$nl = "\n";

$max_items = $wpa_wpcf->get_plugin_setting( 'max-items', 10 );
$crawl_amp = $wpa_wpcf->get_plugin_setting( 'crawl-amp', 'off' );
$crawl_images = $wpa_wpcf->get_plugin_setting( 'crawl-images', 'on' );

if( isset($_GET['limit'] ) && is_numeric( $_GET['limit'] )){
  $max_items = intval( $_GET['limit'] );
}

$feed_query = new WP_Query(array(
  'post_type' => 'post',
  'posts_per_page' => $max_items,
  'ignore_sticky_posts' => 1,
  'orderby' => 'modified'
));

$count=0;
if ( $feed_query->have_posts() ){
  while ( $feed_query->have_posts() ){
    $feed_query->the_post();

    $post_id = get_the_ID();
    echo get_the_permalink() . $nl;
    $count++;
    if( $count>=$max_items ) break;

    if( $crawl_amp === 'on' ){
      echo get_the_permalink() . "amp/" .$nl;
      $count++;
      if( $count>=$max_items ) break;
    }

    if( $crawl_images === 'on' && has_post_thumbnail() ){
      $feat_id = get_post_meta( $post_id, '_thumbnail_id', true );
      if( $feat_id ){
        $feat_mobile = wp_get_attachment_image_src( $feat_id, 'medium' );
        $feat_desktop = wp_get_attachment_image_src( $feat_id, 'medium_large' );
        
        if( $feat_mobile ){
          echo $feat_mobile[0] .$nl;
          $count++;
          if( $count>=$max_items ) break;
        }

        if( $feat_desktop ){
          echo $feat_desktop[0] .$nl;
          $count++;
          if( $count>=$max_items ) break;
        }
      }
    }
  }
}
