=== Sewn In Simple SEO ===
Contributors: jcow, ekaj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jacobsnyder%40gmail%2ecom&lc=US&item_name=Jacob%20Snyder&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: seo,search engine,meta data
Requires at least: 3.6.1
Tested up to: 4.7.3
Stable tag: 2.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A very simple SEO interface without caricatures and cruft. New improved social support.


== Description ==

Adds a fast, simple interface for adding SEO meta data to pages and posts. Designed to remove all of the extra stuff that you just won't use. It is made to be straight forward for users with no confusing extras and no annoying ads. So you can enjoy using it and feel comfortable putting it before a client.

*	Choose which post types it is added to (public post types by default)
*	Integrates nicely with the [Sewn In Simple SEO](https://wordpress.org/plugins/sewn-in-simple-seo/) plugin, but it is not required.

Very simple, no cruft or extra features you won't use.

= Control what post types are added =

By default only pages and posts are added, but you can remove either of those and/or add more using this filter:

`
/**
 * Add a post type to the XML sitemap and add SEO edit panel to it
 *
 * Takes the default array('post','page') and adds 'news' and 'event' post types 
 * to it. Returns: array('post','page','news','event')
 *
 * @param	array	$post_types	List of post types to be added to the XML Sitemap
 * @return	array	$post_types	Modified list of post types
 */
add_filter( 'sewn/seo/post_types', 'custom_sitemap_post_types' );
function custom_sitemap_post_types( $post_types ) {
	$post_types[] = 'news';
	$post_types[] = 'event';
	return $post_types;
}
`

`
/**
 * Completely replace the post types in the XML sitemap and SEO edit functionality
 *
 * This will replace the default completely. Returns: array('news','event')
 *
 * The result is to remove 'post' and 'page' post types and to add 'news' and 
 * 'event' post types
 *
 * @param	array	$post_types	List of post types to be added to the XML Sitemap
 * @return	array	$post_types	Modified list of post types
 */
add_filter( 'sewn/seo/post_types', 'custom_sitemap_post_types' );
function custom_sitemap_post_types( $post_types ) {
	$post_types = array('news','event');
	return $post_types;
}
`

= Compatibility =

Works with the [Sewn In Simple SEO](https://wordpress.org/plugins/sewn-in-simple-seo/) plugin and the [Sewn In Simple Social Optimization](https://wordpress.org/plugins/sewn-in-simple-social/) plugin (coming soon).


== Installation ==

*   Install like any other plugin, directly from your plugins page.


== Frequently Asked Questions ==

= No questions yet. =


== Screenshots ==


== Changelog ==

= 2.1.0 - 2017-02-29 =

*   Initial split off of the SEO plugin.


== Upgrade Notice ==

