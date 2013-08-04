<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>
<?php ngwp_call('the_post'); ?>

<div class="row">
	<div class="small-12 columns" bind-compile="data.page.content"><?php ngwp_call('the_content'); ?></div>
</div>

<section
	ng-if="wall.data&&wall.type=='posts'"
	ng-include="wordpress.templateUrl+'/views/post-wall.php'"></section>
