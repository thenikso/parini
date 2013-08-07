<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>

<div class="thumbnails-wall" ng-init="theData = (wall.data||data)">

	<?php
	// By using `ngwp_call`, the correct behaviour of the requested method will
	// be used if the page is being included in PHP or requested as a template.
	while ( ngwp_call('have_posts') ) : ngwp_call('the_post'); ?>

	<article class="thumbnails-wall-brick" ng-repeat="post in theData.posts track by post.id">
		<div class="thumbnails-wall-content">
			<header class="post-header">
				<p class="thumbnails-wall-open-label"><?php _e('View project', 'ngwp'); ?></p>
				<h2 class="post-title" ng-bind="post.title"><?php ngwp_call('the_title'); ?></h2>
				<div class="post-excerpt subheader" ng-bind-html="post.excerpt"><?php ngwp_call('the_excerpt'); ?></div>
			</header>
			<img
				class="post-thumbnail"
				src="<?php echo ngwp_call('wp_get_attachment_url', ngwp_call('get_post_thumbnail_id') ); ?>"
				ng-src="{{post.thumbnail_images.large.url||'http://placehold.it/300'}}"
				alt="{{post.title}}">
			<a class="post-link" ng-href="{{post.url}}" href="<?php echo ngwp_call('the_permalink'); ?>"><?php ngwp_call('the_title'); ?></a>
		</div>
	</article>

	<?php endwhile; ?>

</div>
<a href="" class="wall-load-more-link" ng-class="{ 'loading': theData.isLoadingMore }" ng-if="theData.hasMore()" ng-click="theData.loadMore()" ng-cloak>Mostra pi&ugrave; articoli</a>

<div ng-if="!theData||!theData.posts||!theData.posts.length" class="wall-no-results no-result not-found" ng-cloak>
	<?php _e('Nothing found.', 'ngwp'); ?> <a href="<?php echo get_site_url(); ?>"><?php _e('Back to home', 'ngwp'); ?></a>
</div>
