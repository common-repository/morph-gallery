<?php
namespace MorphGallery;

/**
 * Class for saving gallery data
 */
class Saver {
    
    protected $nonce_name;
    protected $nonce_action;
    protected $custom_post_type;

	/**
	 * @var \MorphGallery\TemplateList
	 */
	protected $templates;

	/**
	 * @var \MorphGallery\Grafika\EditorInterface
	 */
	protected $editor;

    protected $galleries_dir;
    protected $galleries_url;

	/**
	 * @var \MorphGallery\Fetcher
	 */
	protected $fetcher;

	/**
	 * @var string
	 */
	protected $textdomain;

    public function __construct( $nonce_name, $nonce_action, $custom_post_type, $templates, $editor, $galleries_dir, $galleries_url, $fetcher, $textdomain ){
        $this->nonce_name = $nonce_name;
		$this->nonce_action = $nonce_action;
        $this->custom_post_type = $custom_post_type;
        $this->templates = $templates;
        $this->editor = $editor;
        $this->galleries_dir = $galleries_dir;
        $this->galleries_url = $galleries_url;
        $this->fetcher = $fetcher;
        $this->textdomain = $textdomain;
    }
    
    public function run(){
        global $wp_version;
		
		// Use better hook if available
		if ( version_compare( $wp_version, '3.7', '>=' ) ) {
			add_action( "save_post_{$this->custom_post_type}", array( $this, 'save_post_hook' ) );
		} else {
			add_action( 'save_post', array( $this, 'save_post_hook' ) );
		}

        // Add hook for ajax operations if logged in
        add_action( 'wp_ajax_morph_regen_thumbs', array( $this, 'morph_regen_thumbs' ) );
    }
    

    public function save_post_hook( $gallery_id ){
        global $morph_saved_done;
        
        // Stop! We have already saved..
        if($morph_saved_done){
            return $gallery_id;
        }

	    // Assign global to local variable
        $post = $_POST;

        // Verify nonce
        if (!empty($post[$this->nonce_name])) {
            if (!wp_verify_nonce($post[$this->nonce_name], $this->nonce_action)) {
                return $gallery_id;
            }
        } else {
            return $gallery_id; // Make sure we cancel on missing nonce!
        }
        
        // Don't autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $gallery_id;
        }

        // Assign POST data with array key checks
        $items = isset($post['morph_gallery_items']) ? $post['morph_gallery_items'] : array();
        $settings = isset($post['morph_gallery_settings']) ? $post['morph_gallery_settings'] : array();
        $template = $settings['template'];
        $current_template = $this->templates->get_template($template);

        // Item settings
        $sanitized_items = array();
        if( is_array($items) ){
            $i=0;//always start from 0
            foreach($items as $item){
                $item_json = (array) json_decode(stripslashes($item['json']));

                $sanitized_items[$i]['id'] = isset($item_json['id']) ? intval($item_json['id']) : 0;
                $sanitized_items[$i]['name'] = isset($item_json['name']) ? sanitize_text_field($item_json['name']) : '';
                $sanitized_items[$i]['alt'] = isset($item_json['alt']) ? sanitize_text_field($item_json['alt']) : '';
                $sanitized_items[$i]['title'] = isset($item_json['title']) ? sanitize_text_field($item_json['title']) : '';

                // Custom template item fields
                foreach ( $current_template->get_item_fields() as $template_field_key => $template_field ) {

                    $sanitized_items[ $i ][ $template_field_key ] = $template_field['default']; // Default value

                    if(isset($item_json[ $template_field_key ])){ // JSON key present
                        $json_val = $item_json[ $template_field_key ]; // Get JSON value
                        $sanitized_items[ $i ][ $template_field_key ] = sanitize_text_field($json_val); // Default sanitizer
                        if ( isset( $template_field['sanitizer'] ) and is_callable( $template_field['sanitizer'] ) ) { // Use template sanitizer function if present
                            $sanitized_items[ $i ][ $template_field_key ] = $template_field['sanitizer']( $json_val );
                        }
                    }
                }

                $i++;
            }
        }
        $this->add_gallery_items( $gallery_id, $sanitized_items );

        // Gallery settings
        $settings['template'] = sanitize_text_field($settings['template']);
        $settings = apply_filters( 'morph_pre_add_gallery_settings', $settings ); // Filter for add-ons to work its magic
        $this->add_gallery_settings( $gallery_id, $settings);

		// Template settings
	    $template_settings = array();
        $template_post_key = 'morph_template_settings_'.$current_template->get_name();
	    if( isset($post[ $template_post_key ]) ) {
		    foreach ( $current_template->get_fields() as $name=>$field ) {
			    $template_settings[ $name ] = $post[ $template_post_key ][ $name ];
		    }
	    }
	    $template_settings = wp_parse_args($template_settings, $current_template->get_defaults() ); // Apply defaults
        // Sanitize before saving
        $sanitized_template_settings = array();
        foreach($template_settings as $key=>$value){
            $sanitized_template_settings[ $key ] = sanitize_text_field( $value );
        }
	    $this->add_template_settings( $gallery_id, $sanitized_template_settings, $current_template);

        // Process images
        $current_template->save_hook($gallery_id, $sanitized_items, $settings, $sanitized_template_settings);

        // Mark as done
        $morph_saved_done = true;

        return $gallery_id;
    }

    /**
     * @param string $post_title
     * @param array $settings
     * @param array $items
     * @param array $template_settings Format: [ 'carousel'=>[...], 'masonry'=>[...], ... ]
     *
     * @return int|\WP_Error
     */
    public function add_gallery( $post_title, $settings, $items, $template_settings ){
        
        $post_data = array(
            'post_type' => $this->custom_post_type,
            'post_title' => sanitize_text_field($post_title),
            'post_content' => '',
            'post_status' => 'publish'
        );

        if( $gallery_id = wp_insert_post( $post_data ) ){

            // Item settings
            $this->add_gallery_items( $gallery_id, $items );

            // Gallery settings
            $this->add_gallery_settings( $gallery_id, $settings );

            // Save all template settings not just the current template. So we loop.
            foreach($this->templates as $template) {

                // Apply defaults for missing data
                $template_settings[ $template->get_name() ] = wp_parse_args($template_settings[ $template->get_name() ], $template->get_defaults() );

                // Save settings to database
                $this->add_template_settings( $gallery_id, $template_settings[ $template->get_name() ], $template );

                // Process images
                $template->save_hook( $gallery_id, $items, $settings, $template_settings[ $template->get_name() ] );
            }

        }

	    return $gallery_id;
    }

	public function add_gallery_items( $gallery_id, $items ){
        update_post_meta( $gallery_id, '_morph_gallery_items', $items );
    }

    public function add_gallery_settings( $gallery_id, $settings ){
        update_post_meta( $gallery_id, '_morph_gallery_settings', $settings );
    }

    /**
     * @param $gallery_id
     * @param array $settings Template settings.
     * @param \MorphGallery\TemplateInterface $template
     */
    public function add_template_settings( $gallery_id, $settings, $template ){
		update_post_meta( $gallery_id, '_morph_template_settings_'.$template->get_name(), $settings);
    }

    public function morph_regen_thumbs(){
        $post = $_POST;
        if(false === isset($post['nonce'])){
            $result = array(
                'status' => 'error',
                'message' => __('Missing NONCE value.', $this->textdomain ),
                'code' => null,
                'data' => null
            );
            echo json_encode($result);
            die();
        }
        if ( false === wp_verify_nonce( $post['nonce'], $this->nonce_action ) ) {
            $result = array(
                'status' => 'error',
                'message' => __('Wrong NONCE value.', $this->textdomain ),
                'code' => null,
                'data' => null
            );
            echo json_encode($result);
            die();
        }

	    $gallery_id = (int) $post['gallery_id'];
	    $items = (array) $post['batch'];
	    $template_name = $post['template_name'];
	    $template = $this->templates->get_template($template_name);
		$gallery_settings = $this->fetcher->get_gallery_settings( $post['gallery_id'] );
	    $template_settings = $this->fetcher->get_template_settings( $post['gallery_id'], $template );

	    // Resize images
	    if( method_exists($template,'regen_thumbs_hook') ){
		    $template->regen_thumbs_hook($gallery_id, $items, $gallery_settings, $template_settings);
	    }


        echo json_encode($post['batch']);
        die();
    }
}

