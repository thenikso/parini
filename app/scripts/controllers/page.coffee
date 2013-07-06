'use strict'

angular.module('App')
	.controller 'PageCtrl', ($scope, $routeParams, wordpressApi) ->
		$scope.loading = yes
		$scope.data = wordpressApi.get_page {slug: $routeParams.pageSlug}, ->
			$scope.loading = no
