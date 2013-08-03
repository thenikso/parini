<?php
/** Crawlers support */

// Prepares the `ngwp_call` function to have the correct behaviour wether
// the view template is being rendered from within `index.php` for a call by a
// crawler, or as an AngularJS template requested via AJAX.
if ( !defined('ABSPATH') )
{
	define('NGWP_IS_TEMPLATE_VIEW', true);
}
else
{
	define('NGWP_IS_TEMPLATE_VIEW', false);
}

// If `NGWP_NEEDS_WORDPRESS` is set, Wordpress is needed by the view even when
// called via AJAX for Angular to use. This may be the case if Wordpress options
// are needed to complete the rendering of the view template.
// To load options use:
// global $ng_options; // default options
// $ng_settings = get_option( 'ng_options', $ng_options );
if ( defined('NGWP_NEEDS_WORDPRESS') ) {
	define('WP_USE_THEMES', true);
	if (file_exists(dirname(__FILE__) . '/../../../wp-load.php'))
		require_once (dirname(__FILE__) . '/../../../wp-load.php');
	else
		require_once (dirname(__FILE__) . '/../../../../wp-load.php');
}

$ngwp_have_posts_mock = true;
function ngwp_call($fun)
{
	// Handle the function specially when executed in a view called via AJAX
	if ( NGWP_IS_TEMPLATE_VIEW ) {
		switch ($fun) {
			case 'have_posts':
				global $ngwp_have_posts_mock;
				if ($ngwp_have_posts_mock)
				{
					$ngwp_have_posts_mock = false;
					return true;
				}
				else
				{
					$ngwp_have_posts_mock = true;
					return false;
				}
				break;
		}
		// Ignore all other functions
		return;
	}

	// When included in a page for crawling, exectute the function as normal
	if (func_num_args() > 1)
	{
		return call_user_func_array($fun, array_slice(func_get_args(), 1));
	}
	return $fun();
}
