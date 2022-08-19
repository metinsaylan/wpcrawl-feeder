<?php

/*
Plugin Name: WPCrawl - Cache Bot Feed
Plugin URI: https://wpcrawl.com/plugin
Description: Official WPCrawl plugin to create a crawl feed for WPCrawl external wordpress crawler. Read more on <a href="https://wpcrawl.com/" target="_blank" rel="noopener noreferrer">WPCrawl</a>.
Version: 1.0
Author: WPCrawl
Author URI: https://wpcrawl.com
*/


function wpa_get_random_string(){
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
  return substr( str_shuffle( $permitted_chars ), 0, 5 );
}

global $wpa_cbf;
$wpa_cbf = new WPA_Cache_Bot_Feed();
class WPA_Cache_Bot_Feed {
  
  function __construct() {

    $this->settings_key = 'wpa-cbf';
    $this->version = '1.0';
    $this->options_page = 'wpa-cbf-options';
    $this->options_title = 'WPCrawl - Crawl Feed Settings';
    $this->menu_label = 'WPCrawl Feed Settings';

    $this->options = array(
      array(
        "name" => "labels",
        "label" => __("General Settings"),
        "type" => "section"
      ),
      array( "type" => "open" ),
      array(
        'type'   => 'text',
        'id'     => 'page-url',
        'name'  => 'Page URL',
        'default' => wpa_get_random_string()
      ),
      array(
        'type'  => 'number',
        'id'    => 'max-items',
        'name'  => 'Max Items',
        'default' => 200
      ),
      array(
        'type'  => 'checkbox',
        'id'    => 'crawl-images',
        'name'  => 'Crawl featured images',
        'default' => 'on'
      ),
      array(
        'type'  => 'checkbox',
        'id'    => 'crawl-amp',
        'name'  => 'Crawl AMP versions',
        'default' => 'off'
      ),
      array( "type" => "close" ),
    );

    add_action( 'template_redirect', array(&$this, 'template_redirect'), 1 );

    add_action( 'admin_menu', array(&$this, 'admin_header') );
  }

  function template_redirect() {
    global $wpa_cbf;
    global $wp_query;

    if ( !$wp_query ) return;

    $cbf_page = $this->get_plugin_setting( 'page-url' );
    if( !$cbf_page || '' === $cbf_page ) return;

    $page_slug = $wp_query->query_vars['name'];
    $permalink_structure = get_option( 'permalink_structure' );
    if( '/%category%/%postname%/' === $permalink_structure ){
      $page_slug = $wp_query->query_vars['category_name'];
    }

    if ( !$page_slug ) return;

    if ( $page_slug !== $cbf_page ) return;

    if ( $wp_query->is_404 ) {
      $wp_query->is_404 = false;
    }

    // include custom template
    include dirname( __FILE__ ) . '/cache-feed.php';
    exit;
  }
      
  function options_page(){

    $title = $this->options_title;

    $messages = array(
      "1" => __("Settings are saved.", 'wpa-cbf' ),
      "2" => __("Settings are reset.", 'wpa-cbf' )
    );
    
    $options = $this->options;
    $current = $this->get_plugin_settings();

    include_once( "wpa-cbf-options-page.php" );

  }
  
  function enqueue_styles(){
    wp_enqueue_style( "wpa-options", plugins_url( '/options.css' , __FILE__ ) , false, null, "all");
  }
  
  function admin_header( $instance ){
    if( !wp_doing_ajax() ){

      /* If we are on options page */
      if ( @$_GET['page'] == $this->options_page ) {

        $this->enqueue_styles();

        if ( @$_REQUEST['action'] && 'save' == $_REQUEST['action'] ) {

          // Save settings
          $settings = $this->get_plugin_settings();

          // Set updated values
          foreach( $this->options as $option ){
            if( array_key_exists( 'id', $option ) ){
              if( $option['type'] == 'checkbox' && empty( $_REQUEST[ $option['id'] ] ) ) {
                $settings[ $option['id'] ] = 'off';
              } elseif( array_key_exists( $option['id'], $_REQUEST ) ) {
                $settings[ $option['id'] ] = $_REQUEST[ $option['id'] ];
              } else {
                // hmm no key here?
              }
            }
          }

          // Save the settings
          update_option( $this->settings_key, $settings );
          header("Location: admin.php?page=" . $this->options_page . "&saved=true&message=1");
          die;
        } else if( @$_REQUEST['action'] && 'reset' == $_REQUEST['action'] ) {

          // Start a new settings array
          $settings = array();
          delete_option( $this->settings_key );

          header("Location: admin.php?page=" . $this->options_page . "&reset=true&message=2");
          die;
        }

      }

      $page = add_options_page(
        $this->options_title,
        $this->menu_label,
        'manage_options',
        $this->options_page,
        array(&$this, 'options_page')
      );
    }
  }

  function get_plugin_settings(){
    $settings = get_option( $this->settings_key );

    if(FALSE === $settings){
      // Options doesn't exist, install standard settings
      return $this->install_default_settings();
    } else { // Options exist, update if necessary
      if( !empty( $settings['version'] ) ){ $ver = $settings['version']; }
      else { $ver = ''; }

      if($ver != $this->version){
        // Update settings
        return $this->update_plugin_settings( $settings );
      } else {
        // Plugin is up to date, let's return
        return $settings;
      }
    }
  }

  /* Updates a single option key */
  function update_plugin_setting( $key, $value ){
    $settings = $this->get_plugin_settings();
    $settings[$key] = $value;
    update_option( $this->settings_key, $settings );
  }

  /* Retrieves a single option */
  function get_plugin_setting( $key, $default = '' ) {
    $settings = $this->get_plugin_settings();
    if( array_key_exists($key, $settings) ){
      return $settings[$key];
    } else {
      return $default;
    }

    return FALSE;
  }

  function install_default_settings(){
    // Create settings array
    $settings = array();

    // Set default values
    foreach($this->options as $option){
      if( array_key_exists( 'id', $option ) && array_key_exists( 'default', $option ) )
        $settings[ $option['id'] ] = $option['default'];
    }

    $settings['version'] = $this->version;
    // Save the settings
    update_option( $this->settings_key, $settings );
    return $settings;
  }

  function update_plugin_settings( $current_settings ){
    //Add missing keys
    foreach($this->options as $option){
      if( array_key_exists ( 'id' , $option ) && !array_key_exists ( $option['id'] ,$current_settings ) ){
        $current_settings[ $option['id'] ] = $option['default'];
      }
    }

    update_option( $this->settings_key, $current_settings );
    return $current_settings;
  }
}
