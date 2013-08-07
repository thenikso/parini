'use strict'

app = angular.module('WordpressApp')
app.controller 'MainCtrl', ($scope, $rootScope, $location, wordpress, wordpressApi) ->
	# Expose wordpress settings
	$scope.wordpress = wordpress

	# Specify body classes
	$scope.body =
		classes: []

	# Setting document title
	$rootScope.document =
		title: ''
		locationPath: ''

	# Loading animations control
	$rootScope.loading = yes
	$rootScope.$on '$routeChangeStart', ->
		$rootScope.loading = yes
	$rootScope.$on '$routeChangeSuccess', ->
		$rootScope.document.title = ''
		$rootScope.document.locationPath = $location.path()
	$rootScope.$on '$viewContentLoaded', ->
		$rootScope.loading = no unless secondaryLoadInProgress

	# API access
	# This can be used in pages like so:
	# <div ng-init="myposts=load.posts()">{{myposts.status}}</div>
	secondaryLoadInProgress = no
	loadFactory = (wpApi) -> (opts, callback) ->
		$rootScope.loading = secondaryLoadInProgress = yes
		wordpressApi[wpApi] angular.extend({ lang: $scope.lang }, opts), (data, resp) ->
			$rootScope.loading = secondaryLoadInProgress = no
			callback?(data, resp)
	$scope.load =
		posts: loadFactory 'getPosts'
		categoryPosts: loadFactory 'getCategoryPosts'
		datePosts: loadFactory 'getDatePosts'
		searchPosts: loadFactory 'getSearchPosts'
		# TODO add other apis

# Animation to give the appearance of the content to fall down
app.animation '.view-animation', ->
	leave: (el, done) ->
		scrollTop = $(window).scrollTop()
		wrapper = el.wrapAll('<div class="view-animation-wrapper"></div>').parent()
		# wrapper css is setup via styles
		wrapper.scrollTop scrollTop
		$(window).scrollTop(0)
		if Modernizr?.csstransforms3d
			translateY = $(window).height()
			wrapper.css
				'-webkit-transition': 'all 1s ease-in-out'
				'-moz-transition': 'all 1s ease-in-out'
				'-o-transition': 'all 1s ease-in-out'
				'transition': 'all 1s ease-in-out'
				'-webkit-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-mox-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-o-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-ms-transform': "translateY(#{translateY}px)"
				'transform': "translate3d(0px, #{translateY}px, 0px)"
			setTimeout (->
				wrapper.remove()
				done()), 1000
		else
			wrapper.animate top: $(window).height(),
				{
					duration: 1000
					easing: 'easeInOutExpo'
					done: ->
						wrapper.remove()
						done()
				}
		null

$.easing['easeInOutExpo'] = (x, t, b, c, d) ->
	return b if t == 0
	return b + c if t == d
	return c/2 * Math.pow(2, 10 * (t - 1)) + b if (t/=d/2) < 1
	c/2 * (-Math.pow(2, -10 * --t) + 2) + b
