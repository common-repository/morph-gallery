<?php
namespace MorphGallery;
/**
 * Class TemplateList holds an array of TemplateInterface instance. It can be iterated or use inside a loop.
 */
class TemplateList implements \Iterator{

	/**
	 * @var array
	 */
	protected $templates;

	/**
	 * \MorphGallery\TemplateList constructor.
	 *
	 * @param array $templates
	 */
	public function __construct( $templates = array() ) {
		$this->templates = $templates;
	}

	/**
	 * @param string $name
	 * @param \MorphGallery\TemplateInterface $template
	 */
	public function add_template( $name, $template ) {
		$this->templates[$name] = $template;
	}

	/**
	 * @param $name
	 * @return \MorphGallery\TemplateInterface or null
	 */
	public function get_template( $name ) {
		return isset($this->templates[$name]) ? $this->templates[$name] : null;
	}

	/**
	 * @return array
	 */
	public function get_templates() {
		return $this->templates;
	}

	/**
	 * @param array $templates
	 */
	public function set_templates( $templates ) {
		$this->templates = $templates;
	}


    /**
     *
     */
    public function rewind() {
        reset( $this->templates );
    }

    /**
     * @return bool
     */
    public function valid() {
        return isset( $this->templates[ key( $this->templates ) ] );
    }

    /**
     * @return mixed
     */
    public function current() {
        return current( $this->templates );
    }

    /**
     * @return mixed
     */
    public function key() {
        return key( $this->templates );
    }

    /**
     * @return mixed
     */
    public function next() {
        return next( $this->templates );
    }


}