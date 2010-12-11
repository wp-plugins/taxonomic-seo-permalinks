<?php
/**
 * Taxonomic SEO Permalink
 * @author tj2point0 [Rakesh Tembhurne, rakesh@tembhurne.com]
 * @package TaxonomicSeoPermalink
 * @since 0.1.0
 */
class TaxonomicSeoPermalink
{
	/**
	 * Post Types
	 * @var array
	 */
	public $post_types 		= array("Result");
	/**
	 * Taxonomy Terms
	 * @var array
	 */
	public $taxonomy_terms 	= array("Season", "Course", "Semester");
	/**
	 * Taxonomic SEO Permalink
	 * Constructor function
	 */
	function TaxonomicSeoPermalink()
	{
		// constructor
	}
	/**
	 * Create Post Types
	 * creates new post types 
	 */
	function create_post_types()
	{
		// looping multiple
		foreach ($this->post_types as $post_type)
		{
			register_post_type( 
				$post_type,
                array( 
                	'label' => __($post_type), 
                	'public' => true, 
                	'show_ui' => true 
                ) 
            );
			register_taxonomy_for_object_type('post_tag', $post_type);
		}
	}
	/**
	 * Create Taxonomies
	 * creates new taxonomies if do not exist
	 */
	function create_taxonomies()
	{
		// looping multiple
		foreach($this->taxonomy_terms as $term)
		{
			$term_lowercase = strtolower($term); // TODO: proper use of uppercase and lowercase
			if ( !is_taxonomy($term_lowercase) ) 
			{
				register_taxonomy( 
					$term_lowercase, 
					'post', 
					array(
						'hierarchical' => FALSE, 
						'label' 	=> __($term_lowercase),  
						'sort'		=> TRUE,
						'public' 	=> TRUE, 
						'show_ui' 	=> TRUE,
						'args'		=> array('orderby' => 'term_order'),
						'query_var' => $term_lowercase,
						'rewrite' 	=> TRUE 
					)	// TODO: use minimum parameters
				);
			}
		}
	}
	/**
	 * Is Set Permalink
	 * checks if user has set the url structure in admin >> settings >> permalink
	 * @param $permalink
	 */
	function is_set_permalink_structure($permalink)
	{
		foreach($this->taxonomy_terms as $term)
		{
			$term_lower = strtolower($term);
			
			//print('%'.$term_lower.'%');
			if ( strpos($permalink, '%'.$term_lower.'%') === FALSE )
				return false;
		}
		
		return true;
	}
	/**
	 * Write Link Addresses
	 * Changes all link addresses based on taxonomic permalink structure in admin >> settings >> permalink
	 * @param $permalink
	 * @param $post_id
	 * @param $leavename
	 */
	function write_link_addresses($permalink, $post_id, $leavename)
	{
		global $blog_id;
		// Get post
		$post = get_post($post_id);
		if (!$post) return $permalink;
		
		// MULTISITE main blog
		
		if ( is_multisite() && $blog_id == SITE_ID_CURRENT_SITE )
		{
			$primary_url = get_bloginfo("wpurl");
			$permalink = str_replace($primary_url, "$primary_url/blog", $permalink);
		}
		
		foreach($this->taxonomy_terms as $term)
		{
			$term = strtolower($term);
			// Get taxonomy terms of post
			$wp_term 	= wp_get_object_terms($post->ID, $term, $args = array());
			// Get taxonomy slug
			if (!is_wp_error($wp_term) && !empty($wp_term) && is_object($wp_term[0])) $slug = $wp_term[0]->slug;
			else $slug = 'no-'.$term;
	
			// Modify Permalink
			$permalink = str_replace('%'.$term.'%', $slug, $permalink);
		}
	
		return $permalink;
	}
	
	function add_tsp_rewrite_rules()
	{
		global $wp_rewrite;
		//$wp_rewrite->flush_rules();
		$keywords_structure = $wp_rewrite->root.'/';
		$perma = "";
		foreach ($this->taxonomy_terms as $term)
		{
			$term = strtolower($term);
			$keytag_token = '%'.$term.'%';
			$wp_rewrite->add_rewrite_tag($keytag_token, '([^/]+)', $term.'=');
			$keywords_structure .= $keytag_token.'/';
			$perma .= '/%'.$term.'%';
		}
		$wp_rewrite->set_permalink_structure("$keywords_structure%postname%/");
		update_option('permalink_structure',"$keywords_structure%postname%/");
		
		$keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);
		$wp_rewrite->rules = $keywords_rewrite + $wp_rewrite->rules;
		
		return $wp_rewrite->rules;
	}
}
?>