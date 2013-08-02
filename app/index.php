<!doctype html>
<!--[if lte IE 8]>     <html id="ng-app" ng-app="WordpressApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html ng-app="WordpressApp" class="no-js"> <!--<![endif]-->
	<head>
		<?php if ($_SERVER['HTTP_HOST'] != 'localhost'): ?><base href="/parini/" /><?php endif; ?>

		<title ng-bind-template="<?php bloginfo( 'name' ); ?>{{document.title&&(' | '+document.title)}}"><?php wp_title( '|', true, 'right' ); ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href='http://fonts.googleapis.com/css?family=Quicksand:300,400,700|Arapey:400,400italic' rel='stylesheet' type='text/css' />
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>

		<!--[if lte IE 8]>
		<style type="text/css" src="<?php echo get_template_directory_uri(); ?>/styles/ie.css"></style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.2.4/json3.min.js"></script>
		<script type="text/javascript">
		// Add every element directvie to the array here to have it available in IE8
		for (d in ['ng-view'])
			document.createElement(d);
		</script>
		<![endif]-->

		<?php wp_head(); ?>
	</head>
	<body ng-controller="MainCtrl" wp-href-lang="{{lang}}" ng-class="body.classes">
		<!--[if lt IE 8]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->

		<div id="site">

			<header id="site-header">
				<nav class="top-bar">
					<ul class="title-area">
						<li class="name">
							<h1 id="site-title"><a wp-href="<?php echo get_site_url(); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						</li>
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
					</ul>

					<section class="top-bar-section">
					<?php
						$menu_name = 'primary';

						if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
							$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

							$menu_items = wp_get_nav_menu_items($menu->term_id);

							$menu_list = '<ul id="' . $menu_name . '-menu" class="left">';

							foreach ( (array) $menu_items as $key => $menu_item ) {
								$url = $menu_item->url;
								$menu_list .= '<li wp-href-active-class><a wp-href="' . $url . '" class="' . join(' ', $menu_item->classes) . '">' . $menu_item->title . '</a></li>';
							}
							$menu_list .= '</ul>';
						} else {
							$menu_list = '<ul class="left"><li>Menu "' . $menu_name . '" non definito.</li></ul>';
						}

						echo $menu_list;
					?>
					<ul id="secondary-menu" class="right">
						<li class="hide-for-small"><a href="" class="social-link twitter">Twitter</a></li>
						<li class="hide-for-small"><a href="" class="social-link facebook">Facebook</a></li>
						<li class="hide-for-small"><a href="" class="social-link instagram">Instagram</a></li>
						<li><a wp-href-change lang="en" ng-if="lang!='en'" target="_self">Eng</a></li>
						<li><a wp-href-change lang="" ng-if="lang" target="_self">Ita</a></li>
					</ul>
					</section>
				</nav>
			</header>

			<div id="site-loader" ng-show="loading" ng-animate="'loader-animation'">
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

			<div id="site-content" ng-view ng-animate="'view-animation'">
				<?php
				if (($path = ngwp_crawler_path()) !== null) {
					switch (ngwp_template_for_path($path)) {
						case 'post':
							get_template_part('/views/post');
							break;
						case 'page':
							get_template_part('/views/page');
							break;
						default: // home
							get_template_part('/views/home');
							break;
					}
				} ?>
			</div>

		</div><!-- #site -->

		<?php
		global $ng_options;
		$ng_settings = get_option( 'ng_options', $ng_options );
		?>
		<footer reveal-sheet-stack id="site-footer">
			<a reveal-sheet="reference" id="footer-map" href="<?php echo $ng_settings['footer_background_link']; ?>" target="_blank" style="background-image:url('<?php echo $ng_settings['footer_background_url']; ?>')"></a>
			<div reveal-sheet id="footer-contacts">
				<?php echo $ng_settings['footer_content']; ?>
			</div>
		</footer><!-- #site-footer -->

		<!-- Cleanup start -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/4.1.6/js/foundation.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/3.0.0/masonry.pkgd.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/2.1.0/jquery.imagesloaded.min.js"></script>
		<!-- Cleanup end -->
		<script src="<?php echo get_template_directory_uri(); ?>/script.js"></script>
		<script type="text/javascript">
		angular.module('WordpressApp').constant('wordpress', {
			info: {
				name: "<?php bloginfo( 'name' ); ?>"
			},
			siteUrl: "<?php echo get_site_url(); ?>",
			templateUrl: "<?php echo get_template_directory_uri() ?>",
			routes: <?php echo ngwp_routes_object_json(); ?>,
			language: <?php echo ngwp_sitepress_languages_json(); ?>,
			data: <?php echo ngwp_query_data_json(); ?>
		});
		</script>

		<?php wp_footer(); ?>
	</body>
</html>
