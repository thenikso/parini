'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q, wordpress) ->
	siteUrl = wordpress.siteUrl or ''

	# API resources
	RecentPosts = $resource "#{siteUrl}/:lang/api/get_recent_posts", lang: null
	Posts = $resource "#{siteUrl}/:lang/api/get_posts", lang: null
	Post = $resource "#{siteUrl}/:lang/api/get_post", lang: null
	Page = $resource "#{siteUrl}/:lang/api/get_page", lang: null

	# Prepare single post
	preparePost = (post) ->
		if post?.date?
			d = new Date(post.date)
			if isNaN(d) and d = dateRegExp.exec(post.date)
				d = d[1..].map (i) -> parseInt i, 10
				d = new Date Date.UTC(d...)
			post.date = d
		post

	# Preparing data
	preparePostData = (data) ->
		data.post = preparePost data.post
		data

	preparePageData = (data) ->
		data.page = preparePost data.page
		data

	preparePostsData = (data, Api, opts) ->
		count = data.count
		# Prepare every single post
		data.posts = data.posts.map(preparePost)
		# Add facility methods to retrieve more posts
		data.currentPage = 1
		data.hasMore = ->
			data.count * data.currentPage < data.count_total
		data.isLoadingMore = no
		data.loadMore = ->
			return if data.isLoadingMore or not data.hasMore()
			data.isLoadingMore = yes
			more = Api.get angular.extend({
				page: data.currentPage + 1
				count: count
			}, opts), ->
				return unless more?.posts?
				data.posts = data.posts.concat more.posts.map(preparePost)
				data.currentPage += 1
				data.isLoadingMore = no
		data

	# Utility function to generate get APIs with prepare step
	getPreparedGetFactory = (Api, prepare) -> (opts, callback) ->
		res = Api.get opts, (data, resp) ->
			prepare(res, Api, opts)
			callback?(data, resp)

	# Utility function to generate promises methods
	getPromiseFactory = (Api, prepare, check=->yes) -> (opts) ->
		deferred = $q.defer()
		if (data = wordpress.data)? and check(data, opts)
			wordpress.data = null
			deferred.resolve prepare(data, Api, opts)
		else
			Api.get opts
			, (data) ->
				if check(data, opts)
					deferred.resolve prepare(data, Api, opts)
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

		getRecentPosts: getPreparedGetFactory(RecentPosts, preparePostsData)
		getPosts: getPreparedGetFactory(Posts, preparePostsData)
		getPost: getPreparedGetFactory(Post, preparePostData)
		getPage: getPreparedGetFactory(Page, preparePageData)

		getRecentPostsPromise: getPromiseFactory RecentPosts, preparePostsData
		getPostsPromise: getPromiseFactory Posts, preparePostsData
		getPostPromise: getPromiseFactory Post, preparePostData, (data, opts) -> data.post?.slug is opts.slug
		getPagePromise: getPromiseFactory Page, preparePageData, (data, opts) -> data.page?.slug is opts.slug
	}
wordpressApi.$inject = ['$resource', '$q', 'wordpress']

dateRegExp = /^(\d{4})\-(\d\d)\-(\d\d)(?:\s+(\d\d):(\d\d):(\d\d))?$/
dateRegExp.compile(dateRegExp)

angular.module('App').service 'wordpressApi', wordpressApi
