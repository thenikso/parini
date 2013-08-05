'use strict';

# AngularJS will instantiate a singleton by calling "new" on this function
wordpressApi = ($resource, $q, $sce, wordpress) ->
	siteUrl = wordpress.siteUrl or ''

	# API resources

	# Page
	# Requires one of:
	# - `id` or `page_id` - set to the page's ID
	# - `slug` or `page_slug` - set to the page's URL slug
	# Optional:
	# `children` - set to a non-empty value to include a recursive hierarchy of child pages
	# `post_type` - used to retrieve custom post types
	Page = $resource "#{siteUrl}/:lang/api/get_page", lang: null

	# Post
	# Requires one of:
	# - `id` or `post_id` - set to the post's ID
	# - `slug` or `post_slug` - set to the post's URL slug
	# Optional:
	# `post_type` - used to retrieve custom post types
	Post = $resource "#{siteUrl}/:lang/api/get_post", lang: null

	# Posts
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	Posts = $resource "#{siteUrl}/:lang/api/get_posts", lang: null

	# RecentPosts
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	RecentPosts = $resource "#{siteUrl}/:lang/api/get_recent_posts", lang: null

	# DatePosts
	# Requires one of:
	# - `date` - set to a date in the format `YYYY` or `YYYY-MM` or `YYYY-MM-DD`
	#   (non-numeric characters are stripped from the var, so `YYYYMMDD` or `YYYY/MM/DD` are also valid)
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	DatePosts = $resource "#{siteUrl}/:lang/api/get_date_posts", lang: null

	# CategoryPosts
	# Requires one of:
	# - `id` or `category_id` - set to the category's ID
	# - `slug` or `category_slug` - set to the category's URL slug
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	CategoryPosts = $resource "#{siteUrl}/:lang/api/get_category_posts", lang: null

	# TagPosts
	# Required one of:
	# - `id` or `tag_id` - set to the tag's ID
	# - `slug` or `tag_slug` - set to the tag's URL slug
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	TagPosts = $resource "#{siteUrl}/:lang/api/get_tag_posts", lang: null

	# AuthorPosts
	# Required one of:
	# - `id` or `author_id` - set to the author's ID
	# - `slug` or `author_slug` - set to the author's URL slug
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	AuthorPosts = $resource "#{siteUrl}/:lang/api/get_author_posts", lang: null

	# SearchPosts
	# Required one of:
	# - `search` - set to the desired search query
	# Optional:
	# `count` - determines how many posts per page are returned (default value is 10)
	# `page` - return a specific page number from the results
	# `post_type` - used to retrieve custom post types
	SearchPosts = $resource "#{siteUrl}/:lang/api/get_search_results", lang: null

	# Prepare single post
	preparePost = (post) ->
		if post?.date?
			d = new Date(post.date)
			if isNaN(d) and d = dateRegExp.exec(post.date)
				d = d[1..].map (i) -> parseInt i, 10
				d = new Date Date.UTC(d...)
			post.date = d
		post.content = $sce.trustAsHtml(post.content) if post?.content?
		post

	# Preparing data
	prepareSingleData = (data) ->
		data.post = preparePost data.post
		data

	preparePageData = (data) ->
		data.page = preparePost data.page
		# TODO prepare child pages if present
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
		getPage: getPreparedGetFactory Page, preparePageData
		getPost: getPreparedGetFactory Post, prepareSingleData
		getPosts: getPreparedGetFactory Posts, preparePostsData
		getRecentPosts: getPreparedGetFactory RecentPosts, preparePostsData
		getDatePosts: getPreparedGetFactory DatePosts, preparePostsData
		getCategoryPosts: getPreparedGetFactory CategoryPosts, preparePostsData
		getTagPosts: getPreparedGetFactory TagPosts, preparePostsData
		getAuthorPosts: getPreparedGetFactory AuthorPosts, preparePostsData
		getSearchPosts: getPreparedGetFactory SearchPosts, preparePostsData

		getPagePromise: getPromiseFactory Page, preparePageData, (data, opts) -> data.page?.slug is opts.slug
		getPostPromise: getPromiseFactory Post, prepareSingleData, (data, opts) -> data.post?.slug is opts.slug
		getPostsPromise: getPromiseFactory Posts, preparePostsData
		getRecentPostsPromise: getPromiseFactory RecentPosts, preparePostsData
		getDatePostsPromise: getPromiseFactory DatePosts, preparePostsData
		getCategoryPostsPromise: getPromiseFactory CategoryPosts, preparePostsData
		getTagPostsPromise: getPromiseFactory TagPosts, preparePostsData
		getAuthorPostsPromise: getPromiseFactory AuthorPosts, preparePostsData
		getSearchPostsPromise: getPromiseFactory SearchPosts, preparePostsData
	}
wordpressApi.$inject = ['$resource', '$q', '$sce', 'wordpress']

dateRegExp = /^(\d{4})\-(\d\d)\-(\d\d)(?:\s+(\d\d):(\d\d):(\d\d))?$/
dateRegExp.compile(dateRegExp)

angular.module('WordpressApp').service 'wordpressApi', wordpressApi
