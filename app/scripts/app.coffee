'use strict'

angular.module('WordpressApp', ['ngRoute', 'ngResource'])
	.config ($routeProvider, $locationProvider, wordpress) ->

		# Helper function to generate routes settings using languages if present
		makeRouteHandler = (route, controller, viewName, apiPromiseMethodName, apiParams) ->
			if wordpress.language?.others? then for l in wordpress.language.others
				$routeProvider.when "/#{l}" + route,
					templateUrl: "#{wordpress.templateUrl}/views/#{viewName}.php"
					controller: controller
					resolve:
						wordpressData: (wordpressApi, $route) ->
							wordpressApi[apiPromiseMethodName] angular.extend(apiParams?($route) ? apiParams, lang: l)

			$routeProvider.when route,
				templateUrl: "#{wordpress.templateUrl}/views/#{viewName}.php"
				controller: controller
				resolve:
					wordpressData: (wordpressApi, $route) ->
						wordpressApi[apiPromiseMethodName] (apiParams?($route) ? apiParams)

		# Homepage
		makeRouteHandler '/', 'HomeCtrl', 'home', 'getRecentPostsPromise', {}

		# Custom post types. The post will be injected as `post`
		if wordpress.routes?.postTypes?
			for postType, postTypePermastruct of wordpress.routes.postTypes
				makeRouteHandler postTypePermastruct, 'PostCtrl', 'post', 'getPostPromise', do (postType) -> (route) ->
					post_type: postType
					slug: route.current.params.postname

		# Single post
		makeRouteHandler (wordpress.routes?.post or '/post/:postname'), 'PostCtrl', 'post', 'getPostPromise', (route) ->
			slug: route.current.params.postname

		# Pages should be handled last as they may have a catch-all route
		makeRouteHandler (wordpress.routes?.page or '/:pagename/'), 'PageCtrl', 'page', 'getPagePromise', (route) ->
			slug: route.current.params.pagename

		# Fallback
		$routeProvider.otherwise
			redirectTo: '/'

		# Setup HTML5 push state
		$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$(document).foundation('topbar')
