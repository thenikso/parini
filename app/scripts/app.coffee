'use strict'

angular.module('App', ['ngRoute', 'ngResource'])
	.config ($routeProvider, $locationProvider) ->
		$routeProvider.when '/',
			templateUrl: "#{wordpress.templateUrl}/views/home.html"
			controller: 'HomeCtrl'

		# Account for different languages
		if wordpress?.languages?
			for l in wordpress.languages
				$routeProvider.when "/#{l}",
					templateUrl: "#{wordpress.templateUrl}/views/home.html"
					controller: 'HomeCtrl'

		# Custom post types will have a $routeParams.postname parameter
		if wordpress?.rewriteRules?.postTypes?
			for k, postType of wordpress.rewriteRules.postTypes
				$routeProvider.when postType,
					templateUrl: "#{wordpress.templateUrl}/views/post.html"
					controller: 'PostCtrl'
				if wordpress?.languages?
					$routeProvider.when '/:lang' + postType,
						templateUrl: "#{wordpress.templateUrl}/views/post.html"
						controller: 'PostCtrl'

		# Post will have a $routeParams.postname parameter
		$routeProvider.when (wordpress?.rewriteRules?.post or '/post/:postname'),
			templateUrl: "#{wordpress.templateUrl}/views/post.html"
			controller: 'PostCtrl'

		if wordpress?.languages?
			$routeProvider.when '/:lang' + (wordpress?.rewriteRules?.post or '/post/:postname'),
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'

		# Pages will have a $routeParams.pagename parameter
		$routeProvider.when (wordpress?.rewriteRules?.page or '/:pagename/'),
			templateUrl: "#{wordpress.templateUrl}/views/page.html"
			controller: 'PageCtrl'

		if wordpress?.languages?
			$routeProvider.when '/:lang' + (wordpress?.rewriteRules?.page or '/:pagename/'),
				templateUrl: "#{wordpress.templateUrl}/views/page.html"
				controller: 'PageCtrl'

		# Fallback
		$routeProvider.otherwise
			redirectTo: '/'

		# Setup HTML5 push state
		$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$ ->
	$(document).foundation()