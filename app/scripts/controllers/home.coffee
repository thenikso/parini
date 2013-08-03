'use strict'

angular.module('WordpressApp')
	.controller 'HomeCtrl', ($scope, wordpressData) ->
		$scope.body.classes = ['home']
		$scope.data = wordpressData
