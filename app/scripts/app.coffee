'use strict'

angular.module('App', ['ngRoute'])
	.config ($routeProvider, $locationProvider) ->
		$routeProvider
			.when '/',
				templateUrl: "#{wordpress.templateUrl}/views/main.html"
				controller: 'MainCtrl'
			.otherwise
				redirectTo: '/'

		$locationProvider.html5Mode(yes).hashPrefix('!')

# Run foundation
$(document).foundation()