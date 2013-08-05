'use strict'

app = angular.module('WordpressApp')
app.controller 'PageCtrl', ($scope, wordpressData) ->
	$scope.data = wordpressData ? {}
	$scope.body.classes = [
		'page'
		"page-#{$scope.data.page?.slug}"
		"page-#{$scope.data.page?.id}"
	]
	$scope.document.title = $scope.data.page?.title

	# Custom posts for included post wall
	if $scope.data.page?.custom_fields?.ngwpPageWallType?[0]
		# Set wall type
		$scope.wall =
			type: $scope.data.page?.custom_fields?.ngwpPageWallType?[0]
		# Select appropriate slug
		slug = null
		switch $scope.data.page.custom_fields.ngwpPageWallPostsType?[0]
			when 'category' then slug = $scope.data.page.custom_fields.ngwpPageWallCategory?[0]
			when 'author' then slug = $scope.data.page.custom_fields.ngwpPageWallAuthor?[0]
			when 'tag' then slug = $scope.data.page.custom_fields.ngwpPageWallTag?[0]
		# Fetch posts
		api = 'posts'
		if $scope.data.page.custom_fields.ngwpPageWallPosts?[0]
			api = $scope.data.page.custom_fields.ngwpPageWallPosts[0] + 'Posts'
		$scope.load[api]?({
			count: $scope.data.page.custom_fields.ngwpPageWallCount?[0]
			post_type: $scope.data.page.custom_fields.ngwpPageWallPostsType?[0]
			date: $scope.data.page.custom_fields.ngwpPageWallDate?[0]
			slug: slug
			search: slug = $scope.data.page.custom_fields.ngwpPageWallSearch?[0]
		}, (data) -> $scope.wall.data = data)
