<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>

<section id="category-posts-wall" class="posts-wall-container category-section">
	<header>
		<h1 class="category-title"><?php _e('Category archives:', 'ngwp'); ?> <span ng-bind="data.category.title"><?php ngwp_call('single_cat_title'); ?></span></h1>
	</header>

	<?php require (dirname(__FILE__).'/posts-wall.php'); ?>
</section>
