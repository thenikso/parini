'use strict'

angular.module('App', ['ngRoute', 'ngResource'])
	.config ($routeProvider, $locationProvider) ->
		$routeProvider
			.when '/',
				templateUrl: "#{wordpress.templateUrl}/views/home.html"
				controller: 'HomeCtrl'
			.when '/en',
				templateUrl: "#{wordpress.templateUrl}/views/home.html"
				controller: 'HomeCtrl'

			.when '/agenda/:postSlug',
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'
			.when '/:lang/agenda/:postSlug',
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'

			.when '/lavori/:postSlug',
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'
			.when '/:lang/lavori/:postSlug',
				templateUrl: "#{wordpress.templateUrl}/views/post.html"
				controller: 'PostCtrl'

			.when '/:pageSlug',
				templateUrl: "#{wordpress.templateUrl}/views/page.html"
				controller: 'PageCtrl'
			.when '/:lang/:pageSlug',
				templateUrl: "#{wordpress.templateUrl}/views/page.html"
				controller: 'PageCtrl'

			.otherwise
				redirectTo: '/'

		$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$ ->
	$(document).foundation()