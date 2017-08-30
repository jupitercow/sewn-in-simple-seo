# Sewn In Simple SEO

A very simple SEO interface without caricatures and cruft. New improved social support.

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

## Turn on Auto-Generated Descriptions

We don't recommend this. The meta description should be used as a way to craft what the search engines show, otherwise, let the search engines auto generate the descriptions. It doesn't really have a significant impact on visibility and may hinder visibility by keeping the search engine from digging as deep into the rest of the page. Bad descriptions may do harm, and likely don't do any good.

```php
add_filter( 'sewn/seo/default_description', '__return_true' );
```


= Other filters =

```php
// Custom home or blog page title
add_filter( 'sewn/seo/home_title', 'custom_seo_home_title' );
function custom_seo_home_title( $title ) {
	return 'My blog page title';
}
```

```php
// Customize 404 titles
add_filter( 'sewn/seo/404_title', 'custom_seo_404_title' );
function custom_seo_404_title( $title ) {
	return 'These are not the pages you are looking for';
}
`

```php
// Customize archive titles
add_filter( 'sewn/seo/archive_title', 'custom_seo_archive_title' );
function custom_seo_archive_title( $title ) {
	// Customize the title
}
```

```php
// Custom archive descriptions
add_filter( 'sewn/seo/archive_description', 'custom_seo_archive_description' );
function custom_seo_archive_description( $description ) {
	// Custom description here
}
```

```php
// Turn on open graph type field
add_filter( 'sewn/seo/add_type', '__return_true' );
```