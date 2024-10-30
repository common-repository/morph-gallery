<?php
namespace MorphGallery;
/**
 * Class for fetching gallery data
 */
class Fetcher {

    protected $meta_keys_items;
    protected $meta_keys_settings;
    protected $custom_post_type;

    /**
     * @var TemplateList
     */
    protected $templates;

    public function __construct( $meta_keys_items, $meta_keys_settings, $custom_post_type, $templates ){
        $this->meta_keys_items    = $meta_keys_items;
        $this->meta_keys_settings = $meta_keys_settings;
        $this->custom_post_type   = $custom_post_type;
        $this->templates   = $templates;
    }

    /**
     * Get all galleries.
     *
     * @param array $args Optional. Arguments for WordPress get_posts().
     *
     * @return array Array of Gallery instance.
     */
    public function get_galleries( $args=array() ){
        $defaults = array(
            'post_type' => $this->custom_post_type,
            'numberposts' => -1 // Get all. Overridable
        );
        $args = wp_parse_args($args, $defaults);

        $posts = get_posts( $args ); // Use get_posts to avoid filters
        $galleries = array(); // Store it here
        if( !empty($posts) and is_array($posts) ) {
	        foreach ( $posts as $index => $post ) {
                $settings = $this->get_gallery_settings( $post->ID );
                $template = $this->templates->get_template( $settings['template'] );
		        $galleries[ $index ] = new Gallery(
                    $post->ID,
                    $post->post_name,
                    $post->post_title,
                    $this->get_gallery_settings( $post->ID ),
                    $this->get_gallery_items( $post->ID ),
                    $template
                );
	        }
        }
        return $galleries;

    }

    /**
     * Get gallery base on slug.
     *
     * @param string $slug The gallery slug. It is the WordPress custom post slug.
     *
     * @return bool|Gallery Gallery when found or false if not found.
     */
    public function get_gallery( $slug ){
        $args = array(
            'post_type' => $this->custom_post_type,
            'numberposts' => 1,
            'name'=> $slug
        );

        $posts = get_posts( $args );
        if( isset($posts[0]) ){
            $settings = $this->get_gallery_settings( $posts[0]->ID );
            $items = $this->get_gallery_items( $posts[0]->ID );
            $template = $this->templates->get_template( $settings['template'] );
            return new Gallery(
                $posts[0]->ID,
                $posts[0]->post_name,
                $posts[0]->post_title,
                $settings,
                $items,
                $template
            );
        }
        return false;
    }

    /**
    * @param int $gallery_id ID of slider post
    * @return array The array of settings
    */
    public function get_gallery_settings( $gallery_id ) {
        $meta = get_post_custom( $gallery_id );
	    $settings = array();
        if(isset($meta[$this->meta_keys_settings][0]) and !empty($meta[$this->meta_keys_settings][0])){
            $settings = maybe_unserialize($meta[$this->meta_keys_settings][0]);
        }

        return wp_parse_args($settings, $this->get_gallery_defaults() );
    }

    public function get_gallery_items( $gallery_id ){
        $meta = get_post_custom( $gallery_id );

        if(isset($meta[$this->meta_keys_items][0]) and !empty($meta[$this->meta_keys_items][0])){
            return maybe_unserialize($meta[$this->meta_keys_items][0]);
        }
	    return array();
    }


	/**
	 * @param int $gallery_id
	 * @param \MorphGallery\TemplateInterface $template
	 *
	 * @return array
	 */
	public function get_template_settings( $gallery_id, $template ){
		$meta = get_post_custom( $gallery_id );
        $settings = array();
        $key = '_morph_template_settings_'.$template->get_name();
		if( isset($meta[$key][0]) ){
            $settings = maybe_unserialize($meta[$key][0]);
		}
		return wp_parse_args($settings, $template->get_defaults() ); // Apply default values
	}

	public function get_gallery_defaults(){
        return apply_filters(
            'morph_get_gallery_defaults',
            array(
                'template' => 'masonry',
                'watermark' => 0,
                'watermark_x' => 'center',
                'watermark_y' => 'center',
                'watermark_width' => 100,
                'watermark_width_unit' => '%',
                'watermark_height' => 100,
                'watermark_height_unit' => '%',
                'watermark_mode' => 'fit',
                'watermark_type' => 'image',
                // TODO: to implement
                'watermark_opacity' => 1,
                'watermark_angle' => 0,
                'watermark_repeat' => 'none',
            )
        );
	}

    public function get_gallery_id( $slug ){
        $args = array(
            'post_type' => $this->custom_post_type,
            'numberposts' => 1,
            'name'=> $slug
        );

        $posts = get_posts( $args );
        if( isset($posts[0]) ){
            return $posts[0]->ID;
        }
        return 0;
    }

    /**
     * @return TemplateList
     */
    public function get_templates(){
        return $this->templates;
    }

    public function get_item_thumb($attachment_id, $size='medium'){
        $attachment_id = (int) $attachment_id;
        if($attachment_id > 0){
            $image_url = wp_get_attachment_image_src( $attachment_id, $size, true );
            $image_url = (is_array($image_url)) ? $image_url[0] : '';
            return $image_url;
        }
        return false;
    }
}

