<?php
namespace MorphGallery;

/**
* Class for displaying admin screen
*/
class Admin {

	/**
	 * @var string
	 */
	protected $nonce_name;
	/**
	 * @var string
	 */
	protected $nonce_action;
	/**
	 * @var string
	 */
	protected $textdomain;
	/**
	 * @var string
	 */
	protected $custom_post_type;

	/**
	 * @var \MorphGallery\View
	 */
	protected $view;

	/**
	 * @var \MorphGallery\Fetcher
	 */
	protected $fetcher;

	/**
	 * @var \MorphGallery\Saver
	 */
	protected $saver;
	/**
	 * @var \MorphGallery\TemplateList
	 */
	protected $templates;
	/**
	 * @var object
	 */
	protected $editor;
	/**
	 * @var string
	 */
	protected $galleries_url;

    public function __construct( $nonce_name, $nonce_action, $textdomain, $custom_post_type, $view, $fetcher, $saver, $templates, $editor, $galleries_url ){
        $this->nonce_name = $nonce_name;
        $this->nonce_action = $nonce_action;
        $this->textdomain = $textdomain;
	    $this->custom_post_type = $custom_post_type;
	    $this->view = $view;
	    $this->fetcher = $fetcher;
	    $this->saver = $saver;
	    $this->templates = $templates;
	    $this->editor = $editor;
	    $this->galleries_url = $galleries_url;
    }
    
    public function run() {

        // Add admin menus
        add_action( 'init', array( $this, 'create_post_types' ) );
        
        // Change admin menu icon
        add_action( 'admin_init', array( $this, 'change_admin_menu_icon' ) );
        
        // Customize our custom post messages
        add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
        
        // Remove metaboxes
        add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
        
        // Add slider metaboxes
        add_action( "add_meta_boxes_{$this->custom_post_type}", array( $this, 'add_meta_boxes' ) );

        // Custom columns
        add_action( "manage_{$this->custom_post_type}_posts_custom_column", array( $this, 'custom_column' ), 10, 2);
        add_filter( "manage_edit-{$this->custom_post_type}_columns", array( $this, 'gallery_columns') );
        
        // Add hook for admin footer
        add_action( 'admin_footer', array( $this, 'js_templates') );

        // Add body css for custom styling when on our page
        add_filter( 'admin_body_class', array( $this, 'body_class' ) );

	    // Our shortcode
	    add_shortcode( 'morph_gallery', array( $this, 'shortcode') );

	    // Hook to head
	    add_action('wp_head', array($this, 'wp_head'));

	    // Screen layout
	    add_filter( "get_user_option_screen_layout_{$this->custom_post_type}", array( $this, 'screen_layout_morph' ) );

	    // Hidden metaboxes
	    add_filter( 'default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ) );


    }

	/**
	 * Change layout to 1
	 *
	 * @param $default
	 *
	 * @return int
	 */
    public function screen_layout_morph( $default ) {

        if( false === $default) {
            return 1;
        }
        return $default;
    }

	public function default_hidden_meta_boxes( $hidden ){
		$hidden[] = 'morph-gallery-codes';
		$hidden[] = 'morph-gallery-id';
		return $hidden;
	}

    /**
     * Create custom plugin post type
     */
    public function create_post_types() {
        register_post_type( $this->custom_post_type,
            array(
                'labels' => array(
                    'name' => __('Morph Gallery', 'morph'),
                    'singular_name' => __('Gallery', 'morph'),
                    'add_new' => __('Add Gallery', 'morph'),
                    'add_new_item' => __('Add New Gallery', 'morph'),
                    'edit_item' => __('Edit Gallery', 'morph'),
                    'new_item' => __('New Gallery', 'morph'),
                    'view_item' => __('View Gallery', 'morph'),
                    'search_items' => __('Search Galleries', 'morph'),
                    'not_found' => __('No galleries found', 'morph'),
                    'not_found_in_trash' => __('No galleries found in Trash', 'morph')
                ),
                'supports' => array('title'),
                'public' => false,
                'exclude_from_search' => true,
                'show_ui' => true,
                'menu_position' => 100,
                'can_export' => false // Exclude from export
            )
        );
    }

	/**
	 * Change admin icon
	 */
	public function change_admin_menu_icon() {

		global $menu, $wp_version;

		if(!isset($menu) and !is_array($menu)) {
			return false; // Abort
		}

		foreach( $menu as $key => $value ) {
			if( "edit.php?post_type={$this->custom_post_type}" == $value[2] ) {
				if ( version_compare( $wp_version, '3.9', '<' ) ) { // WP 3.8 and below
					$menu[$key][4] = str_replace('menu-icon-post', 'menu-icon-media', $menu[$key][4]);
				} else { // WP 3.9+
					$menu[$key][6] = 'dashicons-format-gallery';
				}

			}
		}
		return true;
	}


	/**
	 * @param array $messages
	 *
	 * @return array
	 */
	public function post_updated_messages( $messages ){
        $messages[ $this->custom_post_type ] = array(
            0  => '',
            1  => __( 'Gallery updated.', 'morph' ),
            2  => __( 'Custom field updated.', 'morph' ),
            3  => __( 'Custom field deleted.', 'morph' ),
            4  => __( 'Gallery updated.', 'morph' ),
            5  => __( 'Gallery updated.', 'morph' ),
            6  => __( 'Gallery published.', 'morph' ),
            7  => __( 'Gallery saved.', 'morph' ),
            8  => __( 'Gallery updated.', 'morph' ),
            9  => __( 'Gallery updated.', 'morph' ),
            10 => __( 'Gallery updated.', 'morph' )
        );

        return $messages;
    }

    /**
     * Remove Meta Boxes
     *
     * Remove built-in metaboxes from our custom post type
     */
    public function remove_meta_boxes(){
        remove_meta_box('slugdiv', $this->custom_post_type, 'normal');
        remove_meta_box('submitdiv', $this->custom_post_type, 'normal');
    }
    
    /**
     * Add Meta Boxes
     *
     * Add custom metaboxes to our custom post type
     */
    public function add_meta_boxes(){
        
        add_meta_box(
            'morph-gallery-items-box',
            __('Items', 'morph'),
            array( $this, 'render_items_box' ),
            $this->custom_post_type,
            'normal',
            'high'
        );

        add_meta_box(
            'morph-gallery-codes',
            __('Get Codes', 'morph'),
            array( $this, 'render_gallery_codes' ),
	        $this->custom_post_type,
            'normal',
            'low'
        );

        add_meta_box(
            'morph-gallery-id',
            __('Gallery ID', 'morph'),
            array( $this, 'render_gallery_id' ),
	        $this->custom_post_type,
            'normal',
            'low'
        );


        add_meta_box(
            'morph-gallery-templates',
            __('Templates', 'morph'),
            array( $this, 'render_templates_box' ),
            $this->custom_post_type,
            'normal',
            'low'
        );


        add_meta_box(
            'morph-gallery-watermark-upgrade',
            __('Watermark', 'morph'),
            array( $this, 'render_watermark_box' ),
            $this->custom_post_type,
            'normal',
            'low'
        );

	    add_meta_box(
            'morph-gallery-publish',
            __('Publish', 'morph'),
            array( $this, 'render_publish_box' ),
            $this->custom_post_type,
            'normal',
            'low'
        );
    }

    public function render_items_box($post){

		$vars = array();
	    $vars['nonce_name'] = $this->nonce_name;
	    $vars['nonce'] = wp_create_nonce( $this->nonce_action );
	    $vars['gallery_id'] = $post->ID;
	    $vars['items'] = $this->fetcher->get_gallery_items( $post->ID );
//        $vars['debug'] = print_r( $vars['items'], true );

        $settings = $this->fetcher->get_gallery_settings( $post->ID );
        $template_name = $settings['template'];
        $current_template = $this->templates->get_template( $template_name );
        $vars['current_template'] = $current_template;
        $vars['editor_fields_html'] = apply_filters('morph_editor_fields_html', $current_template->get_editor_fields_html(), $current_template );
        $vars['custom_fields_group_html'] = apply_filters('morph_custom_fields_group_html', '', $current_template );

//	    $total = count($vars['items']);
//	    $current_page = isset($_GET['current-page']) ? (int) $_GET['current-page'] : 1;
//	    $per_page = 1000;
//
//	    $paginator = new MorphGallery_Paginator($total, $current_page, $per_page);
//	    $vars['items'] = $paginator->get_rows( $vars['items'], $paginator->get_start_index(), $per_page );
//
//
//	    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
//	    $host = $_SERVER['HTTP_HOST'];
//	    $request_uri = $_SERVER['REQUEST_URI'];
//
//	    $current_url = "{$scheme}://{$host}{$request_uri}";
//	    $parsed_url = parse_url($current_url);
//
//	    parse_str( $parsed_url['query'], $query_array );
//
//	    if(isset($query_array['current-page'])){
//		    unset($query_array['current-page']);
//	    }
//	    $vars['paginator'] = $paginator;
//
//	    $vars['query_array'] = $query_array;
//
//	    $query_string = http_build_query($query_array);
//	    $vars['base_url'] = "{$parsed_url['scheme']}://{$parsed_url['host']}{$parsed_url['path']}?{$query_string}";


	    $item_default = array(
		    'src' => '',
		    'index' => '',
		    'id' => '',
		    'alt' => '',
		    'title' => '',
		    'name' => '',
	    );

		foreach($vars['items'] as $index=>$item){
			$vars['items'][$index] = wp_parse_args($item, $item_default);
			$vars['items'][$index]['src'] = $this->fetcher->get_item_thumb( $item['id'] );
			$vars['items'][$index]['preview'] = $this->fetcher->get_item_thumb( $item['id'], 'large' );
			$vars['items'][$index]['index'] = $index;
            $vars['items'][$index]['id'] = $item['id'];
		}

	    $this->view->render('items.php', $vars);
    }

    public function render_gallery_codes( $post ){
        
        $vars = array();
        $vars['post'] = $post;
        if(empty($post->post_name)){
            $vars['shortcode'] = '';
        } else {
            $vars['shortcode'] = '[morph_gallery id="'.$post->post_name.'"]';
        }
        
        $this->view->render('gallery-codes.php', $vars);

    }

	public function render_gallery_id( $post ){

		$vars = array();
		$vars['post_name'] = $post->post_name;

		$this->view->render('gallery-id.php', $vars);

	}

	public function render_templates_box($post){

		$settings = $this->fetcher->get_gallery_settings( $post->ID );
		$template_name = $settings['template'];
		$current_template = $this->templates->get_template( $template_name );

		$template_settings = $this->fetcher->get_template_settings( $post->ID, $current_template );

		$vars = array();
		$vars['gallery_id'] = $post->ID;
		$vars['settings'] = $settings;
		$vars['templates'] = $this->templates;
		$vars['current_template'] = $current_template;
		$vars['template_name'] = $template_name;
		$vars['template_settings'] = $template_settings;

		$this->view->render('templates.php', $vars);
	}

	public function render_watermark_box($post){
        $settings = $this->fetcher->get_gallery_settings( $post->ID );

        $vars = array();
        $vars['settings'] = $settings;
		$this->view->render('watermark.php', $vars);
	}

	public function render_publish_box($post){

		$settings = $this->fetcher->get_gallery_settings( $post->ID );
		$template_name = $settings['template'];
		$current_template = $this->templates->get_template( $template_name );
		$template_settings = $this->fetcher->get_template_settings( $post->ID, $current_template );

		$vars = array();
		$vars['gallery_id'] = $post->ID;
		$vars['settings'] = $settings;
		$vars['templates'] = $this->templates;
		$vars['current_template'] = $current_template;
		$vars['template_name'] = $template_name;
		$vars['template_settings'] = $template_settings;

		$this->view->render('publish.php', $vars);
	}

    public function js_templates() {

        if( get_post_type() === $this->custom_post_type ){

	        $vars = array();
	        $vars['src'] = '#src';
	        $vars['index'] = '{index}';
            $vars['json'] = '{}';

	        $this->view->render('skeleton-item.php', $vars);

        }
    }

    public function gallery_columns() {
        $columns = array();
        $columns['title']= __('Gallery Name', 'morph');
        $columns['template']= __('Template', 'morph');
        $columns['id']= __('ID', 'morph');
        $columns['shortcode']= __('Shortcode', 'morph');
        return $columns;
    }

    public function custom_column( $column_name, $post_id ){
        if ($column_name == 'template') {
            $settings = $this->fetcher->get_gallery_settings($post_id);
            echo ucwords($settings['template']);
        }

        if ($column_name == 'id') {
            $post = get_post($post_id);
            echo $post->post_name;
        }
        if ($column_name == 'shortcode') {  
            $post = get_post($post_id);
            echo '[morph_gallery id="'.$post->post_name.'"]';
        }  
    }

	/**
	 * Add js and css for WP media manager.
	 */
	public function body_class( $classes ) {
		if( $this->custom_post_type == get_post_type()){
			$classes .= 'morph-gallery';
		}
		return $classes;
	}

	public function shortcode( $parameters ) {
		$parameters = shortcode_atts(
			array(
				'id' => 0
			),
			$parameters,
			'morph_gallery'
		);

		$slug = $parameters['id'];

        if( $gallery = $this->fetcher->get_gallery( $slug ) ) {

            $settings          = $gallery->get_settings();
            $current_template  = $this->templates->get_template( $settings['template'] );
            $template_settings = $this->fetcher->get_template_settings( $gallery->get_id(), $current_template );

            return $current_template->get_render( $gallery, $template_settings );
        }
	}

	public function wp_head(){

	}

}