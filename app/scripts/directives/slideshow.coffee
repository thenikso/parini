# Quick directive to add a slideshow, currently using zurb orbit
angular.module('WordpressApp')
	.directive 'slideshow', ($timeout) ->
		restrict: 'EA'
		replace: yes
		transclude: yes
		scope:
			centerForWidth: '@'
		template: '
			<div class="horizontal-slideshow-wrapper" ng-class="{ \'loading\':!loaded }">
				<ul class="horizontal-slideshow-list" ng-style="{ \'visibility\':(loaded&&\'visible\'||\'hidden\') }" ng-transclude></ul>
			</div>'
		link: (scope, element, attrs) -> $timeout ->
			scope.loaded = no
			list = element.find('ul')
			items = element.find('li')
			element.imagesLoaded ->
				width = 0
				width += i.offsetWidth for i in items
				list.css 'width', "#{width}px"
				scope.$apply -> scope.loaded = yes

			# Center for width currently hardcoded default to 960
			centerForWidth = scope.centerForWidth or 960
			if centerForWidth
				$window = angular.element(window)
				centerToWindow = ->
					margin = ($window.width() - centerForWidth) / 2
					margin = 0 if margin < 0
					list.css marginLeft: "#{margin}px"
				do centerToWindow
				$window.bind 'resize', centerToWindow
				scope.$on '$destroy', ->
					$window.unbind 'resize', centerToWindow
