'use strict'

angular.module('App')
	.controller 'PageCtrl', ($scope, wordpressData) ->
		$scope.data = wordpressData
		console.log $scope.data

