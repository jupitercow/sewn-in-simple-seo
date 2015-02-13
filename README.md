# Sewn In Simple SEO

A nice and simple way to create XML Sitemaps when a page or post is saved. Very simple, no cruft or extra features you won't use.

## Control what post types are added

By default only pages and posts are added, but you can remove either of those and/or add more using this filter:

```php
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
```

```php
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
```

## Add Keywords

Use sparingly. Meta keywords can do more harm than help when used incorrectly. Don't overload them. For that reason, we turned it off by default. If you want them, you can turn them back on by adding this to your functions.php.

```php
add_filter( 'sewn/seo/add_keywords', '__return_true' );
```


## Open Graph Images

This plugin includes some support for open graph images via the featured image field. Coming soon is a specific upload field.


## Automated Header Info

Sewn In Simple SEO adds the necessary info to the header, but if you would like to be more deliberate, you can turn that off and the items you want back in manually.

Turn off the automated fields:

```php
add_filter( 'sewn/seo/automate_head', '__return_false' );`
```

The actions that currently get automated in (along with the meta title):

```php
do_action( 'sewn/seo/description' );
do_action( 'sewn/seo/keywords' );
do_action( 'sewn/seo/classification' );
do_action( 'sewn/seo/site_name' );
do_action( 'sewn/seo/og:title' );
do_action( 'sewn/seo/og:image' );
do_action( 'sewn/seo/og:type' );
```