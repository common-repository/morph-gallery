<?php
/*
Plugin Name: Morph Gallery
Plugin URI: http://www.codefleet.net/morph-gallery/
Description: Create and manage galleries easily.
Version: 2.0.0
Author: Nico Amarilla
Author URI: http://www.codefleet.net/
License: GPLv2

The plugin uses a plugin container approach, autoloading, and service definitions.
*/

if (version_compare(PHP_VERSION, '5.3', '<')) {
    wp_die('Morph Gallery requires PHP version 5.3.x and above. PHP version on this server is '.PHP_VERSION);
}

require_once __DIR__.'/src/autoloader.php';

use MorphGallery\AlertSystem\Alerts;
use MorphGallery\AlertSystem\WpTransientStorageAdapter;
use MorphGallery\FileSystem\FileSystemHelper;
use MorphGallery\Logger;
use MorphGallery\PluginContainer;
use MorphGallery\View;
use MorphGallery\Admin;
use MorphGallery\AssetLoader;
use MorphGallery\Saver;
use MorphGallery\Fetcher;
use MorphGallery\Grafika;
use MorphGallery\Paginator;
use MorphGallery\TemplateList;

$morph_plugin_instance = null;
$morph_saved_done      = false;

// Hook the plugin
add_action( 'plugins_loaded', function () {
	global $morph_plugin_instance;

	$plugin = new PluginContainer();

	//	Configs
	$plugin['debug']              = false;
	$plugin['prefix']             = 'morph';
	$plugin['textdomain']         = 'morph';
	$plugin['nonce_name']         = 'morph_nonce_name';
	$plugin['nonce_action']       = 'morph_nonce_action';
	$plugin['custom_post_type']   = 'morph_gallery';
	$plugin['meta_keys_items']    = '_morph_gallery_items'; // WP custom post meta
	$plugin['meta_keys_settings'] = '_morph_gallery_settings'; // WP custom post meta
	$plugin['form_keys_items']    = 'morph_gallery_items';
	$plugin['form_keys_settings'] = 'morph_gallery_settings';

	// Services
	$plugin['path']                     = function( $plugin ){
		return realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	};

	$plugin['url']                      = function ( $plugin ) {
		return plugin_dir_url( __FILE__ );
	};

	$plugin['wp_upload_location']       = function ( $plugin ) {
		$wp_locations = wp_upload_dir();
		return $wp_locations;
	};

	$plugin['wp_content_dir']           = function ( $plugin ) {
		$wp_upload_dir = $plugin['wp_upload_location'];
		return dirname( $wp_upload_dir['basedir'] );
	};

	$plugin['wp_content_url']           = function ( $plugin ) {
		$wp_upload_dir = $plugin['wp_upload_location'];
		return dirname( $wp_upload_dir['baseurl'] );
	};

	$plugin['galleries_dir']            = function ( $plugin ) {
		return $plugin['wp_content_dir'].'/morph-galleries';
	};

	$plugin['galleries_url']            = function ( $plugin ) {
		return $plugin['wp_content_url'].'/morph-galleries';
	};

	$plugin['view_folder']              = function ( $plugin ) {
		return $plugin['path'] . 'views';
	};

	$plugin['version']                  = function ( $plugin ) {

		$default_headers = array(
				'name'       => 'Plugin Name',
				'plugin_uri' => 'Plugin URI',
				'version'    => 'Version'
		);
		$plugin_data = get_file_data( __FILE__, $default_headers, 'plugin' ); // WP Func

		return $plugin_data['version'];
	};

	$plugin['slug']                     = function ( $plugin ) {

		return basename( __DIR__ ) . '/' . basename( __FILE__ );

	};

	$plugin['view'] = function ( $plugin ) {

		$template_vars = array(
		    'textdomain' => $plugin['textdomain']
		);
		return new View( $plugin['view_folder'], $template_vars );

	};

	$plugin['admin']                    = function ( $plugin ) {

		return new Admin(
				$plugin['nonce_name'],
				$plugin['nonce_action'],
				$plugin['textdomain'],
				$plugin['custom_post_type'],
				$plugin['view'],
				$plugin['fetcher'],
				$plugin['saver'],
				$plugin['templates'],
				$plugin['editor'],
				$plugin['galleries_url']
		);

	};

	$plugin['asset_loader']             = function ( $plugin ) {

		return new AssetLoader( $plugin['url'], $plugin['version'], $plugin['prefix'], $plugin['custom_post_type'], $plugin['templates'], $plugin['nonce_name'], $plugin['nonce_action'] );

	};

	$plugin['saver']                    = function ( $plugin ) {

		return new Saver( $plugin['nonce_name'], $plugin['nonce_action'], $plugin['custom_post_type'], $plugin['templates'], $plugin['editor'], $plugin['galleries_dir'], $plugin['galleries_url'], $plugin['fetcher'], $plugin['textdomain'] );

	};

	$plugin['fetcher']                  = function ( $plugin ) {

		return new Fetcher( $plugin['meta_keys_items'], $plugin['meta_keys_settings'], $plugin['custom_post_type'], $plugin['templates'] );

	};

	$plugin['editor']                   = function ( $plugin ) {

		return Grafika\Grafika::createEditor();

	};

	$plugin['paginator']                = function ( $plugin ) {

		return new Paginator();

	};

    $plugin['alert']                = function ( $plugin ) {

        return new Alerts( new WpTransientStorageAdapter('morph') );

    };

    $plugin['fs']                = function ( $plugin ) {

        return new FileSystemHelper();

    };

    $plugin['logger']                = function ( $plugin ) {

        return new Logger();

    };

	$plugin['templates']                = function ( $plugin ) {

		$locations = array(
				array(
						'path' => $plugin['path'].'templates'.DIRECTORY_SEPARATOR,
						'url' => $plugin['url'].'templates/',
						'name' => 'core'
				)
		);

		$templates = new TemplateList();

		foreach($locations as $location){ // Loop on locations

			if( is_dir( $location['path'] ) ) { // Is a directory?

				if( $items = scandir($location['path'])){ // Loop on directory contents
					foreach( $items as $name ){
						if( $name!='.' and $name != '..' and is_dir( $location['path'].$name ) ){ // Is a directory?

							$class_name = ucwords( $name );
							$class_file_name = $class_name . '.php';
							$class_path = $location['path'] . $name . DIRECTORY_SEPARATOR . $class_file_name;

							if ( @file_exists( $class_path ) ) {
								require_once $class_path;
								$class_name = 'MorphTemplate\\'.$class_name;
								$templates->add_template(
										$name,
										new $class_name(
												$plugin['view'],
												$location['path'] . $name,
												$location['url'] . $name,
												$plugin['galleries_dir'],
												$plugin['galleries_url']
										)
								);
							}
						}
					}
				}
			}
		}

		return $templates;
	};

	load_plugin_textdomain( $plugin['textdomain'], false, basename(dirname(__FILE__)).'/languages' );

    $plugin = apply_filters( 'morph_plugin_container', $plugin );

	$plugin->run();

    do_action( 'morph_after_run', $plugin );

	$morph_plugin_instance = $plugin;
});

