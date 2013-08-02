<?php require_once (dirname(__FILE__).'/../prepare-view.php'); ?>
<?php ngwp_call('the_post'); ?>

<div bind-compile="data.page.content"><?php ngwp_call('the_content'); ?></div>
