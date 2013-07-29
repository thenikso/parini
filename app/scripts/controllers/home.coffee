'use strict'

angular.module('App')
	.controller 'HomeCtrl', ($scope, wordpressData, wordpressApi) ->
		$scope.body.classes = ['home']
		$scope.data = wordpressData
