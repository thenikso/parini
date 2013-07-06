'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q) ->
	Posts = $resource '/:lang/api/get_posts',
		{
			lang: null
		}
	Post = $resource '/:lang/api/get_post',
		{
			lang: null
			slug: '@post.slug'
		}
	Page = $resource '/:lang/api/get_page',
		{
			lang: null
			slug: '@page.slug'
		}
	return {
		get_posts : (opts, callback) ->
			Posts.get opts, callback
		get_post: (opts, callback) ->
			data = {}
			if (data = wordpress?.data)? and data.post?.slug is opts.slug
				deferred = $q.defer()
				wordpress.data = null
				deferred.promise.then -> callback data, null
				deferred.resolve data
			else
				data = Post.get opts, (data, headers) ->
					# TODO check status
					callback data, headers
			data
		get_page: (opts, callback) ->
			Page.get opts, callback
	}
wordpressApi.$inject = ['$resource', '$q']

angular.module('App').service 'wordpressApi', wordpressApi
