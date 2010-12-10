=== Plugin Name ===
Contributors: tj2point0
Tags: Permalink, Taxonomy, SEO
Requires at least: 3.0.1
Tested up to: 3.0.3
Stable tag: TSPv0.1.1
Donate link: http://rakesh.tembhurne.com/

This plugin manages Wordpress taxonomies and modifies url structure based on taxonomies.

== Description ==

Taxonomic SEO Permalinks is a simple plugin that allows you to create SEO urls based on custom taxonomies in WordPress. Let us understand with the help of an example, what we are trying to achieve with this plugin.

Consider a university website want to build a website for announcing results. The results are announced every six months (summer 2010, winter 2010, ...) for various courses (BSc, BTech, ...) and for various semesters (final year, second semester, ...).

What we want is SEO url with the help of custom WordPress taxonomies, viz., Season, Course and Semester which will look something like this:

<code>http://example.com/winter-2010/bsc/final-year/list-of-passed-candidates</code>

which should be configurable by the user like

<code>/%season%/%course%/%semester%/%postname%/</code>

== Installation ==

= Installation from zip file =

1. Go to Admin > Plugins > Add News  and click on upload link.
2. Browse the zip file and click upload
3. Activate the plugin

= Manual Installation =

1. Download the latest copy of Taxonomic SEO Permalink in .zip format
2. Extract the zip file and Upload to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress

= After Installation =

You need to change the settings in the class.tsp.php file for your taxonomies on line 20 which looks similar to this:

public $taxonomy_terms 	= array("Season", "Course", "Semester");

Change the content of the array with the custom taxonomies you would like, in SEQUENCE, from left to right, and your permalink structure would be update automatically. For above example, it would be:

/%season%/%course%/%semester%/%postname%/

= NOTE =

This plugin won't work with default permalink structure. Under Settings >> Permalink opt for custom permalinks and change it to /%category%/%postname%/. Even if you set this permalink, the TSP plugin will change this permalink with Taxonomic SEO Permalinks.

== Frequently Asked Questions ==

= How does it work =

It looks for the structure of url entered and relates with the taxonomic seo structure you set. if it doesn't match the structure you set, it works the same way, it was working without the plugin. 

For example you want taxonomies "Company", "Model", "Color" and want your urls something like example.com/dell/inspiron/ruby-red/heavy-discount-offer, you can use this plugin to do so. If the entered url does not match with the structure you set, it is handled by default way (like regular example.com/category-name/post-name).

== Changelog ==

= 0.1.0 Beta =
* Need to edit taxonomies in php file of plugin
