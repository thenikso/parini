<!doctype html>
<html ng-app="WordpressApp" class="no-js">
	<head>
		<?php ngwp_echo_base_tag(''); ?>

		<title ng-bind-template="<?php bloginfo( 'name' ); ?>{{document.title&&(' | '+document.title)}}"><?php wp_title( '|', true, 'right' ); ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href='http://fonts.googleapis.com/css?family=Tinos:400,400italic|Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>

		<?php wp_head(); ?>
	</head>
	<body ng-controller="MainCtrl" ng-class="body.classes">
		<?php
		global $ngwp_options;
		$ng_settings = get_option( 'ngwp_options', $ngwp_options );
		?>

		<!--[if lte IE 8]>
			<div class="chromeframe">
				<h1><?php echo($ng_settings['header_logo_content'] ? $ng_settings['header_logo_content'] : get_bloginfo( 'name' )); ?></h1>
				<p>Il tuo browser è troppo vecchio. <a href="http://browsehappy.com/">Aggiorna il tuo browser oggi</a> o <a href="http://www.google.com/chromeframe/?redirect=true">installa Google Chrome Frame</a> per navigare meglio questo sito.</p>
				<p>You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
			</div>
		<![endif]-->

		<div id="site">

			<header id="site-header">
				<nav class="top-bar" ng-class="{'expanded': document.topBarExpanded}">
					<ul class="title-area">
						<li class="name">
							<h1 id="site-title">
								<a href="<?php echo site_url(); ?>" rel="home">
									<?php echo($ng_settings['header_logo_content'] ? $ng_settings['header_logo_content'] : get_bloginfo( 'name' )); ?>
								</a>
							</h1>
						</li>
						<li class="toggle-topbar menu-icon"><a href="" ng-click="document.topBarExpanded = !document.topBarExpanded"><span></span></a></li>
					</ul>

					<section class="top-bar-section">
					<?php wp_nav_menu(array(
							'location' => 'primary',
							'container' => false,
							'menu_class' => 'wp-nav-menu primary-menu'
						));
					?>
					<ul id="menu-secondary" class="right">
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

			<div id="site-content" ng-view ng-cloak class="view-animation">
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
