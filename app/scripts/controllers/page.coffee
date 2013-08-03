'use strict'

angular.module('WordpressApp')
	.controller 'PageCtrl', ($scope, wordpressData) ->
		$scope.data = wordpressData
		$scope.document.title = $scope.data.page?.title
