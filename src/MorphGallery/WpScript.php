<?php
namespace MorphGallery;


class WpScript {

	protected $handle;
	protected $source;
	protected $dependencies;
	protected $version;
	protected $in_footer;

	/**
	 * WpScript constructor.
	 *
	 * @param $handle
	 * @param $source
	 * @param $dependencies
	 * @param $version
	 * @param $in_footer
	 */
	public function __construct( $handle, $source, $dependencies = array(), $version = false, $in_footer = false ) {
		$this->handle       = $handle;
		$this->source       = $source;
		$this->dependencies = $dependencies;
		$this->version      = $version;
		$this->in_footer    = $in_footer;
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
	public function get_in_footer() {
		return $this->in_footer;
	}

	/**
	 * @param mixed $in_footer
	 */
	public function set_in_footer( $in_footer ) {
		$this->in_footer = $in_footer;
	}


}