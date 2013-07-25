'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q, wordpress) ->
	# API resources
	RecentPosts = $resource '/:lang/api/get_recent_posts', lang: null
	Posts = $resource '/:lang/api/get_posts', lang: null
	Post = $resource '/:lang/api/get_post', lang: null
	Page = $resource '/:lang/api/get_page', lang: null

	preparePost = (post) ->
		if post?.date?
			d = new Date(post.date)
			if isNaN(d) and d = dateRegExp.exec(post.date)
				d = d[1..].map (i) -> parseInt i, 10
				d = new Date Date.UTC(d...)
			post.date = d
		post

	preparePostData = (data) ->
		data.post = preparePost data.post
		data

	preparePostsData = (data) ->
		data.posts = data.posts.map preparePost
		data

	# Utility function to generate promises methods
	getPromiseFactory = (Api, prepare, check=->yes) -> (opts) ->
		deferred = $q.defer()
		if (data = wordpress.data)? and check(data, opts)
			wordpress.data = null
			deferred.resolve prepare(data)
		else
			Api.get opts
			, (data) ->
				if check(data, opts)
					deferred.resolve prepare(data)
				else
					deferred.resolve null
			, ->
				deferred.resolve null
		return deferred.promise

	# Service interface
	return {
		preparePost: preparePost
		preparePostData: preparePostData
		preparePostsData: preparePostsData

		getRecentPosts: RecentPosts.get
		getPosts: Posts.get
		getPost: Post.get
		getPage: Page.get

		getRecentPostsPromise: getPromiseFactory RecentPosts, preparePostsData
		getPostsPromise: getPromiseFactory Posts, preparePostsData
		getPostPromise: getPromiseFactory Post, preparePostData, (data, opts) -> data.post?.slug is opts.slug
		getPagePromise: getPromiseFactory Page, preparePostData, (data, opts) -> data.page?.slug is opts.slug
	}
wordpressApi.$inject = ['$resource', '$q', 'wordpress']

dateRegExp = /^(\d{4})\-(\d\d)\-(\d\d)(?:\s+(\d\d):(\d\d):(\d\d))?$/
dateRegExp.compile(dateRegExp)

angular.module('App').service 'wordpressApi', wordpressApi
