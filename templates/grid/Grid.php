<?php

namespace MorphTemplate;

use MorphGallery\FileSystem\File;
use MorphGallery\Grafika\EditorInterface;
use MorphGallery\TemplateInterface;
use MorphGallery\VariantsPathResolver;
use MorphGallery\View;
use MorphGallery\Grafika;
use MorphGallery\WpStyle;

class Grid implements TemplateInterface {

	const NAME = 'grid';

	protected $styles;
	protected $scripts;
	/**
	 * @var View $view
	 */
	protected $view;
	protected $dir_path;
	protected $dir_url;
	protected $galleries_dir;
	protected $galleries_url;
    protected $variants;

	public function __construct( $view, $dir_path, $dir_url, $galleries_dir, $galleries_url ){
        $this->styles = array(
            new WpStyle(
                'morph-gallery-grid',
                $dir_url . '/'.'css/style.css'
            )
        );
        $this->scripts = array();
		$this->view = $view;
		$this->dir_path = $dir_path;
		$this->dir_url = $dir_url;
		$this->galleries_dir = $galleries_dir;
		$this->galleries_url = $galleries_url;

	}

	public function get_render( $gallery, $template_settings ){

        $resolver = new VariantsPathResolver( $this->galleries_dir, $this->galleries_url );
        $items = $gallery->get_items();

		foreach($items as $index=>$item) {

            // Add variants
            $image_file = new File( get_attached_file( $item['id'] ) );

            $variant_name= 'normal';
            $items[ $index ]['variants'][$variant_name] = $resolver->resolve_url( $gallery->get_id(), $this->get_name(), $variant_name, $item['id'], $image_file->getExtension() );

            $variant_name= 'full';
            $items[ $index ]['variants'][$variant_name] = $resolver->resolve_url( $gallery->get_id(), $this->get_name(), $variant_name, $item['id'], $image_file->getExtension() );

        }

		$this->view->set_view_folder( $this->dir_path );
		return $this->view->get_render(
            'views/gallery.php',
            array(
                'gallery_slug' => $gallery->get_slug(),
                'gallery_id'=>$gallery->get_id(),
                'items'=>$items
            )
        );
	}

	/**
	 * @return array
	 */
	public function get_fields(){
		return array(
            'image_normal_width' => array(
				'default' => 175
			),
            'image_normal_height' => array(
				'default' => 175
			),
            'image_normal_resize_mode' => array(
                'default' => 'exactWidth'
            ),
            'image_full_width' => array(
				'default' => 800
			),
            'image_full_height' => array(
				'default' => 600
			),
            'image_full_resize_mode' => array(
                'default' => 'fit'
            )
		);
	}

	public function get_defaults(){
		$defaults = array();
		foreach($this->get_fields() as $name=>$field){
			$defaults[ $name ] = $field['default'];
		}
		return $defaults;
	}

    public function get_item_fields(){
        return array(
            'image_normal_width' => array(
                'default' => 175
            ),
            'image_normal_height' => array(
                'default' => 175
            ),
            'image_normal_resize_mode' => array(
                'default' => 'exactWidth'
            ),
            'image_full_width' => array(
                'default' => 800
            ),
            'image_full_height' => array(
                'default' => 600
            ),
            'image_full_resize_mode' => array(
                'default' => 'fit'
            )
        );
    }

    public function get_template_fields_html($template_settings){
        $this->view->set_view_folder( $this->dir_path );
        return $this->view->get_render( 'views/template-fields.php', array('template_settings'=>$template_settings) );
    }

    public function get_editor_fields_html(){
        return '';
    }


    public function save_hook($gallery_id, $items, $settings, $template_settings ){
        foreach( $items as $item ){
            $attachment_id = $item['id'];

            $image_path = get_attached_file( $attachment_id );
            if ( @file_exists( $image_path ) ) {

                $image_file = new File( $image_path );
                $editor     = Grafika\Grafika::createEditor();
                $resolver   = new VariantsPathResolver( $this->galleries_dir, $this->galleries_url );

                // Create normal images
                $width = ('' != $item['image_normal_width']) ? $item['image_normal_width'] : $template_settings['image_normal_width'];
                $height = ('' != $item['image_normal_height']) ? $item['image_normal_height'] : $template_settings['image_normal_height'];
                $resize_mode = ('' != $item['image_normal_resize_mode']) ? $item['image_normal_resize_mode'] : $template_settings['image_normal_resize_mode'];
                $this->_create_variant(
                    'normal',
                    $gallery_id,
                    $item,
                    $settings,
                    $attachment_id,
                    $image_file,
                    $editor,
                    $resolver,
                    $width,
                    $height,
                    $resize_mode
                );

                // Full images
                $width = ('' != $item['image_full_width']) ? $item['image_full_width'] : $template_settings['image_full_width'];
                $height = ('' != $item['image_full_height']) ? $item['image_full_height'] : $template_settings['image_full_height'];
                $resize_mode = ('' != $item['image_full_resize_mode']) ? $item['image_full_resize_mode'] : $template_settings['image_full_resize_mode'];
                $this->_create_variant(
                    'full',
                    $gallery_id,
                    $item,
                    $settings,
                    $attachment_id,
                    $image_file,
                    $editor,
                    $resolver,
                    $width,
                    $height,
                    $resize_mode
                );

            }
        }
    }

    /**
     * @param $variant_name
     * @param $gallery_id
     * @param $item
     * @param $settings
     * @param $attachment_id
     * @param File $image_file
     * @param EditorInterface $editor
     * @param VariantsPathResolver $resolver
     * @param $width
     * @param $height
     * @param $mode
     */
    private function _create_variant($variant_name, $gallery_id, $item, $settings, $attachment_id, $image_file, $editor, $resolver, $width, $height, $mode){

        $editor->open( $image_file->getPath() );
        $image = $editor->getImage();

        $image = apply_filters('morph_variant_image_filter', $image, $item, $settings);

        $editor->setImage($image);

        $path = $resolver->resolve_path(
            $gallery_id,
            $this->get_name(),
            $variant_name,
            $attachment_id,
            $image_file->getExtension()
        );
        $editor->resize( $width, $height, $mode );
        $editor->save(
            $path,
            null,
            70
        );
    }

	/**
	 * @return string
	 */
	public function get_name() {
		return self::NAME;
	}

	/**
	 * @return string
	 */
	public function get_dir_path() {
		return $this->dir_path;
	}

	/**
	 * @return string
	 */
	public function get_dir_url() {
		return $this->dir_url;
	}

	/**
	 * @return array
	 */
	public function get_styles(){
		return $this->styles;
	}

	/**
	 * @return array
	 */
	public function get_scripts(){
		return $this->scripts;
	}


}