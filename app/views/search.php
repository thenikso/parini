<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>

<section id="category-posts-wall" class="posts-wall-container category-section">
	<header>
		<h1 class="category-title">Risultati per: <span ng-bind="searchQuery"><?php ngwp_call('get_search_query'); ?></span></h1>
	</header>

	<?php require (dirname(__FILE__).'/posts-wall.php'); ?>
</section>
