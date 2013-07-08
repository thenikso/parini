'use strict'

angular.module('App')
	.controller 'MainCtrl', ($scope, $rootScope, $location, wordpress) ->
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

		# Loading control
		$rootScope.loading = yes
		$rootScope.$on '$routeChangeStart', ->
			$rootScope.loading = yes
		$rootScope.$on '$viewContentLoaded', ->
			$rootScope.loading = no

	.animation 'view-animation-leave', ->
		setup: (el) ->
			scrollTop = $(document).scrollTop()
			wrapper = el.wrapAll('<div class="view-animation-leave-wrapper"></div>').parent()
			# wrapper css is setup via styles
			wrapper.scrollTop scrollTop
			wrapper
		start: (el, done, wrapper) ->
			wrapper.animate top: $(document).height(),
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
