<?php
namespace MorphGallery;


class WpStyle {

	protected $handle;
	protected $source;
	protected $dependencies;
	protected $version;
	protected $media;

	/**
	 * WpScript constructor.
	 *
	 * @param $handle
	 * @param $source
	 * @param $dependencies
	 * @param $version
	 * @param $media
	 */
	public function __construct( $handle, $source, $dependencies = array(), $version = false, $media = 'all' ) {
		$this->handle       = $handle;
		$this->source       = $source;
		$this->dependencies = $dependencies;
		$this->version      = $version;
		$this->media    = $media;
	}

	/**
	 * @return mixed
	 */
	public function get_handle() {
		return $this->handle;
	}

	/**
	 * @param mixed $handle
	 */
	public function set_handle( $handle ) {
		$this->handle = $handle;
	}

	/**
	 * @return mixed
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * @param mixed $source
	 */
	public function set_source( $source ) {
		$this->source = $source;
	}

	/**
	 * @return mixed
	 */
	public function get_dependencies() {
		return $this->dependencies;
	}

	/**
	 * @param mixed $dependencies
	 */
	public function set_dependencies( $dependencies ) {
		$this->dependencies = $dependencies;
	}

	/**
	 * @return mixed
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param mixed $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

	/**
	 * @return mixed
	 */
	public function get_media() {
		return $this->media;
	}

	/**
	 * @param mixed $media
	 */
	public function set_media( $media ) {
		$this->media = $media;
	}


}