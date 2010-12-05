<?php
/*
Plugin Name: Taxonomic SEO Permalink
Plugin URI: http://rakesh.tembhurne.com/projects/taxonomic-seo-permalinks/
Description: Creates Taxonomies and changes url structure for displaying results
Version: 0.1 Beta
Author: Rakesh Tembhurne
Author URI: http://rakesh.tembhurne.com
License: GPL2
*/
add_action('init', 'taxonomic_seo_permalink_init');
 
function taxonomic_seo_permalink_init() {
	// In order like example.com/first/second/third/postname
	$taxonomies = array("Season", "Course", "Semester");
	
	foreach($taxonomies as $taxonomy)
	{
		if (!is_taxonomy(strtolower($taxonomy))) {
			register_taxonomy( $taxonomy, 'post', 
					   array( 	'hierarchical' => TRUE, 'label' => __($taxonomy),  
							'public' => TRUE, 'show_ui' => TRUE,
							'query_var' => strtolower($taxonomy),
							'rewrite' => true ) );
		}
	}
}

add_filter('post_link', 'taxonomic_seo_permalink', 10, 3);
add_filter('post_type_link', 'taxonomic_seo_permalink', 10, 3);
 
function taxonomic_seo_permalink($permalink, $post_id, $leavename) {
	$taxonomies = array("Season", "Course", "Semester");
	
	foreach($taxonomies as $taxonomy)
	{
		if ( strpos($permalink, '%'.strtolower($taxonomy).'%') === FALSE )
			return $permalink;
	}
	
	// Get post
	$post = get_post($post_id);
	if (!$post) return $permalink;

	foreach($taxonomies as $taxonomy)
	{
		// Get taxonomy terms
		$terms 	= wp_get_object_terms($post->ID, "'".strtolower($taxonomy)."'");
		// Get taxonomy slug
		if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $slug = $terms[0]->slug;
		else $slug = 'no-'.strtolower($taxonomy);

		// Modify Permalink
		$permalink = str_replace('%'.strtolower($taxonomy).'%', $slug, $permalink);
	}

	return $permalink;
}
?>