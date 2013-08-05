<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns" ng-bind-html="data.page.content"><?php ngwp_call('the_content'); ?></div>
</div>

<section
	id="page-wall"
	ng-if="wall.data"
	ng-include="wordpress.templateUrl+'/views/'+wall.type+'-wall.php'"></section>

<div class="row" ng-if="!data||!data.page" ng-cloak>
	<div class="small-12 columns">
		<p>
			La pagina non esiste! <a href="/">Torna alla homepage</a>
		</p>
	</div>
</div>
