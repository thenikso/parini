'use strict'

angular.module('App')
	.controller 'MainCtrl', ($scope) ->
		# Loading control
		$scope.loading = yes
		$scope.$on '$routeChangeStart', ->
			$scope.loading = yes
		$scope.$on '$viewContentLoaded', ->
			$scope.loading = no
