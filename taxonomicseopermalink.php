<?php
/*
Plugin Name: Taxonomic SEO Permalink
Plugin URI: http://rakesh.tembhurne.com/projects/taxonomic-seo-permalinks/
Description: Creates Taxonomies and changes url structure for displaying results
Version: 0.1.3
Author: Rakesh Tembhurne
Author URI: http://rakesh.tembhurne.com
License: GPL2
*/
/*  Copyright 2010  Rakesh Tembhurne  (email : rakesh@tembhurne.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.
*/

include (dirname(__FILE__) . '/class.tsp.php');


if ( class_exists("TaxonomicSeoPermalink") ) 
{
	// define functions
	function taxonomic_seo_permalink_init()
	{
		
		$tsp = new TaxonomicSeoPermalink();
		// create post type
		//$tsp->create_post_types();
		// create taxonomies
		$tsp->create_taxonomies();
	}
	
	function taxonomic_seo_permalink($permalink, $post_id, $leavename)
	{
		$tsp = new TaxonomicSeoPermalink();
		// if user is ready with permalink structure
		if ( $tsp->is_set_permalink_structure($permalink) )
		{
			// rewrite all links
			$permalink = $tsp->write_link_addresses($permalink, $post_id, $leavename);
			// parse entered url	
		}
		
		return $permalink;
	}

	function tsp_add_rewrite_tags()
	{
		$tsp = new TaxonomicSeoPermalink();
		
		//$tsp->add_tsp_rewrite_rules();
		$tsp->add_tsp_rewrite_rules();
	}
	
	// Remember to flush_rules() when adding rules
	function flushRules(){
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

//Actions and Filters	

	//Actions
	//add_action('init','flushRules');
	add_action('init', 'taxonomic_seo_permalink_init');	
	add_action('generate_rewrite_rules', 'tsp_add_rewrite_tags');
	//Filters
	
	add_filter('post_link', 'taxonomic_seo_permalink', 10, 3);
	add_filter('post_type_link', 'taxonomic_seo_permalink', 10, 3);
	


?>