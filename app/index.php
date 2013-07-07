<!doctype html>
<!--[if lte IE 8]>      <html id="ng-app" ng-app="App" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html ng-app="App" class="no-js"> <!--<![endif]-->
	<head>
		<title><?php wp_title( '|', true, 'right' ); ?></title>

		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>

		<!--[if lte IE 8]>
		<style type="text/css" src="<?php echo get_template_directory_uri(); ?>/styles/ie.css"></style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.2.4/json3.min.js"></script>
		<script type="text/javascript">
		document.createElement('ng-view');
		</script>
		<![endif]-->

		<?php wp_head(); ?>
</head>
	<body ng-controller="MainCtrl">
		<!--[if lt IE 8]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->

		<div id="site">

			<header id="site-header" class="fixed">
				<nav class="top-bar">
					<ul class="title-area">
						<li class="name">
							<h1 id="site-title"><a href="/" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						</li>
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
					</ul>

					<section class="top-bar-section">
					<?php
						$menu_name = 'primary';

						if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
							$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

							$menu_items = wp_get_nav_menu_items($menu->term_id);

							$menu_list = '<ul id="menu-' . $menu_name . '" class="left">';

							foreach ( (array) $menu_items as $key => $menu_item ) {
								$url = $menu_item->url;
								$menu_list .= '<li ng-class="siteMenu.activeClass(\'' . $url . '\')"><a href="' . $url . '">' . $menu_item->title . '</a></li>';
							}
							$menu_list .= '</ul>';
						} else {
							$menu_list = '<ul class="left"><li>Menu "' . $menu_name . '" non definito.</li></ul>';
						}

						echo $menu_list;
					?>
					</section>
				</nav>
			</header>

			<div id="site-loader" ng-show="loader.loading" ng-animate="'loader-animation'">
				<center style="margin-top:100px">
					<div class="loader rspin">
						<span class="c"></span>
						<span class="d spin"><span class="e"></span></span>
						<span class="r r1"></span>
						<span class="r r2"></span>
						<span class="r r3"></span>
						<span class="r r4"></span>
					</div>
				</center>
			</div>

			<div id="site-content" ng-view ng-animate="'view-animation'"></div>

		</div>
		<footer id="site-footer">
			footer
		</footer>

		<script type="text/javascript">
		window.wordpress = {
			templateUrl: "<?php echo get_template_directory_uri() ?>",
			data: <?php echo ng_current_page_data(); ?>
		};
		</script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/4.1.6/js/foundation.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/script.js"></script>

		<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
		<script>
			var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src='//www.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>

		<?php wp_footer(); ?>
	</body>
</html>
