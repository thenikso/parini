'use strict'

angular.module('App')
	.controller 'PostCtrl', ($scope, wordpressData) ->
		$scope.post = wordpressData.post
		console.log wordpressData
