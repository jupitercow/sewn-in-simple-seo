=== Sewn In Simple SEO ===
Contributors: jcow, ekaj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jacobsnyder%40gmail%2ecom&lc=US&item_name=Jacob%20Snyder&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: seo,search engine,meta data
Requires at least: 3.6.1
Tested up to: 4.1
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A very simple SEO interface without caracatures and cruft.


== Description ==

Designed to remove all of the extra stuff that you just won't use. It is made to be straight forward for users with not confusing extras and no annoying ads. So you can enjoy using it and feel comfortable putting it before a client.

*	Choose which post types it is added to (posts and pages by default)
*	Integrates nicely with the Sewn In XML Sitemap plugin, so they get merged into one panel for editing

# Sewn In Simple SEO

A nice and simple way to create XML Sitemaps when a page or post is saved. Very simple, no cruft or extra features you won't use.

## Control what post types are added

By default only pages and posts are added, but you can remove either of those and/or add more using this filter:

`
/**
 * Add a post type to the XML sitemap
 *
 * Takes the default array('post','page') and adds 'news' and 'event' post types 
 * to it. Returns: array('post','page','news','event')
 *
 * @param	array	$post_types	List of post types to be added to the XML Sitemap
 * @return	array	$post_types	Modified list of post types
 */
add_filter( 'sewn_seo/post_types', 'custom_sitemap_post_types' );
function custom_sitemap_post_types( $post_types )
{
	$post_types[] = 'news';
	$post_types[] = 'event';
	return $post_types;
}
`

`
/**
 * Completely replace the post types in the XML sitemap
 *
 * This will replace the default completely. Returns: array('news','event')
 *
 * The result is to remove 'post' and 'page' post types and to add 'news' and 
 * 'event' post types
 *
 * @param	array	$post_types	List of post types to be added to the XML Sitemap
 * @return	array	$post_types	Modified list of post types
 */
add_filter( 'sewn_seo/post_types', 'custom_sitemap_post_types' );
function custom_sitemap_post_types( $post_types )
{
	$post_types = array('news','event');
	return $post_types;
}
`

## Add Keywords

Use sparingly. Meta keywords can do more harm than help when used incorrectly. Don't overload them. For that reason, we turned it off by default. If you want them, you can turn them back on by adding this to your functions.php.

`
add_filter( 'sewn/seo/add_keywords', '__return_true' );
`


## Open Graph Images

This plugin includes some support for open graph images via the featured image field. Coming soon is a specific upload field.


## Automated Header Info

Sewn In Simple SEO adds the necessary info to the header, but if you would like to be more deliberate, you can turn that off and the items you want back in manually.

Turn off the automated fields:

`
add_filter( 'sewn/seo/automate_head', '__return_false' );`
`

The actions that currently get automated in (along with the meta title):

`
do_action( 'sewn/seo/description' );
do_action( 'sewn/seo/keywords' );
do_action( 'sewn/seo/classification' );
do_action( 'sewn/seo/site_name' );
do_action( 'sewn/seo/og:title' );
do_action( 'sewn/seo/og:image' );
do_action( 'sewn/seo/og:type' );
`


= Compatibility =

This works with the Sewn In XML Sitemap plugin. When installed, the XML sitemap checkbox integrates with the SEO fields. The goal is to keep things very simple and integrated.


== Installation ==

*   Copy the folder into your plugins folder, or use the "Add New" plugin feature.
*   Activate the plugin via the Plugins admin page


== Frequently Asked Questions ==

= No questions yet. =


== Screenshots ==

1. The checkbox to remove posts in the backend.


== Changelog ==

## 2.0.0 - 2015-02-12

- Added to the repo


== Upgrade Notice ==

= 2.0.0 =
This is the first version in the Wordpress repository.
