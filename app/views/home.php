<?php
// Prepare the view for possible inclusion in `index.php` when called by a crawler.
// Also request for Wordpress to be loaded in order to retrieve theme options.
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');

global $ngwp_options;
$ng_settings = get_option( 'ngwp_options', $ngwp_options );
?>

<section class="slogan" parallax-box>
	<div class="slogan-background" style="background-image:url('<?php echo $ng_settings['home_slogan_background_url']; ?>');" parallax-multiplier="0.5"></div>
	<article class="slogan-content">
		<div class="row">
			<div class="small-12 columns">
				<?php echo $ng_settings['home_slogan_content']; ?>
			</div>
		</div>
	</article>
	<a smooth-scroll="home-posts-wall" speed="1000" offset="45" class="slogan-scroll-link hide-for-small"></a>
</section>

<section id="home-posts-wall" class="posts-wall-container home-content">
	<?php require (dirname(__FILE__).'/posts-wall.php'); ?>
</section>
