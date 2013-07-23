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
				scheduleRelayout @masonry
			@appendBrick = (element, testImages=yes) ->
				@createMasonry()
				return if @masonry?.getItem(element.get(0))?
				if testImages and $?.fn?.imagesLoaded? and element.find('img').length
					element.imagesLoaded => @appendBrick element, no
					return
				@masonry?.appended element.get(0), yes
				scheduleRelayout @masonry
			@removeBrick = (element) ->
				@masonry?.remove element.get(0)
				scheduleRelayout @masonry
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
			element.addClass 'masonry-brick'
			controller.appendBrick element
			scope.$on '$destroy', ->
				controller.removeBrick element