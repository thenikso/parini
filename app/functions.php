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
// RewriteRule ^$ $1 [QSA,L]
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

			// Get category
			if ( is_category() ) {
				$data['category'] = new JSON_API_Category(get_category(get_query_var('cat'),false));
			}
		}
		return json_encode($data);
	}

} else {

	function ngwp_query_data_json() {
		return "null";
	}

}

/** Wordpress setup */

function ngwp_setup() {

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
add_action( 'init', 'ngwp_setup' );

function ngwp_widgets_init()
{
	register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'ngwp' ),
		'id' => 'footer-1',
		'description' => __( 'Appears on the bottom of the readable footer area', 'ngwp' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
}
add_action( 'widgets_init', 'ngwp_widgets_init' );

/** Meta Boxes */

function ngwp_add_meta_boxes()
{
	add_meta_box( 'ngwp-page-wall-box', __( 'Posts Wall Settings', 'ngwp' ), 'ngwp_page_wall_meta_box', 'page', 'side', 'core' );
}
add_action( 'add_meta_boxes', 'ngwp_add_meta_boxes' );

function ngwp_page_wall_meta_box()
{
	global $post;
	$values = array(
		'type' => get_post_meta( $post->ID, 'ngwpPageWallType', true ),
		'posts' => get_post_meta( $post->ID, 'ngwpPageWallPosts', true ),
		'count' => get_post_meta( $post->ID, 'ngwpPageWallCount', true ),
		'postsType' => get_post_meta( $post->ID, 'ngwpPageWallPostsType', true ),
		'date' => get_post_meta( $post->ID, 'ngwpPageWallDate', true ),
		'category' => get_post_meta( $post->ID, 'ngwpPageWallCategory', true ),
		'tag' => get_post_meta( $post->ID, 'ngwpPageWallTag', true ),
		'author' => get_post_meta( $post->ID, 'ngwpPageWallAuthor', true ),
		'search' => get_post_meta( $post->ID, 'ngwpPageWallSearch', true )
	);

	wp_nonce_field( 'ngwp_page_wall_meta_box_nonce', 'ngwp_page_wall_meta_box_nonce_name' );
?>
	<p><?php _e( 'A posts wall like the one in the homepage or a thumbnails wall can be displayed after the content of the page.', 'ngwp' ); ?></p>
	<p>
		<label for="ngwp-page-wall-box-type">Type</label>
		<select name="ngwp-page-wall-box[type]" id="ngwp-page-wall-box-type" class="ngwp-show-has-class-like-id">
			<option value="">None</option>
			<option value="posts" <?php selected( $values['type'], 'posts' ); ?>>Posts Wall</option>
			<option value="thumbnails" <?php selected( $values['type'], 'thumbnails' ); ?>>Thumbnails Wall</option>
		</select>
	</p>
	<div class="has-ngwp-page-wall-box-type" style="display:none">
		<p>
			<label for="ngwp-page-wall-box-posts">Posts</label>
			<select name="ngwp-page-wall-box[posts]" id="ngwp-page-wall-box-posts" class="ngwp-show-has-class-like-val">
				<option value="">Posts</option>
				<option value="recent" <?php selected( $values['posts'], 'recent' ); ?>>Recent Posts</option>
				<option value="date" <?php selected( $values['posts'], 'date' ); ?>>Date Posts</option>
				<option value="category" <?php selected( $values['posts'], 'category' ); ?>>Category Posts</option>
				<option value="tag" <?php selected( $values['posts'], 'tag' ); ?>>Tag Posts</option>
				<option value="author" <?php selected( $values['posts'], 'author' ); ?>>Author Posts</option>
				<option value="search" <?php selected( $values['posts'], 'search' ); ?>>Search Posts</option>
			</select>
		</p>

		<p>
			<label fore="ngwp-page-wall-box-count">Count per page</label>
			<input type="number" name="ngwp-page-wall-box[count]" id="ngwp-page-wall-box-count" value="<?php echo $values['count'] ? $values['count'] : '10'; ?>" style="width: 4em;">
		</p>

		<?php
		$post_types = get_post_types(array('_builtin' => false), 'objects');
		if (count($post_types) > 0):
		?>
		<p>
			<label for="ngwp-page-wall-box-posts-type">Post type</label>
			<select name="ngwp-page-wall-box[postsType]" id="ngwp-page-wall-box-posts-type">
				<option value="">Posts</option>
				<?php foreach ($post_types as $pt): ?>
				<option value="<?php echo $pt->name; ?>" <?php selected( $values['postsType'], $pt->name ); ?>><?php echo $pt->label; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php endif; ?>

		<p class="has-ngwp-page-wall-box-posts ngwp-is-date" style="display:none;">
			<label for="ngwp-page-wall-box-date">Date</label>
			<input type="date" id="ngwp-page-wall-box-date" name="ngwp-page-wall-box[date]" value="<?php echo $values['date']; ?>">
		</p>

		<p class="has-ngwp-page-wall-box-posts ngwp-is-category" style="display:none;">
			<label for="ngwp-page-wall-box-category">Category</label>
			<select name="ngwp-page-wall-box[category]" id="ngwp-page-wall-box-category">
				<?php foreach (get_categories() as $c): ?>
				<option value="<?php echo $c->slug; ?>" <?php selected( $values['category'], $c->slug ); ?>><?php echo $c->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="has-ngwp-page-wall-box-posts ngwp-is-tag" style="display:none;">
			<label for="ngwp-page-wall-box-tag">Tag</label>
			<select name="ngwp-page-wall-box[tag]" id="ngwp-page-wall-box-tag">
				<?php foreach (get_tags() as $c): ?>
				<option value="<?php echo $c->slug; ?>" <?php selected( $values['tag'], $c->slug ); ?>><?php echo $c->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="has-ngwp-page-wall-box-posts ngwp-is-author" style="display:none;">
			<label for="ngwp-page-wall-box-author">Author</label>
			<select name="ngwp-page-wall-box[author]" id="ngwp-page-wall-box-author">
				<?php foreach (get_users() as $c): ?>
				<option value="<?php echo $c->user_nicename; ?>" <?php selected( $values['author'], $c->user_nicename ); ?>><?php echo $c->display_name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="has-ngwp-page-wall-box-posts ngwp-is-search" style="display:none;">
			<label for="ngwp-page-wall-box-search">Search</label>
			<input type="text" id="ngwp-page-wall-box-search" name="ngwp-page-wall-box[search]" value="<?php echo $values['search']; ?>">
		</p>

	</div>
	<script type="text/javascript">
	jQuery('.ngwp-show-has-class-like-id').change(function () {
		var $el = jQuery('.has-'+this.id);
		if (jQuery(this).val()) $el.fadeIn('fast');
		else $el.fadeOut('fast');
	}).change();
	jQuery('.ngwp-show-has-class-like-val').change(function () {
		var $el = jQuery('.has-'+this.id+'.ngwp-is-'+jQuery(this).val());
		jQuery('.has-'+this.id).not($el).fadeOut('fast', function () {
			$el.fadeIn('fast');
		});
	}).change();
	</script>
<?php
}

function ngwp_meta_box_save( $post_id )
{
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post', $post_id ) ) return;

	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['ngwp_page_wall_meta_box_nonce_name'] ) || !wp_verify_nonce( $_POST['ngwp_page_wall_meta_box_nonce_name'], 'ngwp_page_wall_meta_box_nonce' ) ) return;

	foreach ($_POST['ngwp-page-wall-box'] as $key => $value) {
		update_post_meta( $post_id, 'ngwpPageWall'.ucfirst($key), $value );
	}
}
add_action( 'save_post', 'ngwp_meta_box_save' );


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
