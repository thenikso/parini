'use strict'

angular.module('App')
	.controller 'PostCtrl', ($scope, wordpressData) ->
		$scope.data = wordpressData
		console.log wordpressData
