'use strict'

angular.module('App')
	.controller 'MainCtrl', ($scope, $rootScope, $location, wordpress, wordpressApi) ->
		# Specify body classes
		$scope.body =
			classes: []

		# Setting document title
		$rootScope.document =
			title: ''

		# Language
		# The current language code is extrapolated from the current location path
		# as the first 2 letters of the path component.
		$scope.lang = null
		$scope.$on '$routeChangeSuccess', ->
			path = $location.path()
			if (lang = path.substr(1,2)) in wordpress.language?.others and (path.length is 3 or path.substr(3,1) is '/')
				$scope.lang = lang
			else
				$scope.lang = null

		# Loading animations control
		$rootScope.loading = yes
		$rootScope.$on '$routeChangeStart', ->
			$rootScope.loading = yes
		$rootScope.$on '$routeChangeSuccess', ->
			$rootScope.document.title = ''
		$rootScope.$on '$viewContentLoaded', ->
			$rootScope.loading = no

		# API access
		# This can be used in pages like so:
		# <div ng-init="myposts=load.posts()">{{myposts.status}}</div>
		$scope.load =
			posts: (opts) ->
				wordpressApi.getPosts angular.extend({ lang: $scope.lang }, opts)
			# TODO add other apis

	# Animation to give the appearance of the content to fall down
	.animation 'view-animation-leave', ->
		setup: (el) ->
			scrollTop = $(window).scrollTop()
			wrapper = el.wrapAll('<div class="view-animation-leave-wrapper"></div>').parent()
			# wrapper css is setup via styles
			wrapper.scrollTop scrollTop
			$(window).scrollTop(0)
			wrapper
		start: (el, done, wrapper) ->
			wrapper.animate top: $(window).height(),
				{
					duration: 1000
					easing: 'easeInOutExpo'
					done: ->
						wrapper.remove()
						done()
				}

$.easing['easeInOutExpo'] = (x, t, b, c, d) ->
		return b if t == 0
		return b + c if t == d
		return c/2 * Math.pow(2, 10 * (t - 1)) + b if (t/=d/2) < 1
		c/2 * (-Math.pow(2, -10 * --t) + 2) + b
