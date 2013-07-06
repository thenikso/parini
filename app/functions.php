<?php

// Setup
function parini_setup() {

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu' ) );

	// See http://justintadlock.com/archives/2010/04/29/custom-post-types-in-wordpress
	// Custom post for Works
	register_post_type( 'lavori',
		array(
			'labels' => array(
				'name' => __( 'Lavori' ),
				'singular_name' => __( 'Lavoro' )
			),
			'rewrite' => array(
				'with_front' => false
			),
			'public' => true,
		)
	);
}
add_action( 'init', 'parini_setup' );

// Output data only if JSON API plugin is installed and active
if (class_exists("JSON_API_Post")) {

function ng_current_page_data() {
	if ( !have_posts() ) return "null";
	global $post, $wp_query;
	// Get single page
	if ( is_page() ) {
		$data = array(
			'status' => 'ok',
			'page' => new JSON_API_Post($post)
		);
	}
	// Get single post
	elseif ( is_single() )
	{
		$previous = get_adjacent_post(false, '', true);
		$next = get_adjacent_post(false, '', false);
		$data = array(
			'status' => 'ok',
			'post' => new JSON_API_Post($post)
		);
		if ($previous) {
			$data['previous_url'] = get_permalink($previous->ID);
		}
		if ($next) {
			$data['next_url'] = get_permalink($next->ID);
		}
	}
	// Get all posts
	else
	{
		$posts = array();
		while ( have_posts() ) {
			the_post();
			$posts[] = new JSON_API_Post($post);
		}
		$data = array(
			'count' => count($posts),
			'count_total' => (int) $wp_query->found_posts,
			'pages' => $wp_query->max_num_pages,
			'posts' => $posts
		);
	}
	return json_encode($data);
}

} else {

function ng_current_page_data() {
	return "null";
}

}
