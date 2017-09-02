<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-simple-seo
 * @since             1.0.0
 * @package           Sewn_Seo
 *
 * @wordpress-plugin
 * Plugin Name:       Sewn In Simple SEO
 * Plugin URI:        https://wordpress.org/plugins/sewn-in-simple-seo/
 * Description:       Adds a very simple, clean interface for controlling SEO items for a website.
 * Version:           2.1.3
 * Author:            Jupitercow
 * Author URI:        http://Jupitercow.com/
 * Contributor:       Jake Snyder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sewn-seo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$class_name = 'Sewn_Seo';
if ( ! class_exists($class_name) ) :

class Sewn_Seo
{
	/**
	 * The unique prefix for Sewn In.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $prefix         The string used to uniquely prefix for Sewn In.
	 */
	protected $prefix;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $settings       The array used for settings.
	 */
	protected $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    2.0.0
	 */
	public function __construct()
	{
		$this->prefix      = 'sewn';
		$this->plugin_name = strtolower(__CLASS__);
		$this->version     = '2.1.3';
		$this->frontend    = array();
		$this->settings    = array(
			'add_xml_sitemap'  => false,
			'add_social'       => false,
			'post_types'       => array(''),
			'field_groups'     => array (
				array(
					'id'              => $this->plugin_name,
					'title'           => __( 'SEO', $this->plugin_name ),
					'fields'          => array (
						array(
							'label'         => __( 'Content', $this->plugin_name ),
							'name'          => 'content',
							'type'          => 'tab',
							'placement'     => 'top',
						),
						array(
							'label'         => __( 'Title', $this->plugin_name ),
							'name'          => 'meta_title',
							'type'          => 'text',
							'instructions'  => __( 'Title display in search engines is limited to %d chars.', $this->plugin_name ),
							'maxlength'     => 70,
						),
						array(
							'label'         => __( 'Description', $this->plugin_name ),
							'name'          => 'meta_description',
							'type'          => 'textarea',
							'instructions'  => __( 'The meta description is limited to %d chars and will show up on the search engine results page.', $this->plugin_name ),
							'maxlength'     => 156,
						),
						array(
							'label'         => __( 'Keywords', $this->plugin_name ),
							'name'          => 'meta_keywords',
							'type'          => 'text',
							'instructions'  => __( 'Use sparingly. This field can be harmful if you overload it.', $this->plugin_name ),
						),
						array (
							'label'         => __( 'Advanced', $this->plugin_name ),
							'name'          => 'advanced',
							'type'          => 'tab',
							'placement'     => 'top',
						),
						array(
							'label'         => __( 'Meta Robots Index', $this->plugin_name ),
							'name'          => 'meta_robots_index',
							'type'          => 'radio',
							'layout'        => 'horizontal',
							'instructions'  => __( '', $this->plugin_name ),
							'choices'       => array(
								'index',
								'noindex',
							),
						),
						array(
							'label'         => __( 'Meta Robots Follow', $this->plugin_name ),
							'name'          => 'meta_robots_follow',
							'type'          => 'radio',
							'layout'        => 'horizontal',
							'instructions'  => __( '', $this->plugin_name ),
							'choices'       => array(
								'follow',
								'nofollow',
							),
						),
						array(
							'label'         => __( 'Meta Robots Advanced', $this->plugin_name ),
							'name'          => 'meta_robots_advanced',
							'type'          => 'select',
							'layout'        => 'horizontal',
							'instructions'  => __( '', $this->plugin_name ),
							'choices'       => array(
								'noodp' => 'noodp',
								'noimageindex' => 'noimageindex',
								'noarchive' => 'noarchive',
								'nosnippet' => 'nosnippet',
							),
							'multiple' => 1,
							'ui' => 1,
						),
						array(
							'label'         => __( 'Canonical URL', $this->plugin_name ),
							'name'          => 'meta_canonical',
							'type'          => 'text',
							'instructions'  => __( 'Override the default permalink.', $this->plugin_name ),
						),
					),
					'post_types'      => array(),
					'menu_order'      => 0,
					'context'         => 'normal',
					'priority'        => 'low',
					'label_placement' => 'top',
				),
			),
		);
		$this->settings = apply_filters( "{$this->prefix}/seo/settings", $this->settings );
	}

	/**
	 * Load the plugin.
	 *
	 * @since	2.0.0
	 * @return	void
	 */
	public function run()
	{
		add_action( 'plugins_loaded',         array($this, 'plugins_loaded') );
		add_action( 'init',                   array($this, 'init') );
		add_action( 'wp_loaded',              array($this, 'register_field_groups') );
		add_filter( 'sewn/seo/archive_title', 'sewn_simplify_archive_title' );
	}

	/**
	 * On plugins_loaded test if Sewn Im XML Sitemap should be combined in and load the meta box class.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function plugins_loaded()
	{
		// If XML Sitemap is being used, combine it
		if ( class_exists('Sewn_Xml_Sitemap') ) {
			$this->settings['add_xml_sitemap'] = true;
		}

		// If Social plugin is being used, combine it
		if ( class_exists('Sewn_Social') ) {
			$this->settings['add_social'] = true;
		}

		// Load the Meta Box/Fields generator
		if ( ! class_exists('Sewn_Meta') ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/sewn-meta/sewn-meta.php';
		}

		// Load Frontend Base
		if ( ! class_exists('Sewn_Seo_Frontend') ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-frontend.php';
		}

		// Load Frontend Classes
		$frontend_classes = array(
			'seo'    => 'Sewn_Seo_Frontend_Seo',
			'social' => 'Sewn_Seo_Frontend_Social',
		);
		foreach ( $frontend_classes as $key => $classname ) {
			if ( ! class_exists($classname) ) {
				require_once plugin_dir_path( __FILE__ ) . "includes/class-frontend-$key.php";
				$this->frontend[$classname] = new $classname;
			}
		}
	}

	/**
	 * Initialize the plugin once during run.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function init()
	{
		add_action( 'admin_enqueue_scripts',                   array($this, 'admin_enqueue_scripts') );

		/* WordPress head */
		add_action( 'wp_head',                                 array($this, 'wp_head'), 1 );

		if ( $this->frontend ) {
			foreach( $this->frontend as $class ) {
				$class->init();
			}
		}
	}

	/**
	 * wp_head
	 *
	 * If automate is turned on (default), automate the header fields.
	 *
	 * @since	1.0.3
	 * @return	void
	 */
	public function wp_head()
	{
		if ( apply_filters( "{$this->prefix}/seo/automate_head", apply_filters( "{$this->plugin_name}/automate_head", true ) ) ) {
			global $wp_query;
			$old_wp_query = null;

			if ( ! $wp_query->is_main_query() ) {
				$old_wp_query = $wp_query;
				wp_reset_query();
			}

			do_action( "{$this->prefix}/seo/head" );

			if ( ! empty( $old_wp_query ) ) {
				$GLOBALS['wp_query'] = $old_wp_query;
				unset( $old_wp_query );
			}
			return;
		}
	}

	/**
	 * Get post types.
	 *
	 * @since	2.0.0
	 * @return	void
	 */
	public function post_types()
	{
		$this->settings['post_types'] = get_post_types( array(
			'public' => true,
		));
		unset($this->settings['post_types']['attachment']);

		return apply_filters( "{$this->prefix}/seo/post_types", apply_filters( "{$this->plugin_name}/post_types", $this->settings['post_types'] ) );
	}

	/**
	 * Load text limiter script in the admin for meta fields.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function admin_enqueue_scripts( $hook )
	{
		if ( ! in_array($hook, array('post.php','post-new.php')) || ! in_array($GLOBALS['post_type'], $this->post_types()) ) { return; } # || )

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/sewn-simple-seo-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add the meta box.
	 *
	 * @since	2.0.0
	 * @return	void
	 */
	public function register_field_groups()
	{
		// locations for this field group
		if ( $post_types = $this->post_types() ) {
			foreach ( $post_types as $post_type ) {
				$this->settings['field_groups'][0]['post_types'][] = $post_type;
			}
		}

		$fields = array();

		foreach ( $this->settings['field_groups'][0]['fields'] as $key => $field )
		{
			if ( empty($field) ) { continue; }

			// Add max length to instructions
			if ( ! empty($field['maxlength']) ) {
				$field['instructions'] = sprintf( $field['instructions'], $field['maxlength'] );
			}

			// Remove keywords or open graph image field unless asked for
			if ( 'meta_keywords' == $field['name'] ) {
				if ( apply_filters( "{$this->prefix}/seo/add_keywords", false ) ) {
					$fields[] = $field;
				}

				// Add sitemap if it is installed and set
				if ( apply_filters( "{$this->prefix}/seo/add_sitemap", $this->settings['add_xml_sitemap'] ) && $xml_field = apply_filters( "{$this->prefix}/sitemap/exclude_field", null ) ) {
					$fields[] = $xml_field;
				}

				// Add sitemap if it is installed and set
				if ( $social_fields = apply_filters( "{$this->prefix}/social/fields", array() ) ) {
					$fields = array_merge( $fields, $social_fields );
				}
			} else {
				$fields[] = $field;
			}
		}

		$this->settings['field_groups'][0]['fields'] = $fields;

		foreach ( $this->settings['field_groups'] as $field_group ) {
			do_action( "{$this->prefix}/meta/register_field_group", $field_group );
		}
	}
}

$$class_name = new $class_name;
$$class_name->run();
unset($$class_name);

function sewn_simplify_archive_title( $title )
{
	$delimiter = ': ';
	$array     = explode( $delimiter, $title );
	if ( 1 < count($array) ) {
		array_shift($array);
		return implode( $delimiter, $array );
	}
	return $title;
}

endif;
