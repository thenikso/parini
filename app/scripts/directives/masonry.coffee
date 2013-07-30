'use strict'

angular.module('WordpressApp')
	.directive 'masonry', ->
		restrict: 'EA'
		controller: ($scope, $element, $attrs) ->
			@relayoutScheduled = no
			@scheduleRelayout = ->
				return if @relayoutScheduled or not @masonry?
				@relayoutScheduled = yes
				setTimeout (=>
					@relayoutScheduled = no
					do @masonry?.layout), 0
			@masonry = null
			@createMasonry = ->
				return if @masonry?
				options = $scope.$eval $attrs.masonry
				options = angular.extend options or {}, {
					itemSelector: '.masonry-brick'
				}
				return unless $element.children().hasClass(options.itemSelector.substr(1))
				@masonry = new Masonry $element.get(0), options
				do @scheduleRelayout
			@appendBrick = (element, testImages=yes) ->
				@createMasonry()
				return if @masonry?.getItem(element.get(0))?
				if testImages and $?.fn?.imagesLoaded? and element.find('img').length
					element.imagesLoaded => @appendBrick element, no
					return
				@masonry?.appended element.get(0), yes
				do @scheduleRelayout
			@removeBrick = (element) ->
				return @destroy() if @masonry?.getItemElements().length <= 1
				@masonry?.remove element.get(0)
				do @scheduleRelayout
			@destroy = ->
				@masonry?.destroy()
				@masonry = null
			@
		link: (scope, element, attrs, controller) ->
			controller.createMasonry()
			if $?.fn?.imagesLoaded?
				element.imagesLoaded()
			scope.$on '$destroy', ->
				do controller.destroy

	.directive 'masonryBrick', ->
		restrict: 'AC'
		require: '^masonry'
		link: (scope, element, attrs, controller) ->
			element.addClass 'masonry-brick'
			controller.appendBrick element
			scope.$on '$destroy', ->
				controller.removeBrick element

	.directive 'masonryLayoutOn', ->
		priority: -1
		restrict: 'A'
		require: '^masonry'
		link: (scope, element, attrs, controller) ->
			scope.$watch attrs.masonryLayoutOn, ->
				if $?.fn?.imagesLoaded? and (element.find('img').length or element.prop('tagName') is 'IMG')
					element.imagesLoaded ->
						do controller.scheduleRelayout
					return
				do controller.scheduleRelayout
