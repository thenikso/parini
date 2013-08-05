'use strict'

app = angular.module('WordpressApp')
app.controller 'HomeCtrl', ($scope, wordpressData) ->
	$scope.body.classes = ['home']
	$scope.data = wordpressData
