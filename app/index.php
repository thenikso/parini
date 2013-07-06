<!doctype html>
<!--[if lt IE 9]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<title><?php wp_title( '|', true, 'right' ); ?></title>

		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>

		<!--[if lt IE 9]>
		<style type="text/css" src="<?php echo get_template_directory_uri(); ?>/styles/ie.css"></style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.2.4/json3.min.js"></script>
		<![endif]-->

		<?php wp_head(); ?>
</head>
	<body ng-app="App">
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->

		<div id="site">

			<header id="site-header">
				<nav class="top-bar">
					<ul class="title-area">
						<li class="name">
							<h1 id="site-title"><a href="/" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						</li>
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
					</ul>

					<section class="top-bar-section">
						<?php wp_nav_menu( array(
							'container' => false,
							'theme_location' => 'primary',
							'menu_class' => 'left'
						) ); ?>
					</section>
				</nav>
			</header>

			<div id="site-content" ng-view></div>

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
