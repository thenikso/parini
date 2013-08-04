<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns">
		<article class="post">
			<header>
				<h1 class="post-title" ng-bind="data.post.title"><?php ngwp_call('the_title'); ?></h1>
			</header>
			<div class="post-content" bind-compile="data.post.content"><?php ngwp_call('the_content'); ?></div>
		</article>
		<div ng-if="!data||!data.post" class="no-result not-found">
			Nessun post trovato. <a href="/">Torna alla homepage</a>
		</div>
	</div>
</div>