<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>
<?php ngwp_call('the_post'); ?>

<div class="row" ng-cloak>
	<div class="small-12 columns">
		<article class="post post-format-standard">
			<header class="post-header">
				<div class="post-meta">
					<div class="post-date" ng-bind="data.post.date|date:'dd/MM/yyyy'"><?php ngwp_call('the_date'); ?></div>
				</div>
				<h1 class="post-title" ng-bind-html="data.post.title"><?php ngwp_call('the_title'); ?></h1>
			</header>
			<div class="post-content" bind-html-compile="data.post.content"><?php ngwp_call('the_content'); ?></div>
		</article>
		<div ng-if="!data||!data.post" class="no-result not-found" ng-cloak>
			<?php _e('Nothing found.', 'ngwp'); ?> <a href="<?php echo get_site_url(); ?>"><?php _e('Back to home', 'ngwp'); ?></a>
		</div>
	</div>
</div>
