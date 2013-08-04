<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>

<div class="post-wall" masonry="{ columnWidth: '.grid-sizer' }" ng-init="theData = (wall.data||data)">
	<div class="grid-sizer"></div>

	<?php
	// By using `ngwp_call`, the correct behaviour of the requested method will
	// be used if the page is being included in PHP or requested as a template.
	while ( ngwp_call('have_posts') ) : ngwp_call('the_post'); ?>

	<article masonry-brick class="post" ng-repeat="post in theData.posts track by post.id">
		<header>
			<div class="post-meta">
				<div class="post-category-icon" ng-if="post.categories.length" ng-class="'category-icon-'+post.categories[0].slug"></div>
				<span class="post-date" ng-bind="post.date|date:'dd/MM/yyyy'"></span>
				<span class="post-categories" ng-if="post.categories.length">&ndash;
					<span ng-repeat="category in post.categories">
						<a href="<?php echo ngwp_call('the_permalink'); ?>" ng-href="/topics/{{category.slug}}" ng-bind="category.title"></a><span class="sep" ng-if="!$last">,</span>
					</span>
				</span>
			</div>
			<h3 class="post-title"><a ng-href="{{post.url}}" ng-bind="post.title"><?php ngwp_call('the_title'); ?></a></h3>
		</header>
		<a class="post-link" ng-href="{{post.url}}" href="<?php echo ngwp_call('the_permalink'); ?>">
			<img class="post-thumbnail" ng-src="{{post.thumbnail_images.large.url}}" ng-if="post.thumbnail_images" masonry-layout-on="post.thumbnail_images.large.url">
			<?php ngwp_call('the_post_thumbnail'); ?>
			<div class="post-content" ng-bind-html-unsafe="post.excerpt"><?php ngwp_call('the_excerpt'); ?></div>
		</a>
	</article>

	<?php endwhile; ?>

</div>
<a href="" class="post-wall-load-more-link" ng-class="{ 'loading': theData.isLoadingMore }" ng-if="theData.hasMore()" ng-click="theData.loadMore()">Mostra pi&ugrave; articoli</a>

<div ng-if="!theData||!theData.posts||!theData.posts.length" class="post-wall-no-results no-result not-found">
	Nessun post trovato. <a href="/">Torna alla homepage</a>
</div>
