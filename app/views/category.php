<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>

<section id="category-post-wall" class="post-wall-container category-section">
	<header>
		<h1 class="category-title">Archivio per categoria: <span ng-bind="data.category.title"><?php ngwp_call('single_cat_title'); ?></span></h1>
	</header>

	<?php require (dirname(__FILE__).'/post-wall.php'); ?>
</section>
