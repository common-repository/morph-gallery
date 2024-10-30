<?php
namespace MorphGallery;

interface TemplateInterface{

	public function __construct( $view, $dir_path, $dir_url, $galleries_dir, $galleries_url );

    public function save_hook( $gallery_id, $items, $settings, $template_settings );

    /**
     * @param Gallery $gallery
     * @param $template_settings
     *
     * @return mixed
     */
    public function get_render( $gallery, $template_settings );

    public function get_item_fields();

    public function get_editor_fields_html();

    public function get_template_fields_html($template_settings);

	public function get_fields();

	public function get_defaults();

	public function get_name();

	public function get_dir_path();

	public function get_dir_url();

	public function get_styles();

	public function get_scripts();

}