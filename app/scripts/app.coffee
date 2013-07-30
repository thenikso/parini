'use strict'

angular.module('WordpressApp', ['ngRoute', 'ngResource'])
	.config ($routeProvider, $locationProvider, wordpress) ->
		$routeProvider.when '/',
			templateUrl: "#{wordpress.templateUrl}/views/home.php"
			controller: 'HomeCtrl'
			resolve:
				wordpressData: (wordpressApi) ->
					wordpressApi.getRecentPostsPromise {}

		# Account for different languages
		if wordpress.language?.others? then for l in wordpress.language.others
			$routeProvider.when "/#{l}",
				templateUrl: "#{wordpress.templateUrl}/views/home.php"
				controller: 'HomeCtrl'
				resolve:
					wordpressData: (wordpressApi) ->
						wordpressApi.getRecentPostsPromise
							lang: l

		# Custom post types will have a $routeParams.postname parameter
		# The post will be injected as `post`
		if wordpress.rewriteRules?.postTypes?
			for postType, postTypePermastruct of wordpress.rewriteRules.postTypes
				$routeProvider.when postTypePermastruct,
					templateUrl: "#{wordpress.templateUrl}/views/post.html"
					controller: 'PostCtrl'
					resolve:
						wordpressData: ($route, wordpressApi) ->
							wordpressApi.getPostPromise
								post_type: postType
								slug: $route.current.params.postname
				if wordpress.language?.others? then for l in wordpress.language.others
					$routeProvider.when "/#{l}#{postTypePermastruct}",
						templateUrl: "#{wordpress.templateUrl}/views/post.html"
						controller: 'PostCtrl'
						resolve:
							wordpressData: ($route, wordpressApi) ->
								wordpressApi.getPostPromise
									lang: l
									post_type: postType
									slug: $route.current.params.postname

		# Post will have a $routeParams.postname parameter
		$routeProvider.when (wordpress.rewriteRules?.post or '/post/:postname'),
			templateUrl: "#{wordpress.templateUrl}/views/post.html"
			controller: 'PostCtrl'
			resolve:
				wordpressData: ($route, wordpressApi) ->
					wordpressApi.getPostPromise
						slug: $route.current.params.postname

		if wordpress.language?.others? then for l in wordpress.language.others
			$routeProvider.when "/#{l}" + (wordpress.rewriteRules?.post or '/post/:postname'),
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'
				resolve:
					wordpressData: ($route, wordpressApi) ->
						wordpressApi.getPostPromise
							lang: l
							slug: $route.current.params.postname

		# Pages will have a $routeParams.pagename parameter
		if wordpress.language?.others? then for l in wordpress.language.others
			$routeProvider.when "/#{l}" + (wordpress.rewriteRules?.page or '/:pagename/'),
				templateUrl: "#{wordpress.templateUrl}/views/page.html"
				controller: 'PageCtrl'
				resolve:
					wordpressData: ($route, wordpressApi) ->
						wordpressApi.getPagePromise
							lang: l
							slug: $route.current.params.pagename

		$routeProvider.when (wordpress.rewriteRules?.page or '/:pagename/'),
			templateUrl: "#{wordpress.templateUrl}/views/page.html"
			controller: 'PageCtrl'
			resolve:
				wordpressData: ($route, wordpressApi) ->
					wordpressApi.getPagePromise
						slug: $route.current.params.pagename

		# Fallback
		$routeProvider.otherwise
			redirectTo: '/'

		# Setup HTML5 push state
		$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$(document).foundation('topbar')
