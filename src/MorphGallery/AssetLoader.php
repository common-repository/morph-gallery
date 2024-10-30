<?php
namespace MorphGallery;
/**
* Class for handling styles and scripts
*/
class AssetLoader {
    
	protected $url;
	protected $version;
	protected $prefix;
	protected $custom_post_type;
	/**
	 * @var \MorphGallery\TemplateList $templates
	 */
	protected $templates;
	protected $nonce_name;
	protected $nonce_action;


	public function __construct( $url, $version, $prefix, $custom_post_type, $templates, $nonce_name, $nonce_action ){
		$this->url = $url;
		$this->version = $version;
		$this->prefix = $prefix;
		$this->custom_post_type = $custom_post_type;
		$this->templates = $templates;
		$this->nonce_name = $nonce_name;
		$this->nonce_action = $nonce_action;
    }
	
	public function run() {
		
		// Register frontend styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ), 100 );
		
		// Register admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 10);

        // Remove emojis in admin
        if(is_admin()) {
            add_action( 'init', function () {

                // all actions related to emojis
                remove_action( 'admin_print_styles', 'print_emoji_styles' );
                remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
                remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
                remove_action( 'wp_print_styles', 'print_emoji_styles' );
                remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
                remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
                remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

                // filter to remove TinyMCE emojis
                add_filter( 'tiny_mce_plugins', function ( $plugins ) {
                    if ( is_array( $plugins ) ) {
                        return array_diff( $plugins, array( 'wpemoji' ) );
                    } else {
                        return array();
                    }
                } );
            } );
        }
    }

    /**
     * Scripts and styles for admin area
     *
     * @param $page
     */
	public function register_admin_scripts($page) {

        // Limit loading to certain admin pages
        if( $this->custom_post_type == get_post_type() or
            $page === 'morph_gallery_page_morph-export' or
            $page === 'morph_gallery_page_morph-import'
        ){
			// Required media files for new media manager. Since WP 3.5+
			wp_enqueue_media();

			// Main style
			wp_enqueue_style( $this->prefix.'-admin-styles', $this->url.'css/admin.css', array(), $this->version  );
			
			// Disable autosave
			wp_dequeue_script( 'autosave' );
			
			// For sortable elements
			wp_enqueue_script('jquery-ui-sortable');
			
			// For localstorage
			wp_enqueue_script( 'store', $this->url.'js/store-json2.min.js', array('jquery'), $this->version );
			
			// Allow translation to script texts
			wp_register_script( $this->prefix.'-admin-script', $this->url.'js/admin.js', array('jquery'), $this->version  );
			wp_localize_script( $this->prefix.'-admin-script', 'morph_admin_vars',
				array(
					'title'     => __( 'Select an image', 'morph' ), // This will be used as the default title
					'title2'     => __( 'Select Images - Use Ctrl + Click or Shift + Click', 'morph' ),
					'button'    => __( 'Add to Slide', 'morph' ), // This will be used as the default button text
					'button2'    => __( 'Add Items', 'morph' ),
					'button_watermark'    => __( 'Add Watermark', 'morph' ),
					'nonce' => wp_create_nonce( $this->nonce_action )
				)
			);
			wp_enqueue_script( $this->prefix.'-admin-script');
		}
	}

	/**
	 * Scripts and styles. Must be hook to either admin_enqueue_scripts or wp_enqueue_scripts
	 *
	 * @internal param string $hook Hook name passed by WP
	 */
	public function register_frontend_scripts() {

		/*** Templates Styles ***/
		$this->enqueue_templates_css();

		/*** Templates Scripts ***/
		$this->enqueue_templates_scripts();

	}
	
	/**
	* Enqueues templates styles.
	*/
	private function enqueue_templates_css(){

        /**
         * @var TemplateInterface $template
         */
		foreach($this->templates->get_templates() as $template){

            /**
             * @var WpStyle $style
             */
			foreach($template->get_styles() as $style ) {
				wp_enqueue_style(
                    $style->get_handle(),
                    $style->get_source(),
                    $style->get_dependencies(),
                    $style->get_version(),
                    $style->get_media()
                );
			}
		}
	}
   
	/**
	* Enqueues templates scripts.
	*/
	private function enqueue_templates_scripts(){

		/**
		 * @var TemplateInterface $template
		 */
		foreach($this->templates->get_templates() as $template){

            /**
             * @var WpScript $script
             */
			foreach($template->get_scripts() as $script ) {
				wp_enqueue_script(
                    $script->get_handle(),
                    $script->get_source(),
                    $script->get_dependencies(),
                    $script->get_version(),
                    $script->get_in_footer()
                );
			}
		}
	}
}