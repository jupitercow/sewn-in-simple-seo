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
 * Version:           2.0.5
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
if (! class_exists($class_name) ) :

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
		$this->version     = '2.0.3';
		$this->settings    = array(
			'add_xml_sitemap'   => false,
			'post_types'        => array(''),
			'meta_fields'       => array(
				'description'      => '<meta property="og:description" name="description" content="%s">',
				'keywords'         => '<meta name="keywords" content="%s">',
				'classification'   => '<meta property="og:classification" content="%s">',
				'site_name'        => '<meta property="og:site_name" name="copyright" content="%s">',
				'title'            => '<meta property="og:title" content="%s">',
				'image'            => '<meta property="og:image" content="%s">',
				'type'             => '<meta property="og:type" content="%s">',
			),
			'field_groups'      => array (
				array(
					'id'              => $this->plugin_name,
					'title'           => __( 'SEO', $this->plugin_name ),
					'fields'          => array (
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
						array(
							'label'         => __( 'Open Graph Image', $this->plugin_name ),
							'name'          => 'meta_image',
							'type'          => 'image',
							'instructions'  => __( 'Used by some social media sites when a user shares this content.', $this->plugin_name ),
						),
						array(
							'label'         => __( 'Open Graph Type', $this->plugin_name ),
							'name'          => 'meta_type',
							'type'          => 'text',
							'instructions'  => __( 'Used by some social media sites when a user shares this content.', $this->plugin_name ),
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
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
		add_action( 'init',           array($this, 'init') );
	}

	/**
	 * On plugins_loaded test if Sewn Im XML Sitemap should be combined in and load the meta box class.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function plugins_loaded()
	{
		if ( class_exists('Sewn_Xml_Sitemap') ) {
			$this->settings['add_xml_sitemap'] = true;
		}

		if ( ! class_exists('Sewn_Meta') ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/sewn-meta/sewn-meta.php';
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
		add_action( 'admin_enqueue_scripts',                array($this, 'admin_enqueue_scripts') );

		add_filter( "{$this->prefix}/seo/add_image_field",  array($this, 'manual_image'), 99 );

		$this->register_field_groups();

		add_filter( 'wp_title',                             array($this, 'wp_title'), 99, 2 );
		add_action( 'wp_head',                              array($this, 'wp_head'), 1 );
		add_action( "{$this->prefix}/seo/description",      array($this, 'meta_description') );
		add_action( "{$this->prefix}/seo/keywords",         array($this, 'meta_keywords') );
		add_action( "{$this->prefix}/seo/classification",   array($this, 'meta_classification') );
		add_action( "{$this->prefix}/seo/site_name",        array($this, 'meta_site_name') );
		add_action( "{$this->prefix}/seo/og:title",         array($this, 'meta_og_title') );
		add_action( "{$this->prefix}/seo/og:image",         array($this, 'meta_og_image') );
		add_action( "{$this->prefix}/seo/og:type",          array($this, 'meta_og_type') );
		add_action( "{$this->prefix}/seo/permalink",        array($this, 'meta_permalink') );
	}

	/**
	 * Get post types.
	 *
	 * @since	2.0.0
	 * @return	void
	 */
	public function post_types()
	{
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
		if ( ! in_array($hook, array('post.php','post-new.php')) ) { return; } # || ! in_array($GLOBALS['post_type'], $this->post_types())

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/sewn-simple-seo-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * wp_head
	 *
	 * If automate is turned on, automate the header items.
	 *
	 * @since	1.0.3
	 * @return	void
	 */
	public function wp_head()
	{
		if ( apply_filters( "{$this->prefix}/seo/automate_head", apply_filters( "{$this->plugin_name}/automate_head", true ) ) )
		{
			do_action( "{$this->prefix}/seo/description" );
			do_action( "{$this->prefix}/seo/keywords" );
			do_action( "{$this->prefix}/seo/classification" );
			do_action( "{$this->prefix}/seo/site_name" );
			do_action( "{$this->prefix}/seo/og:title" );
			do_action( "{$this->prefix}/seo/og:image" );
			do_action( "{$this->prefix}/seo/og:type" );
		}
	}

	/**
	 * Better SEO: meta title.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function wp_title( $title, $sep="|" )
	{
		if (! $sep && false !== $sep ) {
			$sep = "|";
		}

		$title = "$title $sep " . get_bloginfo('blogname');
		if ( is_feed() ) {
			return $title;
		}

		$content = '';

		global $post, $paged, $page;

		if ( is_404() )
		{
			$content = apply_filters( "{$this->prefix}/seo/404_title", "Not Found, Error 404" );
		}
		elseif ( is_home() )
		{
			$posts_page_id = get_option('page_for_posts');
			$front_page_id = get_option('page_on_front');

			// If pages are default with home being posts and a site meta exists
			if (! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_title') ) {
				$content = $meta;
			// Look for a custom meta on a posts page
			} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_title', true) ) {
				$content = $meta;
			// Look for a posts page title
			} elseif ( $posts_page_id && $meta = get_the_title($posts_page_id) ) {
				$content = "$meta $sep " . get_bloginfo('blogname');
			// Use a default that can be filtered
			} else {
				$content = apply_filters( "{$this->prefix}/seo/home_title", get_bloginfo('blogname') );
			}
		}
		else
		{
			// Look for a custom meta title and override post title
			if (! empty($GLOBALS['post']->ID) )
			{
				if ( $meta_title = get_post_meta($GLOBALS['post']->ID, 'meta_title', true) ) {
					$content = $meta_title;
				} elseif ( $meta_title = get_the_title($GLOBALS['post']->ID) ) {
					$content = "$meta_title $sep " . get_bloginfo('blogname');
				}
			}
		}

		// Add pagination
		if ( 1 < $GLOBALS['paged'] || 1 < $GLOBALS['page'] ) {
			$content .= " $sep Page " . max( $GLOBALS['paged'], $GLOBALS['page'] );
		}

		// Add the site name
		if ( $content ) {
			$title = $content;
		}

		return trim($title);
	}

	/**
	 * Better SEO: meta description.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_description()
	{
		$content = '';

		if ( is_home() )
		{
			$posts_page_id = get_option('page_for_posts');
			$front_page_id = get_option('page_on_front');

			// If pages are default with home being posts and a site meta exists
			if (! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_description') ) {
				$content = $meta;
			// Look for a custom meta on a posts page
			} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_description', true) ) {
				$content = $meta;
			// Look for a posts page content
			} elseif ( $posts_page_id && $meta = get_post_field('post_content', $posts_page_id) ) {
				$content = wp_trim_words($meta, '30', '');
			}
		}
		else
		{
			if (! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_description', true) ) {
				$content = $meta;
			} elseif (! empty($GLOBALS['post']->ID) && $meta = get_post_field('post_content', $GLOBALS['post']->ID) ) {
				$content = wp_trim_words($meta, '30', '');
			}
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['description'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: meta description.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_keywords()
	{
		$content = '';

		if ( apply_filters( "{$this->prefix}/seo/add_keywords", false ) )
		{
			if ( is_home() )
			{
				$posts_page_id = get_option('page_for_posts');
				$front_page_id = get_option('page_on_front');
	
				// If pages are default with home being posts and a site meta exists
				if (! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_keywords') ) {
					$content = $meta;
				// Look for a custom meta on a posts page
				} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_keywords', true) ) {
					$content = $meta;
				}
			}
			else
			{
				if (! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_keywords', true) ) {
					$content = $meta;
				}
			}
	
			if ( $content ) {
				printf( $this->settings['meta_fields']['keywords'] . "\n", $content );
			}
		}
	}

	/**
	 * Better SEO: meta classification.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_classification()
	{
		if ( $meta = get_option('meta_classification') ) {
			printf( $this->settings['meta_fields']['classification'] . "\n", $meta );
		}
	}

	/**
	 * Better SEO: site name.
	 *
	 * @since	1.0.1
	 * @return	void
	 */
	public function meta_site_name()
	{
		$content = '';
		if ( $meta = get_option('meta_title') ) {
			$content = $meta;
		} else {
			$content = get_option('blogname');
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['site_name'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph title.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_og_title()
	{
		$content = '';
		if (! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_title', true) ) {
			$content = $meta;
		} else {
			$content = get_the_title();
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['title'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph image.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_og_image()
	{
		$content = '';
		if ( is_home() && $meta = get_option('meta_image') ) {
			$content = $meta;
		} elseif (! empty($GLOBALS['post']->ID) && $meta = apply_filters( "{$this->prefix}/seo/add_image_field", $GLOBALS['post']->ID ) ) {
			$content = $meta;
		} elseif (! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_image', true) ) {
			$content = $meta;
		} elseif (! empty($GLOBALS['post']->ID) && $meta_array = wp_get_attachment_image_src(get_post_thumbnail_id($GLOBALS['post']->ID), 'full') ) {
			if (! empty($meta_array[0]) ) {
				$content = $meta_array[0];
			}
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['image'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph type.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_og_type()
	{
		$content = '';
		if (! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_type', true) ) {
			$content = $meta;
		} elseif ( $meta = get_option('meta_type') ) {
			$content = $meta;
		}

		if ( $content ) {
			printf( $this->settings['meta_fields']['type'] . "\n", $content );
		}
	}

	/**
	 * Better SEO: open graph type.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_permalink()
	{
		if ( is_home() )
		{
			$posts_page_id = get_option('page_for_posts');
			// Look for a permalink on a posts page
			if ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_title', true) ) {
				echo $meta;
			// Look for a posts page post permalink
			} elseif ( $posts_page_id && $meta = get_the_title($posts_page_id) ) {
				echo $meta;
			// Else home url
			} else {
				echo home_url('/');
			}
		}
		else
		{
			echo get_permalink();
		}
	}

	/**
	 * Image override returns false by default.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function manual_image( $post_id )
	{
		if ( is_numeric($post_id) ) {
			return false;
		} else {
			return $post_id;
		}
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

		// Add sitemap if it is installed and set
		if ( apply_filters( "{$this->prefix}/seo/add_sitemap", $this->settings['add_xml_sitemap'] ) && class_exists('Sewn_Xml_Sitemap') && $field = apply_filters( "{$this->prefix}/sitemap/exclude_field", '' ) ) {
			$this->settings['field_groups'][0]['fields'][] = $field;
		}

		foreach ( $this->settings['field_groups'][0]['fields'] as $key => &$field )
		{
			if ( empty($field) ) { continue; }

			// Remove keywords or open graph image field unless asked for
			if (
				('meta_keywords' == $field['name'] && ! apply_filters( "{$this->prefix}/seo/add_keywords", false )) || 
				('meta_type' == $field['name'] && ! apply_filters( "{$this->prefix}/seo/add_type", false )) || 
				('meta_image' == $field['name'] && ! apply_filters( "{$this->prefix}/seo/add_image", false )) 
			) {
				unset($this->settings['field_groups'][0]['fields'][$key]);
				continue;
			}

			// Add max length to instructions
			if (! empty($field['maxlength']) ) {
				$field['instructions'] = sprintf( $field['instructions'], $field['maxlength'] );
			}
		}

		foreach ( $this->settings['field_groups'] as $field_group ) {
			do_action( "{$this->prefix}/meta/register_field_group", $field_group );
		}
	}
}

$$class_name = new $class_name;
$$class_name->run();
unset($class_name);

endif;