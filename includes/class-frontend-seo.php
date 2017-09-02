<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-simple-seo
 * @since             2.1.0
 * @package           Sewn_Seo/Includes
 */

$class_name = 'Sewn_Seo_Frontend_Seo';
if ( ! class_exists($class_name) ) :

class Sewn_Seo_Frontend_Seo extends Sewn_Seo_Frontend
{
	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    2.1.0
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->settings['meta_fields'] = array(
			'description'    => '<meta name="description" content="%s">',
			'keywords'       => '<meta name="keywords" content="%s">',
			'robots'         => '<meta name="robots" content="%s">',
			'canonical'      => '<link rel="canonical" href="%s">',
		);
		$this->settings = apply_filters( "{$this->prefix}/seo/frontend/seo", $this->settings );
	}

	/**
	 * Initialize the plugin once during run.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function init()
	{
		add_action( "{$this->prefix}/seo/head",                array( $this, 'wp_head' ), 1 );

		/* Remove actions to replace */
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		remove_action( 'wp_head', 'noindex', 1 );

		/* WordPress head and standard meta */
		add_filter( 'pre_get_document_title',                  array( $this, 'meta_title' ), 15 );
		add_filter( 'wp_title',                                array( $this, 'meta_title' ), 15, 3 );
		add_filter( 'loginout',                                array( $this, 'nofollow_link' ) );
		add_filter( 'register',                                array( $this, 'nofollow_link' ) );

		add_action( "{$this->prefix}/seo/description",         array( $this, 'meta_description' ) );
		add_action( "{$this->prefix}/seo/keywords",            array( $this, 'meta_keywords' ) );
		add_action( "{$this->prefix}/seo/robots",              array( $this, 'meta_robots' ) );
		add_action( "{$this->prefix}/seo/canonical",           array( $this, 'meta_canonical' ) );
	}

	/**
	 * Better SEO: meta title.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_title( $title, $sep="|", $separator_location='' )
	{
		if ( ! $sep && false !== $sep ) { $sep = "|"; }
		if ( false === $sep ) { $sep = ''; }

		$title = "$title $sep " . get_bloginfo('blogname');
		if ( is_feed() ) {
			return $title;
		}

		$content = $this->seo_title( $sep );

		// Add the site name
		if ( $content ) {
			$title = $content;
		}

		return trim($title);
	}

	/**
	 * wp_head
	 *
	 * Output all fields
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function wp_head()
	{
		do_action( "{$this->prefix}/seo/description" );
		do_action( "{$this->prefix}/seo/keywords" );
		do_action( "{$this->prefix}/seo/robots" );
		do_action( "{$this->prefix}/seo/canonical" );
	}

	/**
	 * Adds rel="nofollow" to a link
	 *
	 * @since 2.1.0
	 * @param string $input The link element as a string.
	 * @return string
	 */
	public function nofollow_link( $input )
	{
		return str_replace( '<a ', '<a rel="nofollow" ', $input );
	}

	/**
	 * Robots HTTP Header
	 *
	 * Keeps the content from being indexed, but allows robots to follow the links.
	 *
	 * @since 2.1.0
	 * @return boolean Boolean indicating whether the noindex header was sent
	 */
	public function noindex_feed()
	{
		if ( ( is_feed() || is_robots() ) && headers_sent() === false ) {
			header( 'X-Robots-Tag: noindex, follow', true );
			return true;
		}
		return false;
	} 

	/**
	 * Better SEO: meta description.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function meta_description()
	{
		$output = $this->seo_description();

		$output = apply_filters( "{$this->prefix}/seo/field/description", $output );
		if ( $output ) {
			printf( $this->settings['meta_fields']['description'] . "\n", esc_attr($output) );
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
				if ( ! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_keywords') ) {
					$content = $meta;
				// Look for a custom meta on a posts page
				} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_keywords', true) ) {
					$content = $meta;
				}
			}
			else
			{
				if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_keywords', true) ) {
					$content = $meta;
				}
			}
	
			if ( $content ) {
				printf( $this->settings['meta_fields']['keywords'] . "\n", $content );
			}
		}
	}

	/**
	 * Better SEO: meta robots.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_robots()
	{
		global $wp_query, $post;

		$robots = array(
			'index' => 'index',
			'follow' => 'follow',
			'other' => array(),
		);

		if ( is_singular() && is_object( $post ) ) {
			if ( apply_filters( "{$this->prefix}/seo/noindex/{$post->post_type}", false ) || 'private' === $post->post_status ) {
				$robots['index'] = 'noindex';
			}
			$robots = $this->single_robots( $robots, $post->ID );
		} elseif ( is_search() || is_404() ) {
			$robots['index'] = 'noindex';
		} elseif ( is_tax() || is_tag() || is_category() ) {
			$term = $wp_query->get_queried_object();
			if ( is_object( $term ) && apply_filters( "{$this->prefix}/seo/noindex/tax-{$term->taxonomy}", false ) ) {
				$robots['index'] = 'noindex';
			}
		} elseif (
			( is_author() && apply_filters( "{$this->prefix}/seo/noindex/author", false ) ) ||
			( is_date() && apply_filters( "{$this->prefix}/seo/noindex/date", false ) )
		) {
			$robots['index'] = 'noindex';
		} elseif ( is_home() ) {
			if ( get_query_var( 'paged' ) > 1 && apply_filters( "{$this->prefix}/seo/noindex/subpages", false ) ) {
				$robots['index'] = 'noindex';
			}
			$page_for_posts = get_option( 'page_for_posts' );
			if ( $page_for_posts ) {
				$robots = $this->single_robots( $robots, $page_for_posts );
			}
			unset( $page_for_posts );
		} elseif ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) { $post_type = reset($post_type); }
			if ( apply_filters( "{$this->prefix}/seo/noindex/archive-$post_type", false ) ) {
				$robots['index'] = 'noindex';
			}

			$is_paged         = isset( $wp_query->query_vars['paged'] ) && ( $wp_query->query_vars['paged'] && $wp_query->query_vars['paged'] > 1 );
			if ( $is_paged && apply_filters( "{$this->prefix}/seo/noindex/subpages", false ) ) {
				$robots['index'] = 'noindex';
			}
		}

		// Force override to respect the WP settings.
		if ( '0' == get_option('blog_public') || isset($_GET['replytocom']) ) {
			$robots['index'] = 'noindex';
		}

		$output = $robots['index'] . ',' . $robots['follow'];
		if ( ! empty($robots['other']) ) {
			$output .= ',' . implode(',', $robots['other']);
		}
		$output = apply_filters( "{$this->prefix}/seo/field/robots", $output );
		if ( $output ) {
			printf( $this->settings['meta_fields']['robots'] . "\n", esc_attr($output) );
		}
	}

	/**
	 * Update the robots for a single post
	 *
	 * @since	2.1.0
	 * @param array $robots Robots data
	 * @param int   $post_id The single post ID to determine specific values
	 * @return array
	 */
	public function single_robots( $robots, $post_id=0 ) {
		$index    = get_post_meta($post_id, 'meta_robots_index', true);
		$follow   = get_post_meta($post_id, 'meta_robots_follow', true);
		$advanced = get_post_meta($post_id, 'meta_robots_advanced', true);

		if ( '1' === $index ) {
			$robots['index'] = 'noindex';
		}

		if ( '1' === $follow ) {
			$robots['follow'] = 'nofollow';
		}

		if ( $advanced && ( '-' !== $advanced && 'none' !== $advanced ) ) {
			foreach ( $advanced as $robot ) {
				$robots['other'][] = $robot;
			}
		} elseif ( ( ! $advanced || '-' === $advanced ) && apply_filters( "{$this->prefix}/seo/noodb", false ) ) {
			$robots['other'][] = 'noodp';
		}

		return $robots;
	}

	/**
	 * Better SEO: canonical URL field.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function meta_canonical()
	{
		$content = $this->seo_canonical();
		if ( $content ) {
			printf( $this->settings['meta_fields']['canonical'] . "\n", esc_url($content) );
		}
	}
}

endif;
