'use strict'

angular.module('App')
	.directive 'masonry', ->
		restrict: 'EA'
		controller: ($scope, $element, $attrs) ->
			relayoutScheduled = no
			scheduleRelayout = (masonry) ->
				return if relayoutScheduled or not masonry?
				relayoutScheduled = yes
				setTimeout (->
					relayoutScheduled = no
					masonry.layout()), 0
			@masonry = null
			@createMasonry = ->
				return if @masonry?
				options = $scope.$eval $attrs.masonry
				options = angular.extend options or {}, {
					itemSelector: '.masonry-brick'
				}
				return unless $element.children().hasClass(options.itemSelector.substr(1))
				@masonry = new Masonry $element.get(0), options
			@appendBrick = (element, testImages=yes) =>
				if testImages and $?.fn?.imagesLoaded? and element.find('img').length
					element.imagesLoaded -> @appendBrick element, no
					return
				@masonry?.appended element.get(0)
				scheduleRelayout()
			@removeBrick = (element) ->
				@masonry?.remove element.get(0)
				scheduleRelayout()
			@destroy = ->
				@masonry?.destroy()
				@masonry = null
			@
		link: (scope, element, attrs, controller) ->
			controller.createMasonry()
			scope.$on '$destroy', controller.destroy

	.directive 'masonryBrick', ->
		restrict: 'AC'
		require: '^masonry'
		link: (scope, element, attrs, controller) ->
			controller.createMasonry()
			element.addClass 'masonry-brick'
			controller.appendBrick element
			scope.$on '$destroy', ->
				controller.removeBrick element