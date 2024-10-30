<?php
namespace MorphGallery;
/**
 * Class \MorphGallery\PluginContainer is a service container for our app
 */
class PluginContainer implements \ArrayAccess {
	/**
	 * @var array
	 */
	protected $contents;

	/**
	 * Init
	 */
	public function __construct() {
		$this->contents = array();
	}

	public function offsetExists( $id ) {
		return isset( $this->contents[ $id ] );
	}

	public function offsetGet( $id ) {
		if ( isset($this->contents[ $id ]) and is_callable( $this->contents[ $id ] ) ) { // If callable
			return $this->contents[$id]( $this ); // Call it
		}

		return isset( $this->contents[ $id ] ) ? $this->contents[ $id ] : null; // Normal params
	}

	public function offsetUnset( $id ) {
		unset( $this->contents[ $id ] );
	}


	public function offsetSet( $id, $value ) {
		$this->contents[ $id ] = $value;
	}

	/**
	 * Run the app
	 */
	public function run() {

		// Loop on contents
		foreach ( $this->contents as $id => $content ) {
			if ( is_callable( $content ) ) { // If callable
				$mixed = $content( $this ); // Call it passing PluginContainer object

				if ( is_object( $mixed ) ) {
					$reflection = new \ReflectionClass( $mixed );
					if ( $reflection->hasMethod( 'inject' ) ) {
						$mixed->inject( $this ); // Inject our container
					}
					if ( $reflection->hasMethod( 'run' ) ) {
						$mixed->run(); // Call run method on object
					}
				}
			}
		}
	}

	protected function is_anon_func($test){
		if($test instanceof \Closure){
			return true;
		}
		return false;
	}
}