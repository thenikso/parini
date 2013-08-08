<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns" ng-bind-html="data.page.content"><?php ngwp_call('the_content'); ?></div>
</div>

<section
	id="page-wall"
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
