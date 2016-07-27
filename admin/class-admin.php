<?php

class WPCampus_showcase_admin {

	protected static $instance = null;
	protected $plugin_screen_hook_suffix = null;
    protected $prefix = null;
	
    /**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'WPCampus_showcase';    
	
    /**
 	 * Settings page metabox id
 	 * @var string
 	 */    
	private $metabox_id = 'WPCampus_showcase_metabox';


	private function __construct() {

		$plugin = WPCampus_showcase::get_instance();
		$this->version = WPCampus_showcase::VERSION;
		$this->plugin_slug = $plugin->get_plugin_slug();
        $this->prefix = $plugin->get_prefix();

		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles') );
        
        // check to make sure our plugin dependencies are active
        add_action( 'init', array($this, 'dependency_detection'), 30 );

        // add in the custom meta boxes
        add_action( 'cmb2_admin_init', array($this, 'cmb2_metaboxes') );    
	}

	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

    function dependency_detection() {
        //make sure that CMB2 is active
        if ( ! defined( 'CMB2_LOADED' ) ) {
            add_action( 'admin_notices', function(){
                if ( current_user_can( 'activate_plugins' ) ) {
                    echo '<div class="error message"><p>The CMB2 plugin must be active for WPCampus Showcase Gallery plugin to function.</p></div>';
                }
            } );
        }

    }


	function enqueue_styles(){

	}

	function enqueue_scripts(){
        
	}
    
    // add the meta boxes to the custom post type
    function cmb2_metaboxes() {

        // Start with an underscore to hide fields from custom fields list
        $prefix = $this->prefix;

        /**
        * Initiate the metabox
        */

        $site = new_cmb2_box( array(
            'id'           => 'test_metaboxs',
            'title'        => 'Site Information',
            'object_types' => array( 'showcase' ), // post type
            'context'      => 'normal', //  'normal', 'advanced', or 'side'
            'priority'     => 'high',  //  'high', 'core', 'default' or 'low'
            'show_names'   => true, // Show field names on the left
        ) );  
/*
* organization
* team (be it department or outside vendor)
*/

        $site->add_field( array(
            'name' => 'URL',
            'desc' => '',
            'id'   =>  $prefix . 'url',
            'type' => 'text_url',
        ) );

        $site->add_field( array(
            'name' => 'Organization',
            'desc' => '',
            'id'   =>  $prefix . 'organization',
            'type' => 'text',
        ) );

        $site->add_field( array(
            'name' => 'Team',
            'desc' => '',
            'id'   =>  $prefix . 'team',
            'type' => 'text',
        ) );

        $site->add_field( array(
            'name' => 'Additional Screenshots',
            'desc' => '',
            'id'   =>  $prefix . 'square_image',
            'type' => 'file_list',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),            
            'preview_size' => array( 150, 150 ),
        ) );  


    }
            
}