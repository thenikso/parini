'use strict'

angular.module('WordpressApp')
	.controller 'PostCtrl', ($scope, wordpressData) ->
		$scope.data = wordpressData
		$scope.body.classes = [
			'single'
			"type-${$scope.data.post.type}"
			"post-#{$scope.data.post.id}"
			"post-#{$scope.data.post.slug}"
		]
		console.log wordpressData
