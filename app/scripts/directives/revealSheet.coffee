'use strict'

angular.module('WordpressApp')
	.directive 'revealSheetStack', ->
		restrict: 'EAC'
		controller: ($element) ->
			@stackElement = $element
			@height = 0
			@sheets = []
			@referenceSheet = null

			@addSheet = (sheetElement, attrs) ->
				@sheets.push sheetElement
				@referenceSheet = sheetElement if attrs.revealSheet is 'reference'
				if @referenceSheet?
					@height = @referenceSheet.height() # [0].offsetHeight to remove jQuery dep
				else
					@height += sheetElement.height()
				@stackElement.css
					height: "#{@height}px"
				sheetElement.css
					position: 'fixed'
					width: '100%'
					bottom: 0
					left: 0

			@removeSheet = (sheetElement) ->
				@referenceSheet = null if @referenceSheet is sheetElement
				@sheets = @sheets.filter (s) -> s isnt sheetElement
				unless @referenceSheet?
					@height = 0
					for s in @sheets
						@height += s.height()
					@stackElement.css
						height: "#{@height}px"

			@scroll = =>
				# TODO remove jQuery dependency
				scrollOffset = -(@stackElement.offset().top - $(window).scrollTop() - $(window).height())
				return unless scrollOffset > 0
				for s, i in @sheets
					continue if s is @referenceSheet
					if (scrollOffset -= s.height()) >= 0
						s.css
							position: 'relative'
					else
						s.css
							position: 'fixed'
			@
		link: (scope, element, attrs, controller) ->
			return if Modernizr?.touch
			angular.element(window).bind 'scroll resize', controller.scroll
			scope.$on '$destroy', ->
				angular.element(window).unbind 'scroll resize', controller.scroll

	.directive 'revealSheet', ->
		restrict: 'A'
		require: '^revealSheetStack'
		link: (scope, element, attrs, controller) ->
			return if Modernizr?.touch
			controller.addSheet element, attrs
			scope.$on '$destroy', ->
				controller.removeSheet element, attrs
