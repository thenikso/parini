'use strict'

app = angular.module('WordpressApp')
app.controller 'DateCtrl', ($scope, wordpressData, $routeParams) ->
	$scope.data = wordpressData
	$scope.date = {}
	$scope.date.year = $routeParams.year if $routeParams.year?
	$scope.date.month = $routeParams.monthnum if $routeParams.monthnum?
	$scope.date.day = $routeParams.day if $routeParams.day?
	$scope.body.classes = [
		'archive'
		"archive-year-#{$scope.date.year or 'none'}"
		"archive-month-#{$scope.date.month or 'none'}"
		"archive-day-#{$scope.date.day or 'none'}"
	]
