<?php
/** Crawler support */

// Gets the requested path if the website visitor is a crawler.
// To make WordPress return the proper content, the following configuration should
// be added to .htaccess after the WordPress symlink configuration:
//
// # BEGIN Angular-WordPress-Theme
// <IfModule mod_rewrite.c>
// RewriteEngine On
// RewriteCond %{QUERY_STRING} ^_escaped_fragment_=(.*)$
// RewriteRule ^$ $1 [L]
// </IfModule>
// # END Angular-WordPress-Theme
function ngwp_crawler_path()
{
	if (isset($_GET['_escaped_fragment_'])) {
		return $_GET['_escaped_fragment_'];
	}
	$spiders = array('Googlebot', 'Yammybot', 'Openbot', 'Yahoo', 'Slurp', 'msnbot', 'ia_archiver', 'Lycos', 'Scooter', 'AltaVista', 'Teoma', 'Gigabot', 'Googlebot-Mobile');
	foreach ($spiders as $s) {
		if (strstr($_SERVER['HTTP_USER_AGENT'], $s)) {
			return $_SERVER['REQUEST_URI'];
		}
	}
	return null;
}

function ngwp_get_permastructs($transform=null)
{
	if (!$transform)
	{
		$transform = function($x) { return $x; };
	}
	global $wp_rewrite;
	$rules = array();
	// Post
	$rules['post'] = $transform(get_option('permalink_structure'));
	// Categories
	$rules['category'] = $transform($wp_rewrite->get_category_permastruct());
	// Author
	$rules['author'] = $transform($wp_rewrite->get_author_permastruct());
	// Search
	$rules['search'] = $transform($wp_rewrite->get_search_permastruct());
	// Date
	$rules['date'] = $transform($wp_rewrite->get_date_permastruct());
	// Custom post types
	$postType = array();
	foreach (get_post_types(array('_builtin' => false)) as $post_type) {
		$postType[$post_type] = $transform($wp_rewrite->get_extra_permastruct($post_type));
	}
	if (count($postType)) $rules['postTypes'] = $postType;
	// Page
	$rules['page'] = $transform($wp_rewrite->get_page_permastruct());
	return $rules;
}

function ngwp_template_for_path($path)
{
	if (strpos($path, '/') === 0)
	{
		$path = substr($path, 1);
	}
	if (strlen($path) === 0)
	{
		return "home";
	}
	function matchPath($template, $permastructs, $path)
	{
		foreach ($permastructs as $regexp => $rewrite)
		{
			if (is_array($rewrite))
			{
				$m = matchPath($regexp, $rewrite, $path);
				if ($m) return $m;
			}
			elseif (preg_match('['.$regexp.']', $path) >= 1)
			{
				return $template;
			}
		}
		return null;
	}
	foreach (ngwp_get_permastructs(function($p) {
		global $wp_rewrite;
		return $wp_rewrite->generate_rewrite_rule($p);
	}) as $name => $rr)
	{
		$m = matchPath($name, $rr, $path);
		if ($m) return $m;
	}
	return null;
}

/** JSON Data builders */

// Rewrite rules to build angular routing
function ngwp_routes_object_json() {
	function addLeadingSlash($value) {
		if (substr($value, 0, 1) != '/') $value = '/' . $value;
		return $value;
	}
	return json_encode(ngwp_get_permastructs(function($p) {
		return addLeadingSlash(preg_replace('/%([a-z\-]+)%/i', ':$1', $p));
	}));
}

//
function ngwp_sitepress_languages_json() {
	global $sitepress;
	if (!$sitepress)
	{
		return 'null';
	}
	$default = $sitepress->get_default_language();
	$languages = array();
	foreach ($sitepress->get_active_languages() as $lang)
	{
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

	function ngwp_query_data_json() {
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

	function ngwp_query_data_json() {
		return "null";
	}

}

/** Wordpress setup */

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


/** Wordpress Administration Setup */

$ng_options = array(
	'home_slogan_content' => get_bloginfo('name'),
	'home_slogan_background_url' => '',
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

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home_slogan_options'; ?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=theme_options&amp;tab=home_slogan_options" class="nav-tab <?php echo $active_tab == 'home_slogan_options' ? 'nav-tab-active' : ''; ?>">Home Slogan</a>
			<a href="?page=theme_options&amp;tab=footer_options" class="nav-tab <?php echo $active_tab == 'footer_options' ? 'nav-tab-active' : ''; ?>">Footer</a>
		</h2>

		<form method="post" action="options.php">

			<?php $settings = get_option( 'ng_options', $ng_options ); ?>

			<?php settings_fields( 'ng_theme_options' ); ?>

			<?php if ($active_tab == 'home_slogan_options'): ?>

			<div><?php wp_editor($settings['home_slogan_content'], 'ng_options[home_slogan_content]'); ?></div>

			<p>
				Home slogan background image URL:
				<input id="home_slogan_background_url" name="ng_options[home_slogan_background_url]" type="text" value="<?php  esc_attr_e($settings['home_slogan_background_url']); ?>"/>
				<a href="" data-select-image="home_slogan_background_url">Choose image</a>
			</p>

			<?php elseif ($active_tab == 'footer_options') : ?>

			<div><?php wp_editor($settings['footer_content'], 'ng_options[footer_content]'); ?></div>

			<p>
				Footer background map image URL:
				<input id="footer_background_url" name="ng_options[footer_background_url]" type="text" value="<?php  esc_attr_e($settings['footer_background_url']); ?>"/>
				<a href="" data-select-image="footer_background_url">Choose image</a>
			</p>

			<p>
				Footer background map link URL:
				<input id="footer_background_link" name="ng_options[footer_background_link]" type="text" value="<?php  esc_attr_e($settings['footer_background_link']); ?>"/>
			</p>

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
	global $ng_options;

	$settings = get_option( 'ng_options', $ng_options );

	// Keep previous values
	foreach ($settings as $key => $value) {
		if ( !isset( $input[$key] ) ) {
			$input[$key] = $value;
		}
	}

	return $input;
}
