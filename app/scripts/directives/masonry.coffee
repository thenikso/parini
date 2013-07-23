'use strict'

angular.module('App')
	.directive 'masonry', ($q) ->
		restrict: 'EA'
		controller: ($scope, $element, $attrs) ->
			@relayoutScheduled = no
			@scheduleRelayout = ->
				return if @relayoutScheduled or not @masonry?
				@relayoutScheduled = yes
				setTimeout (=>
					@relayoutScheduled = no
					do @masonry.layout), 0
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
			@appendBrick = (element) ->
				@createMasonry()
				return if @masonry?.getItem(element.get(0))?
				@onImagesLoaded element, =>
					@masonry?.appended element.get(0), yes
					do @scheduleRelayout
			@removeBrick = (element) ->
				@masonry?.remove element.get(0)
				do @scheduleRelayout
			@destroy = ->
				@masonry?.destroy()
				@masonry = null
			@onImagesLoaded = (element, callback) ->
				deferred = $q.defer()
				deferred.promise.then callback
				images = []
				callback.image = new Image
				callback.image.onload = callback.image.onerror = ->
					if images.length
						checkImages()
					else
						deferred.resolve()
				checkImages = ->
					callback.image.src = images.pop()
				if element.prop('tagName') is 'IMG'
					images.push element.attr('src')
				else for i in element.children().find('img')
					images.push i.attr('src')
				if images.length
					checkImages()
				else
					deferred.resolve()
				deferred.promise
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

	.directive 'masonryLayoutOn', ->
		priority: -1
		restrict: 'A'
		require: '^masonry'
		link: (scope, element, attrs, controller) ->
			scope.$watch attrs.masonryLayoutOn, ->
				controller.onImagesLoaded element, ->
					controller.scheduleRelayout()

