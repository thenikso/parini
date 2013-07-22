'use strict'

angular.module('App')
	.controller 'HomeCtrl', ($scope, wordpressData) ->
		$scope.body.classes = ['home']
		$scope.posts = wordpressData.posts
