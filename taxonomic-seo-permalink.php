<?php
/*
Plugin Name: Taxonomic SEO Permalink
Plugin URI: http://rakesh.tembhurne.com/projects/taxonomic-seo-permalinks/
Description: Creates Taxonomies and changes url structure for displaying results
Version: 0.3.1
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

class Tax_Seo_Perma {
    
    public function __construct() {
        // Hooks
        add_filter('rewrite_rules_array',array($this, 'create_rewrite_rules'));
        add_filter('wp_loaded', array($this, 'flush_rules'));
        add_filter('post_link', array($this, 'modify_link_addresses'), 10, 3);
        //add_filter('post_type_link', 'tsp_write_link_addresses', 10, 3);
    }
    
    public function flush_rules(){
        global $wp_rewrite;
        $wp_rewrite->flush_rules(); 
    }

    public function create_rewrite_rules($rewrite) {
        global $wp_rewrite;

        // loop through custom taxonomies
        $args = array(
            'public'   => true,
            '_builtin' => false 
        );
        $output 			= 'names'; // or objects
        $operator 			= 'and'; // 'and' or 'or'
        $custom_taxonomies 	= get_taxonomies($args, $output, $operator); 
        if ($custom_taxonomies) {
            foreach ($custom_taxonomies as $tax_name ) {
                $tax_token = '%'.$tax_name.'%';
                $wp_rewrite->add_rewrite_tag($tax_token, '(.+)', $tax_name.'=');
            }
        }

        // read current permalink structure and set the same structre
        $keywords_rewrite = $wp_rewrite->generate_rewrite_rules($wp_rewrite->root.$wp_rewrite->permalink_structure);
        return ( $rewrite + $keywords_rewrite );
    }

    public function modify_link_addresses($permalink, $post_id, $leavename)
    {
        global $blog_id;
        global $wp_rewrite;
        // this is user's permalink structure set in options
        $permastruct = $wp_rewrite->permalink_structure;

        $args = array(
            'public'   => true,
            '_builtin' => false 
        );
        $output 			= 'names'; // or objects
        $operator 			= 'and'; // 'and' or 'or'
        $custom_taxonomies 	= get_taxonomies($args, $output, $operator);

        if ($custom_taxonomies) {
            foreach ($custom_taxonomies as $tax_name ) {
                $tax_token = '%'.$tax_name.'%';

                $tax_terms = get_the_terms( $post->id, $tax_name );
                //var_dump($tax_terms);
                if ( !empty($tax_terms) )
                {
                    foreach($tax_terms as $a_term)
                    {
                        $long_slug = $a_term->slug;
                        if( false != (int)$a_term->parent ) { // a's parent p exists
                            $p_term = get_term( (int)$a_term->parent, $tax_name );//var_dump($p_term->slug);
                            $long_slug = ($p_term->slug) ? $p_term->slug .'+'. $long_slug : $long_slug;
                        } 
                        if( false != (int)$a_term->parent AND false != (int)$p_term->parent ) { // p's parent g exists
                            $g_term = get_term( (int)$p_term->parent, $tax_name );
                            $long_slug = ($g_term->slug) ? $g_term->slug .'+'. $long_slug : $long_slug;
                        }

                        $permalink = str_replace($tax_token, $long_slug, $permalink);
                        break;
                    }
                } else {$permalink = str_replace($tax_token, 'no-'.$tax_name, $permalink); }
            }
        }

        return $permalink;
    }
}

$tsp = new Tax_Seo_Perma();
?>