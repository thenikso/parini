'use strict'

app = angular.module('WordpressApp')
app.controller 'SearchCtrl', ($scope, wordpressData, $routeParams) ->
	$scope.data = wordpressData ? {}
	$scope.searchQuery = $routeParams.search
	$scope.body.classes = ['search']
