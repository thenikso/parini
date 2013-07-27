'use strict'

angular.module('App')
	.controller 'HomeCtrl', ($scope, wordpressData, wordpressApi) ->
		$scope.body.classes = ['home']

		count = wordpressData.count
		$scope.data = wordpressData
		$scope.data.page = 1
		$scope.data.hasMore = ->
			$scope.data.count * $scope.data.page < $scope.data.count_total
		$scope.data.isLoadingMore = no
		$scope.data.loadMore = ->
			return if $scope.data.isLoadingMore or not $scope.data.hasMore()
			$scope.data.isLoadingMore = yes
			more = wordpressApi.getRecentPosts {
				lang: $scope.lang
				page: $scope.data.page + 1
				count: count
			}, ->
				return unless more?.posts?
				$scope.data.posts = $scope.data.posts.concat more.posts
				$scope.data.page += 1
				$scope.data.isLoadingMore = no
