'use strict'

angular.module('WordpressApp')
	.controller 'SearchCtrl', ($scope, wordpressData, $routeParams) ->
		$scope.data = wordpressData ? {}
		$scope.searchQuery = $routeParams.search
		$scope.body.classes = ['search']
