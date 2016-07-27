<?php
// create a read api read endpoint
class WPCampus_showcase_rest {

	protected static $instance = null;
	protected $plugin_screen_hook_suffix = null;
	private $prefix = null;
    

	private function __construct() {

		$plugin = WPCampus_showcase::get_instance();
		$this->version = WPCampus_showcase::VERSION;
		$this->plugin_slug = $plugin->get_plugin_slug();
        $this->prefix = $plugin->get_prefix();

        //register our end point
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	}

	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_prefix() {
		return "_" . $this->plugin_slug . "_";
	}

    //register our end points
    function register_routes(){
	
		// get a single random site	
		register_rest_route( 'wpcampus/v1', '/showcase', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_site' ),
		) );
		
		// get a specific sute
		register_rest_route( 'wpcampus/v1/', '/showcase/site/(?P<id>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_site' ),
		) );
			
		// get N random sites 
		register_rest_route( 'wpcampus/v1/', '/showcase/(?P<amount>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_sites' ),
		) );

    }

    //do this when the end point is called
	public function get_site( $request ) {
		$sites = array();

		// grab a specific site
		if( isset($request['id']) ){

			$sites[] = $this->format_site( get_post( intval( $request['id'] ) ));

		// get some random sites
		} else {

			$args = array( 
				'post_type' => 'showcase', 
				'posts_per_page' => 1, 
				'orderby' => 'rand' 
			);

			if( isset($request['amount']) ){
				$args['posts_per_page'] = intval( $request['amount'] ); 
			}

			$posts = get_posts( $args );

			foreach ($posts as $post) {
				$sites[] = $this->format_site( $post );
			}

		}

        return $sites;
	}

	public function get_sites( $request ) {
		if( !isset($request['amount']) ){
			$request['amount'] = 3; 
		}
		return $this->get_site($request);
	}

	function termmap($terms){
		$nterms = array();
		foreach($terms as $term){
			$nterms[ $term->slug ] = (object) array(
				"name" => $term->name,
				"term_id" => $term->term_id,
				"url" => get_term_link( $term->term_id )
			);
		}
		return $nterms;
	}
	
	function objtoarray($obj){
		$ar = array();
		foreach ($obj as $key => $value) {
			array_push($ar, $value);
		}
		return $ar;
	}
	
	public function format_site( $post ){
		
		$site = (object) array(
			"title" => $post->post_title,
			"url" =>  get_the_permalink( $post->ID ),
			"siteurl" => get_post_meta($post->ID, $this->get_prefix() . 'url', true),
	        "organization" => get_post_meta($post->ID, $this->get_prefix() . 'organization', true),
	        "team" => get_post_meta($post->ID, $this->get_prefix() . 'team', true),
   			"description" => wpautop( $post->post_content ),
			"thumbnail" => get_the_post_thumbnail_url( $post->ID, 'showcase' ),
			"thumbnailsrc" => get_the_post_thumbnail( $post->ID, 'showcase' ),
			"type" => $this->objtoarray( $this->termmap( wp_get_post_terms( $post->ID, 'type', array( "fields" => "all" ) ) ) ),


		);
				
        return $site; 
	}

}