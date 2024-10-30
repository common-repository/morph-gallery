<?php
namespace MorphGallery;

/**
 * Class for holding gallery data
 */
class Gallery {

    /**
     * @var int Custom post ID of gallery.
     */
    protected $id;

    /**
     * @var string Custom post slug.
     */
    protected $slug;

    /**
     * @var string Custom post title.
     */
    protected $name;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var TemplateInterface
     */
    protected $template;

    /**
     * Gallery constructor.
     *
     * @param int $id
     * @param string $slug
     * @param string $name
     * @param array $settings
     * @param array $items
     * @param TemplateInterface $template
     */
    public function __construct( $id, $slug, $name, $settings, $items, $template ) {
        $this->id       = $id;
        $this->slug     = $slug;
        $this->name     = $name;
        $this->settings = $settings;
        $this->items    = $items;
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function get_slug() {
        return $this->slug;
    }

    /**
     * @return mixed
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function get_settings() {
        return $this->settings;
    }

    /**
     * @return mixed
     */
    public function get_items() {
        return $this->items;
    }

    /**
     * @return TemplateInterface
     */
    public function get_template() {
        return $this->template;
    }

}