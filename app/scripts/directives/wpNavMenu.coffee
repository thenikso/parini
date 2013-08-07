'use strict'

app = angular.module('WordpressApp')

app.directive 'wpNavMenu', ($location) ->
	restrict: 'AC'
	controller: ($scope, $attrs) ->
		@menuItems = []
		@activeClasses = $attrs.wpNavMenu or 'current-menu-item active';
		$scope.$on '$routeChangeSuccess', =>
			location = $location.absUrl()
			location += '/' if location[location.length-1] isnt '/'
			for i in @menuItems
				href = i.attr('href') or i.find('a').attr('href')
				href += '/' if href[href.length-1] isnt '/'
				if href is location
					i.addClass(@activeClasses)
				else
					i.removeClass(@activeClasses)
		@

app.directive 'menuItem', ->
	restrict: 'AC'
	require: '^wpNavMenu'
	link: (scope, element, attrs, controller) ->
		controller.menuItems.push element