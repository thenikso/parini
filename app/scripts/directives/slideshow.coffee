# Quick directive to add a slideshow, currently using zurb orbit
angular.module('WordpressApp')
	.directive 'slideshow', ->
		restrict: 'EA'
		replace: yes
		transclude: yes
		template: '
			<div class="slideshow-wrapper">
				<div class="preloader"></div>
				<ul data-orbit ng-transclude></ul>
			</div>'
		link: (scope, element, attrs) ->
			setTimeout ->
				$(document).foundation 'orbit', {
					animation: 'slide'
					bullets: no
					timer: no
					timer_speed: 5000
					variable_height: yes
				}
