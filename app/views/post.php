<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns">
		<article class="post">
			<header>
				<h1 class="post-title" ng-bind="data.post.title"><?php ngwp_call('the_title'); ?></h1>
			</header>
			<div class="post-content" bind-html-compile="data.post.content"><?php ngwp_call('the_content'); ?></div>
		</article>
		<div ng-if="!data||!data.post" class="no-result not-found" ng-cloak>
			<?php _e('Nothing found.', 'ngwp'); ?> <a href="<?php echo get_site_url(); ?>"><?php _e('Back to home', 'ngwp'); ?></a>
		</div>
	</div>
</div>
