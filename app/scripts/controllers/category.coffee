'use strict'

app = angular.module('WordpressApp')
app.controller 'CategoryCtrl', ($scope, wordpressData) ->
	$scope.data = wordpressData
	$scope.body.classes = [
		'category'
		"category-#{$scope.data.category.slug}"
		"category-#{$scope.data.category.id}"
	]
