<?php
/*
Plugin Name:  Wp Breadcrumbs
Plugin URI:   https://github.com/bdkabiruddin/wp-breadcrumbs/
Description:  WordPress breadcrumbs.
Version:      Version: 1.0.0
Author: Md Kabir Uddin
Author URI:   https://github.com/bdkabiruddin
License:      
License URI: 
*/
function getcategory_with_child($category){
	$cats = array();
	foreach ($category as $cat){
		if ($cat->category_parent!=0){
			array_push($cats, $cat);
		}
	}
	return $cats;
}
	
function get_category_parents_exe( $id, $link = false, $nicename = false, $visited = array() ) {
	$chains = '';
	$term = get_queried_object();
	$parent = get_term( $id, $term->taxonomy );
	if ( is_wp_error( $parent ) ){
		return $parent;
	}
	if ( $nicename ){
		$name = $parent->slug;
	}
	else{
		$name = $parent->name;
	}
	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chains .= get_category_parents_exe( $parent->parent, $link, $nicename, $visited );
	}
	if ( $link ){
		$chains .= $name .'__/__'. get_category_link( $parent->term_id ).',';
	}
	else{
		$chains .= $name .'__/__,';
	}
	return $chains;
}

function wp_breadcrumbs(){
	
	global $post;
	
	$breadcrumbs = array();
	
	if ( !is_front_page() ) {
		$breadcrumbs[] = array(
			'title' => 'Home',
			'link' => home_url()
		);
		
		if ( is_home() ) {
			$breadcrumbs[] = array(
				'title' => __('Blog'),
				'link' => null
			);
		} 
		
		if ( is_archive() ) {
			
			$post_type = get_post_type();
			$post_type_object = get_post_type_object($post_type);
			$post_type_archive = get_post_type_archive_link($post_type);
			$breadcrumbs[] = array(
				'title' => $post_type_object->labels->name,
				'link' => $post_type_archive
			);
			
			if(is_category() || is_tax()){
				$term = get_queried_object();
				
				$get_term_parents = get_category_parents_exe($term->term_id, true);
				$get_term_parents = rtrim($get_term_parents, ',');
				$term_parents = explode(',', $get_term_parents);

				$terms_count = count($term_parents);

				foreach($term_parents as $key => $parents) {
					$parents = explode('__/__', $parents);
					if ($key < $terms_count-1){
						$breadcrumbs[] = array(
								'title' => $parents[0],
								'link' =>  $parents[1]
						);
					} else {
						$breadcrumbs[] = array(
								'title' => $parents[0],
								'link' => null
						);
					}
				}
				
			}
			if ( is_tag() ) {
				$breadcrumbs[] = array(
					'title' => single_tag_title('', false),
					'link' => null
				);
			}
			if ( is_author() ) {
				global $author;
				$userdata = get_userdata( $author );
				$breadcrumbs[] = array(
					'title' => __('Author: ') . $userdata->display_name,
					'link' => null
				);
			} 
			
			if( is_day() ) {
				$breadcrumbs[] = array(
					'title' => get_the_time('Y'),
					'link' => get_year_link( get_the_time('Y') )
				);
				$breadcrumbs[] = array(
					'title' => get_the_time('M'),
					'link' => get_month_link( get_the_time('Y'), get_the_time('m') )
				);
				$breadcrumbs[] = array(
					'title' => get_the_time('jS') . ' ' . get_the_time('M'),
					'link' => null
				);
			}
			
			if( is_month() ) {
				$breadcrumbs[] = array(
					'title' => get_the_time('Y'),
					'link' => get_year_link( get_the_time('Y') )
				);
				$breadcrumbs[] = array(
					'title' => get_the_time('M'),
					'link' => null
				);
			}
			
			if( is_year() ) {
				$breadcrumbs[] = array(
					'title' => get_the_time('Y'),
					'link' => null
				);
			}
		}
		
		if ( get_query_var('paged') ) {
			
			if ( ! is_archive() ) {
				$breadcrumbs[] = array(
					'title' => get_post_type_object(get_post_type())->labels->singular_name,
					'link' => get_post_type_archive_link(get_post_type_object(get_post_type())->query_var)
				);
			}
			
			$breadcrumbs[] = array(
				'title' => __('Page ') . get_query_var('paged'),
				'link' => null
			);
		} 
		
		 if ( is_search() ) {
			 $breadcrumbs[] = array(
				'title' => __('Search results for ') . get_search_query(),
				'link' => null
			);
		} 
		
		if ( is_404() ) {
			$breadcrumbs[] = array(
				'title' => __('Error 404'),
				'link' => null
			);
		}
		
		if(is_singular()){
			
			if(is_single()){
				
				$post_type = get_post_type();
				$post_type_object = get_post_type_object($post_type);
				$post_type_archive = get_post_type_archive_link($post_type);
				$breadcrumbs[] = array(
					'title' => $post_type_object->labels->name,
					'link' => $post_type_archive
				);
				$term = get_the_category();
				if(!empty($term)) {
					$term_wc = getcategory_with_child($term);
					if(!empty($term_wc)){
						$term = $term_wc;
					} else {
						$term = $term;
					}
					if(!empty($term)){
						$term = $term[count($term)-1];
						
						$get_term_parents = get_category_parents_exe($term->term_id, true);
						$get_term_parents = rtrim($get_term_parents, ',');
						$term_parents = explode(',', $get_term_parents);
						foreach($term_parents as $parents) {
							$parents = explode('__/__', $parents);
							$breadcrumbs[] = array(
								'title' => $parents[0],
								'link' => $parents[1]
							);
						}
						
						
					}
				}
				
				$taxonomy = 'product_cat';
				$taxonomy_exists = taxonomy_exists($taxonomy);
				if(empty($term) && $taxonomy_exists) {
					
					$taxonomy_terms = get_the_terms( $post->ID, $taxonomy );
					$base_tex = $taxonomy_terms[0];
					if(count($taxonomy_terms) > 1){
						$term = get_term( $taxonomy_terms[0]->parent, $taxonomy );
						
						$get_term_parents = get_category_parents_exe($term->term_id, true);
						$get_term_parents = rtrim($get_term_parents, ',');
						$term_parents = explode(',', $get_term_parents);
						foreach($term_parents as $parents) {
							$parents = explode('__/__', $parents);
							$breadcrumbs[] = array(
								'title' => $parents[0],
								'link' => $parents[1]
							);
						}
						
					}
					
					$breadcrumbs[] = array(
						'title' => $base_tex->name,
						'link' => get_term_link( $base_tex->term_id )
					);
				}
				$breadcrumbs[] = array(
					'title' => get_the_title(),
					'link' => null
				);
			}
			
			if(is_page()){
				
				  if( $post->post_parent ){
					$anc = get_post_ancestors( $post->ID );
					$anc = array_reverse($anc);
					if ( !isset( $parents ) ) $parents = null;
					foreach ( $anc as $ancestor ) {
						$breadcrumbs[] = array(
							'title' => get_the_title($ancestor),
							'link' => get_permalink($ancestor)
						);
					}
					$breadcrumbs[] = array(
						'title' => get_the_title(),
						'link' => null
					);
					
				} else {
					$breadcrumbs[] = array(
						'title' => get_the_title(),
						'link' => null
					);
				}
			}
		}
	}
	
	$output = '';
	$output .= '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
	$breadcrumbs = array_combine(range(1, count($breadcrumbs)), $breadcrumbs);
	foreach ($breadcrumbs as $key => $breadcrumb) {
		$output .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">'; 
		if(!empty($breadcrumb['link'])){ 
			$output .= '<a itemprop="item" href="'.$breadcrumb['link'].'">';
		}
		$output .= '<span itemprop="name">'.$breadcrumb['title'].'</span>'; 
		if(!empty($breadcrumb['link'])){
			$output .= '</a>';
		}
		$output .= '<meta itemprop="position" content="'.$key.'" />';
		$output .= '</li> ';
	}
	$output .= '</ol>';
	echo  $output;
}
