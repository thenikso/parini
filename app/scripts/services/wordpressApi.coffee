'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q) ->
	# API resources
	Posts = $resource '/:lang/api/get_posts', lang: null
	Post = $resource '/:lang/api/get_post', lang: null
	Page = $resource '/:lang/api/get_page', lang: null

	# Utility function to generate promises methods
	getPromiseFactory = (Api, dataTrans, check=->yes) -> (opts) ->
		deferred = $q.defer()
		if (data = wordpress?.data)? and (data = dataTrans(data))? and check(data, opts)
			console.log 'cached'
			wordpress.data = null
			deferred.resolve data
		else
			console.log 'fetched'
			Api.get opts
			, (data) ->
				if (data = dataTrans(data))?
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

		getPostsPromise: getPromiseFactory Posts, ((data) -> data?.posts)
		getPostPromise: getPromiseFactory Post, ((data) -> data?.post), ((post, opts) -> post.slug is opts.slug)
		getPagePromise: getPromiseFactory Page, ((data) -> data?.page), ((page, opts) -> page.slug is opts.slug)
	}
wordpressApi.$inject = ['$resource', '$q']


angular.module('App').service 'wordpressApi', wordpressApi
