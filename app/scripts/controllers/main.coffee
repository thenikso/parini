'use strict'

angular.module('App')
	.controller 'MainCtrl', ($scope, $location) ->
		# Loading control
		$scope.loading = yes
		$scope.$on '$routeChangeStart', ->
			$scope.loading = yes
		$scope.$on '$viewContentLoaded', ->
			$scope.loading = no
		# Menu functions
		$scope.siteMenu =
			activeClass: (url) ->
				'active' if $location.absUrl().indexOf(url) >= 0
