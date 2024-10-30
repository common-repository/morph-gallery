<?php
namespace MorphGallery;


use MorphGallery\FileSystem\File;

class VariantsPathResolver {

    protected $galleries_dir;
    protected $galleries_url;

    /**
     * VariantsPathResolver constructor.
     *
     * @param $galleries_dir
     * @param $galleries_url
     */
    public function __construct( $galleries_dir, $galleries_url ) {
        $this->galleries_dir = $galleries_dir;
        $this->galleries_url = $galleries_url;
    }

    public function resolve_path( $gallery_id, $template_name, $variant_name, $item_id, $file_ext ){
        $ds = DIRECTORY_SEPARATOR;
        return "{$this->galleries_dir}{$ds}gallery-{$gallery_id}{$ds}{$template_name}{$ds}{$variant_name}-{$item_id}.{$file_ext}";
    }

    public function resolve_url( $gallery_id, $template_name, $variant_name, $item_id, $file_ext ){
        return "{$this->galleries_url}/gallery-{$gallery_id}/{$template_name}/{$variant_name}-{$item_id}.{$file_ext}";
    }
}