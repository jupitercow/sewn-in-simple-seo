<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-meta
 * @since             1.0.0
 * @package           Sewn_Meta
 *
 * @wordpress-plugin
 * Plugin Name:       Sewn In Meta Fields
 * Plugin URI:        https://wordpress.org/plugins/sewn-in-meta/
 * Description:       Just a basic interface for adding custom meta boxes and fields to plugins and themes.
 * Version:           1.0.0
 * Author:            Jupitercow
 * Author URI:        http://Jupitercow.com/
 * Contributor:       Jake Snyder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sewn-meta
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$class_name = 'Sewn_Meta';
if (! class_exists($class_name) ) :

class Sewn_Meta
{
	/**
	 * The unique prefix for Sewn In.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $prefix         The string used to uniquely prefix for Sewn In.
	 */
	protected $prefix;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->prefix      = 'sewn';
		$this->plugin_name = strtolower(__CLASS__);
		$this->version     = '1.0.0';
	}

	/**
	 * Load the plugin.
	 *
	 * @since	1.0.0
	 * @return	HTML
	 */
	public function run()
	{
		add_action( 'init', array($this, 'init') );

		require_once plugin_dir_path( __FILE__ ) . 'includes/sewn-meta-boxes.php';
		new Sewn_Meta_Box( $this->get_prefix(), $this->get_plugin_name(), $this->get_version() );

		require_once plugin_dir_path( __FILE__ ) . 'includes/sewn-meta-fields.php';
		new Sewn_Meta_Field( $this->get_prefix(), $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Initialize the plugin once during run.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function init()
	{
		add_action( 'admin_enqueue_scripts',    array($this, 'admin_enqueue_scripts') );
		add_action( 'admin_enqueue_scripts',    array($this, 'admin_enqueue_styles') );
	}

	/**
	 * The prefix for Sewn In, to create a uniform system of actions and filters.
	 *
	 * @since     1.0.0
	 * @return    string    The prefix.
	 */
	public function get_prefix()
	{
		return $this->prefix;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Load text limiter script in the admin for meta fields.
	 *
	 * @since	1.0.0
	 */
	public function admin_enqueue_scripts( $hook )
	{
		if ( ! in_array($hook, array('post.php','post-new.php')) ) { return; }

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/sewn-meta.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Register the stylesheets for the admin.
	 *
	 * @since    1.0.0
	 */
	public function admin_enqueue_styles( $hook )
	{
		if ( ! in_array($hook, array('post.php','post-new.php')) ) { return; }

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/sewn-meta.css', array(), $this->version, 'all' );
	}
}

$$class_name = new $class_name;
$$class_name->run();
unset($class_name);

endif;