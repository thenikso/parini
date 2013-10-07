# Quick directive to add a slideshow, currently using zurb orbit
angular.module('WordpressApp')
	.directive 'slideshow', ($timeout) ->
		restrict: 'EA'
		replace: yes
		transclude: yes
		scope: yes
		template: '
			<div class="horizontal-slideshow-wrapper">
				<ul class="horizontal-slideshow-list" ng-style="{width:(slideCount*100)+\'%\'}" ng-transclude></ul>
			</div>'
		link: (scope, element, attrs) ->
			$window = angular.element(window)
			$timeout ->
				items = element.find('li')
				scope.slideCount = items.length
				resizeItems = ->
					items.css 'width', $window.width()
				$window.bind 'resize', resizeItems
				do resizeItems
				scope.$on '$destroy', ->
					$window.unbind 'resize', resizeItems
