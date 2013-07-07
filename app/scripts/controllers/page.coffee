'use strict'

angular.module('App')
	.controller 'PageCtrl', ($scope, $routeParams, wordpressApi) ->
		$scope.loader.loading = yes
		$scope.data = wordpressApi.get_page {slug: $routeParams.pageSlug}, ->
			$scope.loader.loading = no
