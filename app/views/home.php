<?php
define('WP_USE_THEMES', true);
if (file_exists('../../../../wp-load.php'))
	require_once ('../../../../wp-load.php');
else
	require_once ('../../../../../wp-load.php');

global $ng_options;
$ng_settings = get_option( 'ng_options', $ng_options );
?>

<section class="slogan" parallax-box>
	<img class="slogan-background" src="<?php echo $ng_settings['home_slogan_background_url']; ?>" parallax-multiplier="0.5">
	<article class="slogan-content">
		<div class="row">
			<div class="small-12 columns">
				<h1 class="slogan-title"><?php echo $ng_settings['home_slogan_content']; ?></h1>
			</div>
		</div>
	</article>
	<a smooth-scroll-jquery target="home-post-wall" speed="2000" offset="45" class="slogan-scroll-link hide-for-small">News Recenti</a>
</section>

<div id="home-post-wall">
	<section class="post-wall" masonry="{ columnWidth: '.grid-sizer' }">
		<div class="grid-sizer"></div>
		<article masonry-brick class="post" ng-repeat="post in data.posts">
			<header>
				<div class="post-meta">
					<div class="post-category-icon" ng-if="post.categories.length" ng-class="'category-icon-'+post.categories[0].slug"></div>
					<span class="post-date" ng-bind="post.date|date:'dd/MM/yyyy'"></span>
					<span class="post-categories" ng-if="post.categories.length">&ndash;
						<span ng-repeat="category in post.categories">
							<a ng-href="/topics/{{category.slug}}">{{category.title}}</a><span class="sep" ng-if="!$last">,</span>
						</span>
					</span>
				</div>
				<a ng-href="{{post.url}}"><h3 class="post-title" ng-bind="post.title"></h3></a>
			</header>
			<a class="post-link" ng-href="{{post.url}}">
				<img class="post-thumbnail" ng-src="{{post.thumbnail_images.large.url}}" ng-if="post.thumbnail_images" masonry-layout-on="post.thumbnail_images.large.url">
				<div class="post-content" ng-bind-html-unsafe="post.excerpt"></div>
			</a>
		</article>
	</section>
	<a href="" class="post-wall-load-more-link" ng-class="{ 'loading': data.isLoadingMore }" ng-if="data.hasMore()" ng-click="data.loadMore()">Load More</a>
</div>
