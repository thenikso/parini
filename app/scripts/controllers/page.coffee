'use strict'

angular.module('App')
	.controller 'PageCtrl', ($scope, wordpressData) ->
		$scope.data = wordpressData
		$scope.document.title = $scope.data.page?.title
		console.log $scope.data

