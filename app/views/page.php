<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns" ng-bind-html="data.page.content"><?php ngwp_call('the_content'); ?></div>
</div>

<section
	id="page-children"
	class="fade-animation"
	ng-if="data.page.children">

	<ul
		class="page-children-dots"
		ng-if="pageSettings.showChildrenDots"
		ng-style="{ height:data.page.children.length*20 }">
		<li
			ng-repeat="page in data.page.children track by page.id"
			ng-class="{ 'is-active':page.isCurrent }">
			<a href="" smooth-scroll="page-child-{{$index}}" speed="1000" offset="45"></a>
		</li>
	</ul>

	<article
		ng-repeat="page in data.page.children track by page.id"
		id="page-child-{{$index}}"
		class="no-animations page-child page-child-{{$index}} page-{{page.slug}} page-{{page.id}}"
		ng-class="{ 'no-animations':!(pageSettings.animateChildrenInView&&page.enteredView) }"
		in-view="page.enteredView = page.enteredView||$inview">

		<div class="page-child-current-marker" in-view="page.isCurrent = $inview"></div>

		<div class="row">
			<div class="small-12 columns" ng-bind-html="page.content"></div>
		</div>

	</article>

</section>

<section
	id="page-wall"
	class="fade-animation"
	ng-if="wall.data"
	ng-include="wordpress.templateUrl+'/views/'+wall.type+'-wall.php'">
	<?php ngwp_call('ngwp_echo_page_wall'); ?>
</section>

<div class="row" ng-if="!data||!data.page" ng-cloak>
	<div class="small-12 columns">
		<p>
			<?php _e('The page does not exists!', 'ngwp'); ?> <a href="<?php echo get_site_url(); ?>"><?php _e('Back to home', 'ngwp'); ?></a>
		</p>
	</div>
</div>
