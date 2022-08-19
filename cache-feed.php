<?php 

global $wpa_cbf;

header( 'HTTP/1.1 200 OK' );
header( 'Content-Type: text/plain' );

$nl = "\n";

$max_items = $wpa_cbf->get_plugin_setting( 'max-items', 10 );
$crawl_amp = $wpa_cbf->get_plugin_setting( 'crawl-amp', 'off' );
$crawl_images = $wpa_cbf->get_plugin_setting( 'crawl-images', 'on' );

$lp_posts = new WP_Query(array( 
  'post_type'=>'post', 
  'posts_per_page'=>$max_items, 
  'ignore_sticky_posts' => 1, 
  'orderby' => 'modified'
));

if ( $lp_posts->have_posts() ){ 
  while ( $lp_posts->have_posts() ){ 
    $lp_posts->the_post(); 
		$post_id = get_the_ID();
    echo get_the_permalink() . $nl;

    if( $crawl_amp === 'on' ){ echo get_the_permalink() . "amp/" .$nl; };

		if( $crawl_images === 'on' && has_post_thumbnail() ){
			$feat_id = get_post_meta( $post_id, '_thumbnail_id', true );
			if( $feat_id ){
				$feat_mobile = wp_get_attachment_image_src( $feat_id, 'medium' );
				$feat_desktop = wp_get_attachment_image_src( $feat_id, 'medium_large' );
				
				echo $feat_mobile[0] .$nl;
				echo $feat_desktop[0] .$nl;
			}
		}
  }  
} 
