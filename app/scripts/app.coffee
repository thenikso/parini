'use strict'

app = angular.module('WordpressApp', ['ngRoute', 'ngResource', 'ngAnimate', 'ngSanitize'])
app.config ($routeProvider, $locationProvider, wordpress) ->

	# Helper function to generate routes settings using languages if present
	makeRouteHandler = (route, controller, viewName, apiPromiseMethodName, apiParams) ->
		if wordpress.language?.others? then for l in wordpress.language.others
			$routeProvider.when "/#{l}" + route,
				templateUrl: "#{wordpress.templateUrl}/views/#{viewName}.php"
				controller: controller
				resolve:
					wordpressData: ['wordpressApi', '$route', (wordpressApi, $route) ->
						wordpressApi[apiPromiseMethodName] angular.extend(apiParams?($route.current.params) ? apiParams, lang: l)]

		$routeProvider.when route,
			templateUrl: "#{wordpress.templateUrl}/views/#{viewName}.php"
			controller: controller
			resolve:
				wordpressData: ['wordpressApi', '$route', (wordpressApi, $route) ->
					wordpressApi[apiPromiseMethodName] (apiParams?($route.current.params) ? apiParams)]

	# Homepage
	makeRouteHandler '/', 'HomeCtrl', 'home', 'getRecentPostsPromise', {}

	# Custom post types. The post will be injected as `post`
	if wordpress.routes?.postTypes?
		for postType, postTypePermastruct of wordpress.routes.postTypes
			makeRouteHandler postTypePermastruct, 'PostCtrl', 'post', 'getPostPromise', do (postType) -> (routeParams) ->
				post_type: postType
				slug: routeParams.postname ? routeParams[postType]

	# Categories
	makeRouteHandler (wordpress.routes?.category or '/category/:category'), 'CategoryCtrl', 'category', 'getCategoryPostsPromise', (routeParams) ->
		slug: routeParams.category

	# Date archives
	makeRouteHandler (wordpress.routes?.date or '/archive/:year/:monthnum/:day'), 'DateCtrl', 'date', 'getDatePostsPromise', (routeParams) ->
		date: "#{routeParams.year}-#{routeParams.monthnum}-#{routeParams.dat}"

	# Search
	makeRouteHandler (wordpress.routes?.search or '/search/:search'), 'SearchCtrl', 'search', 'getSearchPostsPromise', (routeParams) ->
		search: routeParams.search

	# Single post
	makeRouteHandler (wordpress.routes?.post or '/post/:postname'), 'PostCtrl', 'post', 'getPostPromise', (routeParams) ->
		slug: routeParams.postname

	# Pages should be handled last as they may have a catch-all route
	makeRouteHandler (wordpress.routes?.page or '/:pagename/'), 'PageCtrl', 'page', 'getPagePromise', (routeParams) ->
		slug: routeParams.pagename

	# Fallback
	$routeProvider.otherwise
		redirectTo: '/'

	# Setup HTML5 push state
	$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$(document).foundation('topbar')
