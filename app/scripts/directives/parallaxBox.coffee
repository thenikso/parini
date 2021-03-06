'use strict'

angular.module('WordpressApp')
	.directive 'parallaxBox', ->
		restrict: 'AC'
		controller: ($element) ->
			@element = $element
			@

	.directive 'parallaxMultiplier', ->
		restrict: 'AC'
		require: '^parallaxBox'
		link: (scope, element, attrs, controller) ->
			return if Modernizr?.touch
			parallaxItem =
				parent: controller.element
				element: element
				multiplier: parseFloat(attrs.parallaxMultiplier)
			parallaxItems.push parallaxItem
			scope.$on '$destroy', ->
				removeParallaxItem parallaxItem
			setTimeout updateParallax

parallaxItems = []
removeParallaxItem = (parallaxItem) ->
	parallaxItems = parallaxItems.filter (i) -> i != parallaxItem

$window = $(window)
unless Modernizr?.csstransforms3d
	updateParallax = ->
		for item in parallaxItems
			translateY = Math.floor(($window.scrollTop() - item.parent.offset().top)*item.multiplier)
			item.element.css 'margin-top', "#{translateY}px"
else
	updateParallax = ->
		for item in parallaxItems
			translateY = Math.floor(($window.scrollTop() - item.parent.offset().top)*item.multiplier)
			item.element.css
				'-webkit-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-mox-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-o-transform': "translate3d(0px, #{translateY}px, 0px)"
				'-ms-transform': "translateY(#{translateY}px)"
				'transform': "translate3d(0px, #{translateY}px, 0px)"


$window.scroll updateParallax