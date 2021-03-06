<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>

<div class="posts-wall" masonry="{ columnWidth: '.grid-sizer' }" ng-init="theData = (wall.data||data)">
	<div class="grid-sizer"></div>

	<?php
	// By using `ngwp_call`, the correct behaviour of the requested method will
	// be used if the page is being included in PHP or requested as a template.
	while ( ngwp_call('have_posts') ) : ngwp_call('the_post'); ?>

	<article masonry-brick class="post" ng-repeat="post in theData.posts track by post.id">
		<header class="post-header">
			<div class="post-meta">
				<a class="post-category-icon"
					ng-if="post.categories.length"
					ng-class="'category-icon-'+post.categories[0].slug"
					ng-href="/topics/{{post.categories[0].slug}}"></a>
				<span class="post-date" ng-bind="post.date|date:'dd/MM/yyyy'"><?php ngwp_call('the_date'); ?></span>
				<span class="post-categories" ng-if="post.categories.length">&ndash;
					<span ng-repeat="category in post.categories">
						<a href="<?php echo ngwp_call('the_permalink'); ?>" ng-href="/topics/{{category.slug}}" ng-bind="category.title"></a><span class="sep" ng-if="!$last">,</span>
					</span>
				</span>
			</div>
			<h2 class="post-title"><a ng-href="{{post.url}}" href="<?php ngwp_call('the_permalink'); ?>" ng-bind-html="post.title"><?php ngwp_call('the_title'); ?></a></h2>
		</header>
		<img
			class="post-thumbnail"
			src="<?php echo ngwp_call('wp_get_attachment_url', ngwp_call('get_post_thumbnail_id') ); ?>"
			ng-src="{{post.thumbnail_images.large.url}}"
			ng-if="post.thumbnail_images"
			masonry-layout-on="post.thumbnail_images.large.url"
			alt="{{post.title}}">
		<a class="post-link" ng-href="{{post.url}}" href="<?php echo ngwp_call('the_permalink'); ?>">
			<div class="post-content" ng-bind-html="post.excerpt"><?php ngwp_call('the_excerpt'); ?></div>
		</a>
	</article>

	<?php endwhile; ?>

</div>
<a href="" class="wall-load-more-link" ng-class="{ 'loading': theData.isLoadingMore }" ng-if="theData.hasMore()" ng-click="theData.loadMore()" ng-cloak>
	<?php _e('Mostra più articoli', 'ngwp'); ?>
</a>

<div ng-if="!theData||!theData.posts||!theData.posts.length" class="wall-no-results no-result not-found" ng-cloak>
	<?php _e('Nothing found.', 'ngwp'); ?> <a href="<?php echo get_site_url(); ?>"><?php _e('Back to home', 'ngwp'); ?></a>
</div>
