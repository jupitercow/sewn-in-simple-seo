=== Sewn In Simple SEO ===
Contributors: jcow, ekaj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jacobsnyder%40gmail%2ecom&lc=US&item_name=Jacob%20Snyder&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: seo,search engine,meta data
Requires at least: 3.6.1
Tested up to: 4.8.1
Stable tag: 2.1.3
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


= Add Keywords =

Use sparingly. Meta keywords can do more harm than help when used incorrectly. Don't overload them. For that reason, we turned it off by default. If you want them, you can turn them back on by adding this to your functions.php.

`
add_filter( 'sewn/seo/add_keywords', '__return_true' );
`

= Turn on Auto-Generated Descriptions =

We don't recommend this. The meta description should be used as a way to craft what the search engines show, otherwise, let the search engines auto generate the descriptions. It doesn't really have a significant impact on visibility and may hinder visibility by keeping the search engine from digging as deep into the rest of the page. Bad descriptions may do harm, and likely don't do any good.

`
add_filter( 'sewn/seo/default_description', '__return_true' );
`

= Other filters =

`
/**
 * Custom home or blog page title
 */
add_filter( 'sewn/seo/home_title', 'custom_seo_home_title' );
function custom_seo_home_title( $title ) {
	return 'My blog page title';
}
`

`
/**
 * Customize 404 titles
 */
add_filter( 'sewn/seo/404_title', 'custom_seo_404_title' );
function custom_seo_404_title( $title ) {
	return 'These are not the pages you are looking for';
}
`

`
/**
 * Customize archive titles
 */
add_filter( 'sewn/seo/archive_title', 'custom_seo_archive_title' );
function custom_seo_archive_title( $title ) {
	// Customize the title
}
`

`
/**
 * Custom archive descriptions
 */
add_filter( 'sewn/seo/archive_description', 'custom_seo_archive_description' );
function custom_seo_archive_description( $description ) {
	// Custom description here
}
`

= Compatibility =

Works with the [Sewn In XML Sitemap](https://wordpress.org/plugins/sewn-in-xml-sitemap/) plugin and the [Sewn In Simple Social Optimization](https://wordpress.org/plugins/sewn-in-simple-social/) plugin (coming soon). When installed, the XML sitemap checkbox integrates with the SEO fields. The goal is to keep things very simple and integrated.


== Installation ==

*   Copy the folder into your plugins folder, or use the "Add New" plugin feature.
*   Activate the plugin via the Plugins admin page


== Frequently Asked Questions ==

= No questions yet. =


== Screenshots ==

1. The SEO panel added to posts.
1. The SEO panel, Advanced tab.
1. The SEO panel with [Sewn In XML Sitemap](https://wordpress.org/plugins/sewn-in-xml-sitemap/) & [Sewn In Simple Social Optimization](https://wordpress.org/plugins/sewn-in-simple-social/) installed.


== Changelog ==

*   Initial split off of the SEO plugin.

= 1.0.0 - 2016-01-29 =

*   Updated sewn meta and readme.

= 2.0.8 - 2015-10-15 =

*   Descriptions are no longer auto created. Only crafted descriptions are used. Can be turned back on using this filter: 'sewn/seo/default_description'

= 2.0.7 - 2015-07-27 =

*   Updated the archive default seo titles and descriptions.

= 2.0.6 - 2015-07-27 =

*   Updated the default post types to ALL public post types except 'attachment'.

= 2.0.5 - 2015-03-09 =

*   Small update to make sure admin javascript loads.

= 2.0.4 - 2015-03-08 =

*   Fixed issues with global post on 404s, etc.
*   Added support for Open Graph type field.

= 2.0.3 - 2015-02-13 =

*   Fixed problem with post_types in new system.

= 2.0.2 - 2015-02-13 =

*   Fixed issues with empty fields.
*   Corrected some issues with connection to xml sitemap.

= 2.0.1 - 2015-02-13 =

*   Small bugs with empty field arrays.
*   Issue with XML connection.

= 2.0.0 - 2015-02-12 =

*   Added to the repo.


== Upgrade Notice ==

= 2.0.7 =
Fixed the archive/taxonomy titles. Changed default post types used from only 'post' and 'page' to ALL public post types except "attachment". This is a cleaner approach, but may require customization.

= 2.0.6 =
Changed default post types used from only 'post' and 'page' to ALL public post types except "attachment". This is a cleaner approach, but may require customization.

= 2.0.0 =
This is the first version in the Wordpress repository.

