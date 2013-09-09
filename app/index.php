<!doctype html>
<!--[if lte IE 8]>     <html id="ng-app" ng-app="WordpressApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html ng-app="WordpressApp" class="no-js"> <!--<![endif]-->
	<head>
		<?php ngwp_echo_base_tag( ($_SERVER['HTTP_HOST'] != 'localhost') ? '/parini' : '' ); ?>

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
	<body ng-controller="MainCtrl" ng-class="body.classes">
		<?php
		global $ngwp_options;
		$ng_settings = get_option( 'ngwp_options', $ngwp_options );
		?>

		<!--[if lt IE 8]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->

		<div id="site">

			<header id="site-header">
				<nav class="top-bar">
					<ul class="title-area">
						<li class="name">
							<h1 id="site-title">
								<a href="<?php echo site_url(); ?>" rel="home">
									<?php if ($ng_settings['header_logo_url']): ?>
										<img src="<?php echo $ng_settings['header_logo_url']; ?>" alt="<?php get_bloginfo( 'name' ); ?>">
									<?php else: ?>
										<?php bloginfo( 'name' ); ?>
									<?php endif; ?>
								</a>
							</h1>
						</li>
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
					</ul>

					<section class="top-bar-section">
					<?php wp_nav_menu(array(
							'location' => 'primary',
							'container' => false,
							'menu_class' => 'left wp-nav-menu'
						));
					?>
					<ul id="secondary-menu" class="right">
						<li class="hide-for-small"><a href="" class="social-link twitter">Twitter</a></li>
						<li class="hide-for-small"><a href="" class="social-link facebook">Facebook</a></li>
						<li class="hide-for-small"><a href="" class="social-link instagram">Instagram</a></li>
						<?php
						if (function_exists( 'icl_get_languages' )) :
							$languages = icl_get_languages('skip_missing=0&orderby=code');
							foreach($languages as $l): if($l['active']) continue; ?>
							<li><a ng-href="<?php echo ngwp_site_root_url_for_lang($l['language_code']); ?>{{document.locationPath}}" target="_self" href="<?php $l['url']; ?>">
								<?php echo substr( $l['native_name'], 0, 3 ); ?>
							</a></li>
						<?php endforeach; endif; ?>
					</ul>
					</section>
				</nav>
			</header>

			<div id="site-loader" ng-if="loading" class="fade-animation">
				<center>
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

			<div id="site-content" ng-view class="view-animation">
				<?php
				if (($path = ngwp_crawler_path()) !== null) {
					switch (ngwp_template_for_path($path)) {
						case 'home':
							get_template_part('/views/home');
							break;
						case 'page':
							get_template_part('/views/page');
							break;
						default: // post
							get_template_part('/views/post');
							break;
					}
				} ?>
			</div>

		</div><!-- #site -->

		<footer reveal-sheet-stack id="site-footer">
			<a reveal-sheet="reference" id="footer-map" href="<?php echo $ng_settings['footer_background_link']; ?>" target="_blank" style="background-image:url('<?php echo $ng_settings['footer_background_url']; ?>')"></a>
			<div reveal-sheet id="footer-content">
				<div id="footer-contacts"><?php echo $ng_settings['footer_content']; ?></div>
				<div id="footer-widgets" class="row">
					<div class="small-12 columns">
						<?php dynamic_sidebar( 'footer-1' ); ?>
					</div>
				</div>
			</div>
		</footer><!-- #site-footer -->

		<!-- Cleanup start -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
			siteUrl: "<?php echo site_url(); ?>",
			templateUrl: "<?php echo ngwp_get_localized_template_directory_uri(); ?>",
			routes: <?php echo ngwp_routes_object_json(); ?>,
			data: <?php echo ngwp_query_data_json(); ?>
		});
		</script>

		<?php wp_footer(); ?>
	</body>
</html>
