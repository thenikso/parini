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

	// Adding post thumbnail support
	add_theme_support( 'post-thumbnails' );
}
add_action( 'init', 'parini_setup' );

// Rewrite rules to build angular routing
function ng_rewrite_rules() {
	global $wp_rewrite;
	$rules = array();
	$paramRegexp = '/%([a-z\-]+)%/i';
	$paramReplace = ':$1';
	function addLeadingSlash($value) {
		if (substr($value, 0, 1) != '/') $value = '/' . $value;
		return $value;
	}
	// Post
	$rules['post'] = addLeadingSlash(preg_replace($paramRegexp, $paramReplace, get_option('permalink_structure')));
	// Page
	$rules['page'] = addLeadingSlash(preg_replace($paramRegexp, $paramReplace, $wp_rewrite->get_page_permastruct()));
	// Custom post types
	$postType = array();
	foreach (get_post_types(array('_builtin' => false)) as $post_type) {
		$postType[$post_type] = addLeadingSlash(preg_replace($paramRegexp, ':postname', $wp_rewrite->get_extra_permastruct($post_type)));
	}
	if (count($postType)) $rules['postTypes'] = $postType;
	return json_encode($rules);
}

//
function ng_sitepress_languages() {
	global $sitepress;
	if (!$sitepress) {
		return 'null';
	}
	$default = $sitepress->get_default_language();
	$languages = array();
	foreach ($sitepress->get_active_languages() as $lang) {
		if ($lang['code'] == $default) continue;
		$languages[] = $lang['code'];
	}
	return json_encode(array(
		'default' => $default,
		'others' => $languages
		));
}

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


/** Administration */

$ng_options = array(
	'footer_content' => '&copy; ' . date('Y') . get_bloginfo('name'),
	'footer_background_url' => '',
	'footer_background_link' => ''
);

function ng_admin_init()
{
	register_setting( 'ng_theme_options', 'ng_options', 'ng_validate_options' );
	add_editor_style( 'style.css' );
}
add_action( 'admin_init', 'ng_admin_init' );

function ng_admin_menu() {
	add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'ng_theme_options_page' );
}
add_action( 'admin_menu', 'ng_admin_menu' );

function ng_theme_options_page() {
	global $ng_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options' ) . "</h2>";
		// This shows the page's name and an icon if one has been provided ?>

		<?php if ( false !== $_REQUEST['updated'] ) : ?>
		<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
		<?php endif; // If the form has just been submitted, this shows the notification ?>

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'footer_options'; ?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=theme_options&amp;tab=footer_options" class="nav-tab <?php echo $active_tab == 'footer_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
		</h2>

		<form method="post" action="options.php">

			<?php $settings = get_option( 'ng_options', $ng_options ); ?>

			<?php settings_fields( 'ng_theme_options' ); ?>

			<?php if ($active_tab == 'footer_options'): ?>

			<div><?php wp_editor($settings['footer_content'], 'ng_options[footer_content]'); ?></div>

			<p>
				Footer background map image URL:
				<input id="footer_background_url" name="ng_options[footer_background_url]" type="text" value="<?php  esc_attr_e($settings['footer_background_url']); ?>" placeholder="Background map image"/>
				<a href="" data-select-image="footer_background_url">Choose image</a>
			</p>

			<p>
				Footer background map link URL:
				<input id="footer_background_link" name="ng_options[footer_background_link]" type="text" value="<?php  esc_attr_e($settings['footer_background_link']); ?>" placeholder="Background map link"/>
			</p>

			<?php // elseif ($active_tab == '_options') : ?>

			<?php endif; ?>

			<?php submit_button( "Save Options", "primary" ); ?>

		</form>

		<script type="text/javascript">
		jQuery('a[data-select-image]').click(function () {
			var destinationField = jQuery('#' + jQuery(this).data('select-image'));
			window.send_to_editor = function(html) {
				var image_url = jQuery('img',html).attr('src');
				destinationField.val(image_url);
				tb_remove();
			};
			tb_show('Upload an image', 'media-upload.php?referer=theme_options&type=image&TB_iframe=true&post_id=0', false);
			return false;
		});
		</script>
	</div><!-- .wrap -->

<?php
}

function ng_validate_options( $input ) {
	// global $ng_options;

	// $settings = get_option( 'ng_options', $ng_options );

	// 	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	// $input['footer_copyright'] = wp_filter_nohtml_kses( $input['footer_copyright'] );

	// 	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	// $input['intro_text'] = wp_filter_post_kses( $input['intro_text'] );

	// 	// We select the previous value of the field, to restore it in case an invalid entry has been given
	// $prev = $settings['featured_cat'];
	// 	// We verify if the given value exists in the categories array
	// if ( !array_key_exists( $input['featured_cat'], $categories ) )
	// 	$input['featured_cat'] = $prev;

	// 	// We select the previous value of the field, to restore it in case an invalid entry has been given
	// $prev = $settings['layout_view'];
	// 	// We verify if the given value exists in the layouts array
	// if ( !array_key_exists( $input['layout_view'], $layouts ) )
	// 	$input['layout_view'] = $prev;

	// 	// If the checkbox has not been checked, we void it
	// if ( ! isset( $input['author_credits'] ) )
	// 	$input['author_credits'] = null;
	// 	// We verify if the input is a boolean value
	// $input['author_credits'] = ( $input['author_credits'] == 1 ? 1 : 0 );

	return $input;
}