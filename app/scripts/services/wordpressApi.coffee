'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q, wordpress) ->
	# API resources
	Posts = $resource '/:lang/api/get_posts', lang: null
	Post = $resource '/:lang/api/get_post', lang: null
	Page = $resource '/:lang/api/get_page', lang: null

	# Utility function to generate promises methods
	getPromiseFactory = (Api, check=->yes) -> (opts) ->
		deferred = $q.defer()
		if (data = wordpress.data)? and check(data, opts)
			console.log 'cached'
			wordpress.data = null
			deferred.resolve data
		else
			console.log 'fetched'
			Api.get opts
			, (data) ->
				if check(data, opts)
					deferred.resolve data
				else
					deferred.resolve null
			, ->
				deferred.resolve null
		return deferred.promise

	# Service interface
	return {
		getPosts : Posts.get
		getPost: Post.get
		getPage: Page.get

		getPostsPromise: getPromiseFactory Posts
		getPostPromise: getPromiseFactory Post, (data, opts) -> data.post?.slug is opts.slug
		getPagePromise: getPromiseFactory Page, (data, opts) -> data.page?.slug is opts.slug
	}
wordpressApi.$inject = ['$resource', '$q', 'wordpress']


angular.module('App').service 'wordpressApi', wordpressApi
