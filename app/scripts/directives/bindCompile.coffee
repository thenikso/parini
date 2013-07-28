'use strict'

angular.module('App')
	.directive 'bindCompile', ($compile) ->
		scope: yes
		link: (scope, element, attrs) ->
			scope.$watch attrs.bindCompile, (template) ->
				return unless angular.isDefined(template)
				element.html('').append $compile(template)(scope)
