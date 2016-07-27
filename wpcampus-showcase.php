<?php 
/*
Plugin Name: WPCampus - Showcase Gallery
Plugin URI: 
Description: 
Author: Tryon Eggleston, WPCampus
Author URI: https://github.com/tryonegg/
Version: 1.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WPCampus_showcase {
	const VERSION = '1.0';
	protected $plugin_slug = 'WPCampus_showcase';
	protected static $instance = null;
    
	private function __construct() {
        //register our custom post type on init
        add_action( 'init', array( $this, 'register_custom_post_types') );

        //add some custom image sizes for screenshots pics
        add_image_size( 'showcase', 1024 );
    }
	
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public function get_prefix() {
		return "_" . $this->plugin_slug . "_";
	}
    
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
    //do this when the plugin is activated
    public static function activate( $network_wide ) {
		
	}
	
    //do this when the plugin is deactivated
    public static function deactivate( $network_wide ) {
		
	}
    
    //define our custom post type & taxonomies
    function register_custom_post_types() {       
        $args = array(
            "labels" => array(
                "name" => "Showcase Gallery",
                "singular_name" => "Showcase Site",
                "add_new_item" => "Add a New Showcase Site",
                "edit_item" => "Edit a Site"
            ),
            "public" => true,
            "show_in_rest" => true,
            "has_archive" => true,
            "capability_type" => "post",
            "rewrite" => array( "slug" => "site", "with_front" => false ),
            "supports" => array( "title", "editor", "revisions", "thumbnail" ),
        );
        register_post_type( "showcase", $args );
        
        register_taxonomy(
            'type',
            'showcase',
            array(
                'label' => __( 'Type' ),
                'rewrite' => array( 'slug' => 'type' ),
                'hierarchical' => true,
            )
        );  
 

    }
        
}

register_activation_hook( __FILE__,  array('WPCampus_showcase','activate') );
register_deactivation_hook( __FILE__,  array('WPCampus_showcase','deactivate') );

add_action( 'plugins_loaded', array( 'WPCampus_showcase', 'get_instance' ) );

// Admin screens
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-admin.php' );
	add_action( 'plugins_loaded', array( 'WPCampus_showcase_admin', 'get_instance' ) );
}

// Rest API interface
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-rest.php' );
add_action( 'plugins_loaded', array( 'WPCampus_showcase_rest', 'get_instance' ) );
