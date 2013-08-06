<?php
define('NGWP_NEEDS_WORDPRESS', true);
require_once (dirname(__FILE__).'/../prepare-view.php');
?>

<section id="date-posts-wall" class="posts-wall-container date-section">
	<header>
		<h1 class="date-title"><?php _e('Date archives:', 'ngwp'); ?>
			<span ng-bind="date.year"><?php ngwp_call('get_the_date', 'Y'); ?></span><span ng-if="date.month" class="sep">/</span><span ng-bind="date.month"></span><span ng-if="date.day" class="sep">/</span><span ng-bind="date.day"></span>
		</h1>
	</header>

	<?php require (dirname(__FILE__).'/posts-wall.php'); ?>
</section>
