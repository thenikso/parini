<div class="thumbnails-wall" ng-init="theData = (wall.data||data)">

	<article class="thumbnails-wall-brick" ng-repeat="post in theData.posts track by post.id">
		<div class="thumbnails-wall-content">
			<header class="post-header">
				<p class="thumbnails-wall-open-label">View Project</p>
				<h2 class="post-title" ng-bind="post.title"></h2>
				<div class="post-excerpt subheader" ng-bind-html="post.excerpt"></div>
			</header>
			<img class="post-thumbnail" ng-src="{{post.thumbnail_images.large.url||'http://placehold.it/300'}}" alt="{{post.title}}">
			<a class="post-link" ng-href="{{post.url}}"></a>
		</div>
	</article>

</div>
<a href="" class="wall-load-more-link" ng-class="{ 'loading': theData.isLoadingMore }" ng-if="theData.hasMore()" ng-click="theData.loadMore()" ng-cloak>Mostra pi&ugrave; articoli</a>

<div ng-if="!theData||!theData.posts||!theData.posts.length" class="wall-no-results no-result not-found" ng-cloak>
	Nessun post trovato. <a href="/">Torna alla homepage</a>
</div>
