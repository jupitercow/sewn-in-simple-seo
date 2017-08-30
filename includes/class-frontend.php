<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-simple-seo
 * @since             2.1.0
 * @package           Sewn_Seo/Includes
 */

$class_name = 'Sewn_Seo_Frontend';
if ( ! class_exists($class_name) ) :

class Sewn_Seo_Frontend
{
	/**
	 * The unique prefix for Sewn In.
	 *
	 * @since    2.1.0
	 * @access   protected
	 * @var      string    $prefix         The string used to uniquely prefix for Sewn In.
	 */
	protected $prefix;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.1.0
	 * @access   protected
	 * @var      string    $settings       The array used for settings.
	 */
	protected $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    2.1.0
	 * @param array $post_types Array of supported post types
	 * @param array $defaults Array of default settings, eg: Title and Description
	 * @return	void
	 */
	public function __construct()
	{
		$this->prefix      = 'sewn';
		$this->settings    = array(
			'post_types'       => array(''),
		);
	}

	/**
	 * Better SEO: meta title.
	 *
	 * @since	2.0.7
	 * 
	 * @param string $sep The string separater used the meta title
	 * @return	void
	 */
	public function seo_title( $sep="|" )
	{
		if ( ',' != $sep ) { $sep = " $sep"; }
		$content = '';

		global $post, $paged, $page;

		if ( is_404() ) {
			$content = apply_filters( "{$this->prefix}/seo/404_title", "Not Found, Error 404" );
		} elseif ( is_author() ) {
			$author = get_userdata( get_query_var('author') );
			$content = apply_filters( "{$this->prefix}/seo/author_title", $author->display_name . "$sep " . get_bloginfo('blogname') );
		} elseif ( is_archive() ) {
			$content = apply_filters( "{$this->prefix}/seo/archive_title", get_the_archive_title() . "$sep " . get_bloginfo('blogname') );
		} elseif ( is_home() ) {
			$posts_page_id = get_option('page_for_posts');
			$front_page_id = get_option('page_on_front');

			// If pages are default with home being posts and a site meta exists
			if ( ! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_title') ) {
				$content = $meta;
			// Look for a custom meta on a posts page
			} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_title', true) ) {
				$content = $meta;
			// Look for a posts page title
			} elseif ( $posts_page_id && $meta = get_the_title($posts_page_id) ) {
				$content = "$meta$sep " . get_bloginfo('blogname');
			// Use a default that can be filtered
			} else {
				$content = apply_filters( "{$this->prefix}/seo/home_title", get_bloginfo('blogname') );
			}
		} else {
			// Look for a custom meta title and override post title
			if ( ! empty($GLOBALS['post']->ID) ) {
				if ( $meta_title = get_post_meta($GLOBALS['post']->ID, 'meta_title', true) ) {
					$content = $meta_title;
				} elseif ( $meta_title = get_the_title($GLOBALS['post']->ID) ) {
					$content = "$meta_title$sep " . get_bloginfo('blogname');
				}
			}
		}

		// Add pagination
		if ( $content && (1 < $GLOBALS['paged'] || 1 < $GLOBALS['page']) ) {
			$content .= "$sep Page " . max( $GLOBALS['paged'], $GLOBALS['page'] );
		}

		return $content;
	}

	/**
	 * Better SEO: meta description.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function seo_description()
	{
		$content = '';

		if ( is_archive() )
		{
			$content = apply_filters( "{$this->prefix}/seo/archive_description", strip_tags( str_replace(array("\r","\n"), '', term_description()) ) );
		}
		elseif ( is_home() )
		{
			$posts_page_id = get_option('page_for_posts');
			$front_page_id = get_option('page_on_front');

			// If pages are default with home being posts and a site meta exists
			if ( ! $posts_page_id && ! $front_page_id && $meta = get_option('options_meta_description') ) {
				$content = $meta;
			// Look for a custom meta on a posts page
			} elseif ( $posts_page_id && $meta = get_post_meta($posts_page_id, 'meta_description', true) ) {
				$content = $meta;
			// Look for a posts page content
			} elseif ( $posts_page_id && $meta = get_post_field('post_content', $posts_page_id) ) {
				$content = ( apply_filters("{$this->prefix}/seo/default_description", false) ) ? wp_trim_words($meta, '30', '') : null;
			}
		}
		else
		{
			if ( ! empty($GLOBALS['post']->ID) && $meta = get_post_meta($GLOBALS['post']->ID, 'meta_description', true) ) {
				$content = $meta;
			} elseif ( ! empty($GLOBALS['post']->ID) && $meta = get_post_field('post_content', $GLOBALS['post']->ID) ) {
				$content = ( apply_filters("{$this->prefix}/seo/default_description", false) ) ? wp_trim_words($meta, '30', '') : null;
			}
		}

		if ( $content ) {
			return $content;
		}
	}

	/**
	 * Determine whether this is the posts page, when it's not the frontpage.
	 *
	 * @return bool
	 */
	public function is_posts_page() {
		return ( is_home() && 'page' == get_option( 'show_on_front' ) );
	}

	/**
	 * Canonical URL.
	 *
	 * @since	2.1.0
	 * @return	void
	 */
	public function seo_canonical()
	{
		$canonical = false;

		// Set decent default canonicals for homepage, singulars and taxonomy pages.
		if ( is_singular() ) {
			$obj       = get_queried_object();
			$canonical = get_post_meta( $obj->ID, 'meta_canonical', true );
			if ( ! $canonical ) {
				$canonical = get_permalink( $obj->ID );
			}

			// Fix paginated pages canonical, but only if the page is truly paginated.
			if ( get_query_var( 'page' ) > 1 ) {
				$num_pages = ( substr_count( $obj->post_content, '<!--nextpage-->' ) + 1 );
				if ( $num_pages && get_query_var( 'page' ) <= $num_pages ) {
					if ( ! $GLOBALS['wp_rewrite']->using_permalinks() ) {
						$canonical = add_query_arg( 'page', get_query_var( 'page' ), $canonical );
					} else {
						$canonical = user_trailingslashit( trailingslashit( $canonical ) . get_query_var( 'page' ) );
					}
				}
			}
		} elseif ( is_search() ) {
			$search_query = get_search_query();

			// Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
			if ( ! empty( $search_query ) && ! preg_match( '|^page/\d+$|', $search_query ) ) {
				$canonical = get_search_link();
			}
		} elseif ( is_front_page() ) {
			$canonical = home_url('/');
		} elseif ( $this->is_posts_page() ) {
			$posts_page_id = get_option( 'page_for_posts' );
			$canonical     = get_post_meta( $posts_page_id, 'meta_canonical', true );

			if ( ! $canonical ) {
				$canonical = get_permalink( $posts_page_id );
			}
		} elseif ( is_tax() || is_tag() || is_category() ) {
			$term = get_queried_object();
			if ( ! empty($term) ) {
				$term_link = get_term_link( $term, $term->taxonomy );
				if ( ! is_wp_error( $term_link ) ) {
					$canonical = $term_link;
				}
			}
		} elseif ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$canonical = get_post_type_archive_link( $post_type );
		} elseif ( is_author() ) {
			$canonical = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
		} elseif ( is_archive() ) {
			if ( is_date() ) {
				if ( is_day() ) {
					$canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
				}
				elseif ( is_month() ) {
					$canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
				}
				elseif ( is_year() ) {
					$canonical = get_year_link( get_query_var( 'year' ) );
				}
			}
		}

		if ( $canonical && get_query_var( 'paged' ) > 1 ) {
			global $wp_rewrite;
			if ( ! $wp_rewrite->using_permalinks() ) {
				if ( is_front_page() ) {
					$canonical = trailingslashit( $canonical );
				}
				$canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
			} else {
				if ( is_front_page() ) {
					$canonical = home_url('/');
				}
				$canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
			}
		}

		$canonical = apply_filters( "{$this->prefix}/seo/field/canonical", $canonical );
		return $canonical;
	}
}

endif;
