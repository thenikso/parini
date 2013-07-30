'use strict'

angular.module('WordpressApp')
	.controller 'HomeCtrl', ($scope, wordpressData, wordpressApi) ->
		$scope.body.classes = ['home']
		$scope.data = wordpressData
