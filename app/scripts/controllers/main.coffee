'use strict'

angular.module('App')
	.controller 'MainCtrl', ($scope, $location, wordpress) ->
		# Language
		urlRegExp = /^([^#]*?:\/\/.*?)(\/.*)?$/
		urlRegExp.compile?(urlRegExp)
		$scope.lang =
			code: null
			href: (ref) ->
				return ref unless $scope.lang.code
				return "#{r[1]}/#{$scope.lang.code}#{r[2] ? ''}" if r = urlRegExp.exec(ref)
				"/#{$scope.lang.code}#{ref}"
		$scope.$on '$routeChangeSuccess', ->
			if (lang = $location.path().substr(1,2)) in wordpress.language?.others
				$scope.lang.code = lang
			else
				$scope.lang.code = null

		# Loading control
		$scope.loader = {}
		$scope.loader.loading = yes
		$scope.$on '$routeChangeStart', ->
			$scope.loader.loading = yes
		$scope.$on '$viewContentLoaded', ->
			$scope.loader.loading = no

		# Menu functions
		$scope.siteMenu =
			activeClass: (url) ->
				'active' if $location.absUrl().indexOf(url) >= 0

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
