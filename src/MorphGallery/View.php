<?php
namespace MorphGallery;

/**
* Simple class for fetching template files and attaching template variables
*/
class View {
	
	protected $view_folder; // Folder containing view files
	protected $template_vars; // Global template vars present on all views

	/**
	 * Constructor
	 *
	 * @param string $view_folder
	 * @param array $template_vars
	 */
	public function __construct( $view_folder='', $template_vars=array() ){
		$this->view_folder = $view_folder;
		$this->template_vars = $template_vars;
	}

	/**
	 * Setters
	 *
	 * @param $value
	 */
	public function set_view_folder( $value ){
		$this->view_folder = $value;
	}

	/**
	 * @param array $template_vars
	 */
	public function setTemplateVars( $template_vars ) {
		$this->template_vars = $template_vars;
	}


	/**
	* Getters
	*/
	public function get_view_folder(){
		return $this->view_folder;
	}

	/**
	 * @return array
	 */
	public function getTemplateVars() {
		return $this->template_vars;
	}


	/**
	* Include the view file and extract the passed variables
	* 
	* @param string $file File name of the template
	* @param array $vars Template variables passed to the template
	* @return void on success string "Not found $view_file" on fail
	*/
	public function render($file, $vars = array()){
		$vars = array_merge( $this->template_vars, $vars ); // Merge local with global template vars
		$view_file = $this->right_sep($this->view_folder).$file; // Add directory separator if needed
		if(@file_exists($view_file)){

			$this->sandbox($view_file, $vars); //Include the view file

		} else {
			echo '<p>Not found '.$view_file.'</p>';
		}
	}

	protected function sandbox($view_file, $vars){
		extract($vars, EXTR_SKIP); // Extract variables
		unset($vars);
		include $view_file; //Include the view file
	}

	/**
	* Get and return view_file contents as string
	*
	* @param string $file File name of the template
	* @param array $vars Template variables passed to the template
	* @return string String of template file
	*/
	public function get_render($file, $vars = array()){
		ob_start();
		$this->render($file, $vars);
		return ob_get_clean();
	}
	
	/*
	 * Add directory separator if its missing. Can be \ or / depending on OS.
	 *
	 * @param string $string
	 * @return string $string
	 */
	protected function right_sep( $string ){
		$c = substr($string, -1);
		if($c !== '/' and $c !== '\\'){
			return $string.DIRECTORY_SEPARATOR;
		}
		return $string;
	}
}
